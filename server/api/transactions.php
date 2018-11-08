<?php

/**
 * Transactions API.
 *
 * @version 1.0.0
 *
 * @api
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/Api.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/Account.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/Transaction.php';
$api = new Api('json', ['GET', 'PUT']);
switch ($api->method) {
    case 'GET':
        //returns a specific transaction or all transactions
        if (!$api->checkAuth()) {
            //User not authentified/authorized
            return;
        }
        if (!$api->checkParameterExists('aid', $aid)) {
            require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/User.php';
            $user = new User($api->requesterId);
            if ($api->checkParameterExists('pattern', $pattern)) {
                //return user transactions matching pattern
                $pattern = str_replace('*', '%', $pattern);
                if (false === $transactionsList = $user->getTransactionsByPattern($pattern, $errorMessage)) {
                    $api->output(500, 'Error during transaction query'.$errorMessage);
                    //something gone wrong :(
                    return;
                }
            } else {
                //return all user transactions
                $transactionsList = $user->getTransactions();
            }
            $transactions = array();
            foreach ($transactionsList as $transaction) {
                array_push($transactions, $transaction->structureData());
            }
            $api->output(200, $transactions);
            //return transactions list
            return;
        }
        //check requestor is the account owner
        $account = new Account($aid);
        $account->get();
        if ($account->user !== $api->requesterId) {
            $api->output(403, 'Transactions can be queried by account owner only');
            //indicate the requester is not the account owner and is not allowed to query it
            return;
        }
        if ($api->checkParameterExists('id', $id) && $id !== '') {
            //request for a specific transaction
            $transaction = new Transaction($id);
            if (!$transaction->get()) {
                $api->output(404, 'Transaction not found');
                //indicate the account was not found
                return;
            }
            $api->output(200, $transaction->structureData());
            //return requested transaction
            return;
        }
        //Request all transactions of the account
        $transactionsList = $account->getTransactions();
        $transactions = array();
        foreach ($transactionsList as $transaction) {
            array_push($transactions, $transaction->structureData());
        }
        $api->output(200, $transactions);
        //return transactions list
        return;
        break;
    case 'PUT':
        //update operation
        if (!$api->checkAuth()) {
            //User not authentified/authorized
            return;
        }
        $api->checkParameterExists('aid', $aid);
        if (!$api->checkParameterExists('id', $id) || $id === '') {
            $api->output(400, 'Transaction identifier must be provided');
            //Transaction was not provided, return an error
            return;
        }
        $transaction = new Transaction($id);
        if (!$transaction->get()) {
            $api->output(404, 'Transaction not found');
            //indicate the transaction was not found
            return;
        }
        //check requestor is the account owner
        if ($aid && $aid !== $transaction->aid) {
            $api->output(400, 'Transaction is not valid: inconsistent account ');
            //provided transaction is not valid
            return;
        }
        if (!$aid) {
            $aid = $transaction->aid;
        }
        $account = new Account($aid);
        $account->get();
        if ($account->user !== $api->requesterId) {
            $api->output(403, 'Transaction can be updated by its owner only');
            //indicate the requester is not the account owner and is not allowed to update it
            return;
        }
        $updatedTransaction = $api->query['body'];
        if (!$transaction->validateModel($updatedTransaction, $errorMessage)) {
            $api->output(400, 'Transaction is not valid: '.$errorMessage);
            //provided transaction is not valid
            return;
        }
        if (!$transaction->update($errorMessage)) {
            $api->output(500, 'Error during transaction update'.$errorMessage);
            //something gone wrong :(
            return;
        }
        $transaction->get();
        $api->output(200, $transaction->structureData());
        //return transaction
        return;
        break;
}
