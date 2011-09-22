# Paperless

This is the paperless project for the cs198 program at Stanford. 
The goal is to create a web interface to make code commenting 
easy, and to avoid paper submission copies.

Paperless has been used by CS106A, CS106B, CS106X, CS106L, CS109L, and CS143 at Stanford.

============================================================

### How to Get Involved

We'd love to have you contribute to paperless, and we are running it as an open source project. 

1) Tell us you want to get involved, and set up paperless locally and well send you a testing db. Email jkeeshin@stanford.edu

2) Join the project management site Trello, and we'll add you to our board.

https://trello.com/board/paperless/4e71a616f8fe40db50566e3a

-- Assign yourself to any of the items there, or create a new one and move the card to the "Doing" list.

3) Fork off your own version of Paperless

Quick explanation on forking: http://help.github.com/fork-a-repo/

4) Make your awesome improvements

-- Note: If you are making what you think is a decent sized change, you should create a new branch for your feature. Here's some info on branching: http://learn.github.com/p/branching.html

5) Submit a pull request, and well bring your changes into the main branch.

Quick reference on pull requests: http://help.github.com/send-pull-requests/

============================================================

### Project Management and Issue Tracker

We do the project organization on Trello, the coolest site ever.

https://trello.com/board/paperless/4e71a616f8fe40db50566e3a

### Local Configuration Info

### Configure the Database

- Download MAMP. (or WAMP or LAMP depending on OS)
- Visit localhost:8888/MAMP
- Click phpMyAdmin
- Create a new database called 'paperless'
- Get the sql copy of the database with relevant tables
- Click import and select the sql file

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

To work on both the local and live server, the config file
is not tracked by the git repository. However, there are two
files called config_local.php and config_web.php which show the 
contents of the configuration files for the web and your local 
machine. To get it to run locally copy the config_local.php file
into a file called config.php.

    cp config_local.php config.php

There are two .htaccess files, one for local and one for web.

    cp .htaccess_local .htaccess 

to use the local version of the .htaccess file.

That is all that is in setup.
