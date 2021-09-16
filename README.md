# ToDoList

Symfony application which need to be upgraded, tested and documented.  
Work carried out as part of the training course "Application Developer - PHP / Symfony" on OpenClassrooms.  
![GitHub last commit (branch)](https://img.shields.io/github/last-commit/ElodieBichet/ToDoList/main)  

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/ca0c448bb3ec4fdf914f8bcfb32fd140)](https://www.codacy.com/gh/ElodieBichet/ToDoList/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ElodieBichet/ToDoList&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/ca0c448bb3ec4fdf914f8bcfb32fd140)](https://www.codacy.com/gh/ElodieBichet/ToDoList/dashboard?utm_source=github.com&utm_medium=referral&utm_content=ElodieBichet/ToDoList&utm_campaign=Badge_Coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/25d0677cc6c31404df95/maintainability)](https://codeclimate.com/github/ElodieBichet/ToDoList/maintainability)

## Table of Contents
1.  __[Prerequisite and technologies](#prerequisite-and-technologies)__
  * [Server](#server)
  * [Framework and libraries](#framework-and-libraries)
2.  __[Installation](#installation)__
  * [Download or clone](#download-or-clone)
  * [Configure environment variables](#configure-environment-variables)
  * [Install the project](#install-the-project)
  * [Create the database](#create-the-database)
3.  __[Tests](#tests)__
  * [Configure PHP Unit](#configure-php-unit)
  * [Run the tests](#run-the-tests)
4. __[Contribution](#contribution)__

---
## PREREQUISITE AND TECHNOLOGIES

### __Server__
You need a web server with PHP7 (> 7.1.3) and MySQL.  
Versions used in this project:
* Apache 2.4.46
* PHP 7.3.21
* MySQL 5.7.31

See more information on technical requirements in the [Symfony official documentation](https://symfony.com/doc/4.4/setup.html#technical-requirements).

### __Framework and libraries__
Framework: __Symfony ^4.4.29__  
Dependencies manager: __Composer ^1.11.99.2__  

To run tests, you also need __PHPUnit__. See requirements in [PHPUnit documentation - EN](https://phpunit.readthedocs.io/en/latest/installation.html#requirements) / [FR](https://phpunit.readthedocs.io/fr/latest/installation.html#pre-requis)  

---
## INSTALLATION

### __Download or clone__
Download zip files or clone the project repository with github ([see GitHub documentation](https://docs.github.com/en/github/creating-cloning-and-archiving-repositories/cloning-a-repository)).

### __Configure environment variables__
You need to configure at least these lines in your environment file:
```env
...
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```

### __Install the project__
1.  If needed, install __Composer__ by following [the official instructions](https://getcomposer.org/download/).
2.  In your cmd, go to the directory where you want to install the project and install dependencies with composer:
```
$ cd some\directory
$ composer install
```
Dependencies should be installed in your project (check _vendor_ directory).  

### __Create the database__
If you are in a dev environment, you can create the database and fill it with fake contents with the following command:
```
$ composer prepare-database-dev
```

__Alternatively__, follow the following steps:
1. If the database does not exist, create it with the following command in the project directory:
```
$ php bin/console doctrine:database:create
```
2. Create database structure thanks to migrations:
```
$ php bin/console doctrine:migrations:migrate
```
3. Install fixtures to have fake contents:
```
$ php bin/console doctrine:fixtures:load
```
Your database should be updated with fake tasks and users.

  ![DB Model](UML/Database-Model.png)

---
## TESTS

### __Configure PHP Unit__
If you want to change PHP Unit configuration, use phpunit.xml.dist file and rename it phpunit.xml.  
You must define DATABASE_URL environment variable for tests. By default, tests use SQLite:
```xml
<env name="DATABASE_URL" value="sqlite:///%kernel.cache_dir%/test.db" />
```
### __Run the tests__
To run all tests, use the following command:
```
$ ./vendor/bin/phpunit
```
See more details and options about command-line test runner in [PHP Unit documentation - EN](https://phpunit.readthedocs.io/en/latest/textui.html) / [FR](https://phpunit.readthedocs.io/fr/latest/textui.html).

---
## CONTRIBUTION

See [Contributing file](CONTRIB.md).