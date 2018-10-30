# Own my money!

Own my money is a simple way to track your bank activity on any device connected to the web (pc, smartphone, tablet, ...).
It can work as a standalone application or in your smartphone browser.

You can manage yours accounts with OFX or JSON integration.

## Installation and usage

Following example is based on Linux distribution with Apache :

0. Install prerequisite:
  - Web server: Apache HTTP server / Nginx
  - Database server: MariaDB / MySQL
  - PHP
1. Download the latest version [here](https://github.com/nioc/own-my-money/archive/master.tar.gz)
2. Untar the archive : `tar -xvzf own-my-money-master.tar.gz`
3. Move the files into you web server directory `mv own-my-money-master /var/www/money`
4. Fix file permissions `chown www-data:www-data /var/www/money -R`
5. Configure your web server
6. Create database stuff, user, etc... using with the [database setup page](http://localhost/#/setup)
7. Remove unnecessary files (/docs, ...)

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/nioc/own-my-money/tags).

## Authors

* **[Nioc](https://github.com/nioc/)** - *Initial work*

See also the list of [contributors](https://github.com/nioc/own-my-money/contributors) who participated in this project.

## Motivation

Our aim is to provide a self-hosted manager for personal finances.

## Contributing

The project is open and any contribution is welcome!

#### Backend part (PHP)

To keep the code clean, we use [php-cs-fixer](http://cs.sensiolabs.org/), before commit launch this on each edited files:

```` bash
php /usr/local/bin/php-cs-fixer fix /path/to/editedFile.php -v
````
You can handle all edited files with this single line:
```` bash
cd /var/www/money; for file in $(git diff-index --name-only HEAD); do php /usr/local/bin/php-cs-fixer fix "$file" -v; done
````

#### Frontend part (VueJS)

In order to contribute to the VueJS frontend:

0. Install prerequisite:
  - [Node.js](https://nodejs.org/)
  - npm `npm install npm@latest -g`
  - Vue.js `npm install -g vue,`
  - Vue-cli `npm install -g vue-cli,`
1. Access the frontend folder in a shell `cd /var/www/money/money-front-vue`
2. Build the project `npm install` and wait for the downloads
3. Start the node server `npm run dev`
4. Edit the code!

#### A little how-to for Github

1. [Fork it](https://help.github.com/articles/fork-a-repo/)
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes (with a detailled message): `git commit -am 'Add an awesome feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/dcddd6d1c1284ea496b9a1015e775b2d)](https://www.codacy.com/app/nioc/own-my-money)

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE.md) file for details

## Included project

This project includes the following:
- [VueJS](https://vuejs.org/)
- [Bulma](https://bulma.io/)
- [Buefy](https://buefy.github.io)
- [Font Awesome](https://github.com/FortAwesome/Font-Awesome/)
- [Vue-resource](https://github.com/pagekit/vue-resource)
- [VeeValidate](https://github.com/logaretm/vee-validate)
- [Vue-moment](https://github.com/brockpetrie/vue-moment)
- [accounting.js](https://github.com/openexchangerates/accounting.js)
- [OFX Parser](https://github.com/asgrim/ofxparser)
- [UAParser.js](https://github.com/faisalman/ua-parser-js)
- Favicon: [Piggy Bank by Musmellow from the Noun Project](https://thenounproject.com/term/piggy-bank/1616637) (licensed as Creative Commons CCBY)
