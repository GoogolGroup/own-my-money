<?php

/**
 * User definition.
 *
 * @version 1.0.0
 *
 * @internal
 */
class User
{
    /**
     * @var int User internal identifier
     */
    public $id;
    /**
     * @var string User login
     */
    public $login;
    /**
     * @var string Encrypted user password
     */
    public $password;
    /**
     * @var boolean User status
     */
    public $status;
    /**
     * @var array User scope
     */
    public $scope;

    /**
     * Initializes a User object with his identifier.
     *
     * @param int $id User identifier
     */
    public function __construct($id = null)
    {
        if ($id !== null) {
            $this->id = intval($id);
        }
    }

    /**
     * Return all users.
     *
     * @return array Users
     */
    public static function getAll()
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/DatabaseConnection.php';
        $connection = new DatabaseConnection();
        $query = $connection->prepare('SELECT * FROM `user`;');
        if ($query->execute()) {
            //return array of users
            return $query->fetchAll(PDO::FETCH_CLASS, 'User');
        }
        return [];
    }

    /**
     * Create user.
     *
     * @param string $error The returned error message
     *
     * @return bool True if the user is created
     */
    public function insert(&$error)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/DatabaseConnection.php';
        $connection = new DatabaseConnection();
        $query = $connection->prepare('INSERT INTO `user` (`login`, `password`) VALUES ( :login, :password);');
        $query->bindValue(':login', $this->login, PDO::PARAM_STR);
        $query->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        if ($query->execute() && $query->rowCount() > 0) {
            $this->id = $connection->lastInsertId();
            //return true to indicate a successful user creation
            return true;
        }
        $error = $query->errorInfo()[2];
        //try to return intelligible error
        if ($query->errorInfo()[1] === 1062 || $query->errorInfo()[2] === 'UNIQUE constraint failed: user.login') {
            $error = 'login `'.$this->login.'` already exists';
        }
        //return false to indicate an error occurred while creating user
        return false;
    }

    /**
     * Populate user profile by querying on his identifier.
     *
     * @return bool True if the user is retrieved
     */
    public function get()
    {
        if (is_int($this->id)) {
            require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/DatabaseConnection.php';
            $connection = new DatabaseConnection();
            $query = $connection->prepare('SELECT * FROM `user` WHERE `id`=:id LIMIT 1;');
            $query->bindValue(':id', $this->id, PDO::PARAM_INT);
            if ($query->execute()) {
                $query->setFetchMode(PDO::FETCH_INTO, $this);
                //return true if there is user fetched, false otherwise
                return (bool) $query->fetch();
            }
        }
        //return false to indicate an error occurred while reading the user
        return false;
    }

    /**
     * Check credentials and populate user if they are valid.
     *
     * @param string $login    User login
     * @param string $password User password
     *
     * @return bool True if the user credentials are valid, false otherwise
     */
    public function checkCredentials($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
        require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/DatabaseConnection.php';
        $connection = new DatabaseConnection();
        $query = $connection->prepare('SELECT * FROM `user` WHERE `login`=:login AND `status`=1 LIMIT 1;');
        $query->bindValue(':login', $this->login, PDO::PARAM_STR);
        $query->setFetchMode(PDO::FETCH_INTO, $this);
        if ($query->execute() && $query->fetch()) {
            //return if password does match or not
            return password_verify($password, $this->password);
        }
        //return false to indicate an error occurred while reading the user
        return false;
    }

    /**
     * Update user (login, scope and status).
     *
     * @param string $error The returned error message
     *
     * @return bool True if the user is updated
     */
    public function update(&$error)
    {
        $error = '';
        if (is_int($this->id)) {
            require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/DatabaseConnection.php';
            $connection = new DatabaseConnection();
            $query = $connection->prepare('UPDATE `user` SET `login`=:login, `scope`=:scope, `status`=:status WHERE `id`=:id;');
            $query->bindValue(':id', $this->id, PDO::PARAM_INT);
            $query->bindValue(':login', $this->login, PDO::PARAM_STR);
            $query->bindValue(':scope', $this->scope, PDO::PARAM_STR);
            $query->bindValue(':status', $this->status, PDO::PARAM_BOOL);
            if ($query->execute()) {
                //return true to indicate a successful user update
                return true;
            }
            $error = ' '.$query->errorInfo()[2];
            //try to return intelligible error
            if ($query->errorInfo()[1] === 1062 || $query->errorInfo()[2] === 'UNIQUE constraint failed: user.login') {
                $error = ' : login `'.$this->login.'` already exists';
            }
        }
        //return false to indicate an error occurred while updating the user
        return false;
    }

    /**
     * Update user password.
     *
     * @param string $error The returned error message
     *
     * @return bool True if the user password is updated
     */
    public function updatePassword(&$error)
    {
        $error = '';
        if (is_int($this->id)) {
            require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/DatabaseConnection.php';
            $connection = new DatabaseConnection();
            $query = $connection->prepare('UPDATE `user` SET `password`=:password WHERE `id`=:id;');
            $query->bindValue(':id', $this->id, PDO::PARAM_INT);
            $query->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            if ($query->execute()) {
                //return true to indicate a successful user update
                return true;
            }
            $error = ' '.$query->errorInfo()[2];
        }
        //return false to indicate an error occurred while updating the user
        return false;
    }

    /**
     * Return all user accounts.
     *
     * @return array User accounts
     */
    public function getAccounts()
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/DatabaseConnection.php';
        $connection = new DatabaseConnection();
        $query = $connection->prepare('SELECT * FROM `account` WHERE `user`=:user;');
        $query->bindValue(':user', $this->id, PDO::PARAM_STR);
        if ($query->execute()) {
            //return array of accounts
            return $query->fetchAll(PDO::FETCH_CLASS, 'Account');
        }
        //indicate there is a problem during querying
        return false;
    }

    /**
     * Return public profile.
     *
     * @return object A public version of user profile
     */
    public function getProfile()
    {
        $user = new stdClass();
        $user->sub = (int) $this->id;
        $user->login = $this->login;
        $user->status = (bool) $this->status;
        //get user scope as a list of space-delimited strings (see https://tools.ietf.org/html/rfc6749#section-3.3)
        $user->scope = $this->scope;
        //returns the user public profile
        return $user;
    }

    /**
     * Add account from OFX file.
     *
     * @param BankAccount $bankAccount Account read from OFX file
     *
     * @return array Operation result
     */
    public function insertAccountFromDataset($bankAccount)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/server/lib/Transaction.php';
        $result = [];
        $result['accountInserted'] = 0;
        $result['accountprocessed'] = 0;

        $account = new Account();
        if (isset($bankAccount->routingNumber, $bankAccount->agencyNumber, $bankAccount->accountNumber)) {
            $result['accountprocessed']++;
            if (!$account->getByPublicId($this->id, $bankAccount->routingNumber, $bankAccount->agencyNumber, $bankAccount->accountNumber)) {
                //account does not exists, creating it
                $account->user = $this->id;
                $account->bankId = $bankAccount->routingNumber;
                $account->branchId = $bankAccount->agencyNumber;
                $account->accountId = $bankAccount->accountNumber;
                $account->balance = floatval($bankAccount->balance);
                $account->lastUpdate = time();
                if ($account->insert()) {
                    $result['accountInserted']++;
                }
            }
            if ($account->user === $this->id) {
                //account is ok, now working on transactions
                $transactionsResult = $account->insertTransactionsFromDataset($bankAccount->statement->transactions);
                $result = array_merge($result, $transactionsResult);
            }
        }
        return $result;
    }
}
