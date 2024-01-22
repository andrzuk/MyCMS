# MyCMS
## Full featured Content Management System application. 
### Features:
* Language: PHP
* Architecture: MVC framework
* Database: MySQL
* DB interface: mysqli
* User Interface: HTML, CSS, JS
* Admin Panel
* Users login and register
* Contact form and map
* Built-in form validators, admin list generators etc.
* App installer
* Bots and contact form spam auto detect and lock
* Locking statistics: charts and report
* On-line: http://fast-cms.pl
### Installation steps:
* create database
** `CREATE DATABASE cms;`
* prepare database
** `CREATE USER 'user-name'@'localhost' IDENTIFIED BY 'secret-user-password';`
** `GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT ON cms.* TO 'user-name'@'localhost' WITH GRANT OPTION;`
* customize databse connection: config/config.php
* upload project files to HTTP server (for example http(s)://your-comain.com)
* change attributes for following folders
** `chmod 777 -R css gallery install js layout`
* open URL: `http(s)://your-comain.com/install`
* submit installation form
* remove or rename `/install` folder on HTTP server (if needed)
