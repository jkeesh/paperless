# Paperless

This is the paperless project for the cs198 program at Stanford. 
The goal is to create a web interface to make code commenting 
easy, and to avoid paper submission copies. It's almost 2011!

### Issue Tracker

We do the issue tracking on Pivotal Tracker. You can see the issues,
or add issues here:

[[https://www.pivotaltracker.com/projects/291663]]

### Local Configuration Info


### Configure the Database

Download MAMP.
Visit localhost:8888/MAMP
Click phpMyAdmin
Create a new database called 'paperless'
Get the sql copy of the database with relevant tables
Click import and select the sql file

### Configure the Code

    $ cd /Applications/MAMP/htdocs
    $ git clone git://github.com/jkeesh/paperless.git
    $ cd paperless
    $ ./setup

### Configure the Submissions

Download a few submissions folders for testing.

If you are going to work with local submissions files for testing, 
make sure you put them in a directory called submission_files

For example, for me it should look like this:
    submission_files/cs106a/submissions/jkeeshin/(assn)/(student)/(code)

### Run

Visit localhost:8888/paperless


### More Detailed

To avoid changes with git commits, the configuration information
is not tracked by the git repository. However, there are two
files called config_local.php and config_web.php which show the 
contents of the configuration files for the web and your local 
machine. To get it to run locally copy the config_local.php file
into a file called config.php.

    cp config_local.php config.php

There are two .htaccess files, one for local and one for web.

    cp .htaccess_local .htaccess 

to use the local version of the .htaccess file.

That is what is in setup
