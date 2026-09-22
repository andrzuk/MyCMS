# MyCMS
## Full featured Content Management System application. 
### Features:
* Language: PHP
* Architecture: MVC framework
* Database: MySQL
* DB interface: mysqli
* User Interface: HTML, CSS, JS
* Admin Panel
* Access Control based on user's privileges stored in database
* Users login and register
* Contact form and map
* Built-in form validators, admin list generators etc.
* App installer
* Bots, massive attackers and contact form spam auto detect and lock mechanism
* Locking statistics: charts and reports
* URL of extended implementation (with additional features): http://in-met.com.pl
### Installation steps:
* create database:
 `CREATE DATABASE cms;`
* create user:
 `CREATE USER 'user-name'@'localhost' IDENTIFIED BY 'secret-user-password';`
* grant privileges:
 `GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT ON cms.* TO 'user-name'@'localhost' WITH GRANT OPTION;`
* customize databse connection: config/config.php
* upload project files to HTTP server (for example http(s)://your-domain.com)
* change attributes for following folders
 `chmod 777 -R css gallery install js layout`
* open URL: `http(s)://your-domain.com/install`
* submit installation form
* remove or rename `/install` folder on HTTP server (if needed)
