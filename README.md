# A Basic Development Environment for a USB Pen Drive
These instructions explain how to run a basic portable development environment on a USB pen drive. The development environment consists of
- PHP 8.2
- Composer
- SQLite (a simple relational database)
- Adminer

To get started, download this repository:
- Click on the green code button
- Select Download zip

Move the zipped folder onto your USB drive.

Unzip the resulting file into the **root** of your USB drive. It might take a bit of time.

> What do I mean by the root of the USB? When you select your USB e.g. _D:_  there should be a number of different folders, a README file and a file called **start.bat** immediately visible. **You shouldn't have to click into any other folders first!**. 

Double click on start.bat

A command prompt (terminal) will open it will say something like:-

```
Adminer available at http://localhost:8081/adminer.php
Working in: D:\www
PHP 8.2.35 (cli) (built: Oct 26 2026 09:53:26) (ZTS Visual C++ 2019 x64)
Copyright (c) The PHP Group
Zend Engine v4.2.35, Copyright (c) Zend Technologies
v24.11.1
```
- A second command prompt will open. Leave this open, you need it for Adminer

The command prompt will already be in the _www_ folder, this is where you will create your web applications.


## Creating a Laravel App
In the command prompt enter the following composer command 

```
composer create-project laravel/laravel assignment "^12.0"
```

It will take a bit of time for Composer to download and install the packages needed for Laravel. 

Be patient, wait for it to finish.

Then navigate to your Laravel project folder

```
cd assignment
```

In the terminal enter the following:

```
php artisan serve
```

Your web app should start on http://localhost:8000/

## Accessing the SQLite Database

SQLite is a simple relational database management system (RDBMS). Unlike systems such as MySQL or PostgreSQL, it doesn't require a separate database server to be installed or running.

Instead, the entire database is stored in a single file on your computer (usually with a .sqlite or .db extension).

If we specify `DB_CONNECTION=sqlite` in our _.env_ file, Laravel will automatically create a database.sqlite file for us in _database/database.sqlite_. 

We need to tell Adminer where to find this file. 

Visit:-

http://localhost:8081/adminer.php

Enter the following details to access the database

- username: (leave this blank)
- password: **secret** (I set this up in advance)
- database: **../www/assignment/database/database.sqlite** (the path to Laravel's .sqlite file)

You should be able to log in and see the tables Laravel has set up e.g. _users_

At this point you are set-up and can build your assignment project.

## Important
- It is your responsibility to look after your USB stick.
- Back-up your work regularly. It's very easy to leave it in a machine by accident, lose it, damage it, so make sure you back-up regularly.
