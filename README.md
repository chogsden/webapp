WebApp
======

******  DRAFT  *******

WebApp is a lightweight MVC Web Application framework for developers. It integrates a responsive interface using the Twitter Bootstrap code-base. Basic site sections cam easily be generated via the command line from which further development can then commence. It was originally developed for a museum online exhibition, but could be used for any site that conforms to a responsive approach.

The code-base is very much in its early stages of developemnt, and as such is relatively untested on different platforms. Please report any bugs experienced and contributions/thoughts welcome via the github development wiki.

Requirements:
Web server
MySQL database
PHP installed (including php command line modules).

Setup:

1). Clone to the root directory of a web server;
2). Create a MySQL database - set character encoding to UTF-8;
3). Update app/config/global.php:
- in the config array, enter the name of the root directory of the app
- in the db_config array, enter the settings to connect to a MySQL database - server IP address, username, password, database name. Note user must have read/write/create/drop privileges;
4). Make sure all directories/files in the app can be written to by PHP, including .htaccess;
5). Go to a browser window and enter http://server_address/app_root_directory (e.g. http;//localhost/webapp/

If all goes well you should see a home page for the app.

To easily add new sections into the MVC structure:
1). Open a command line prompt and change directory to the app root directory
2). Enter php _system/section.php create section_name (where section_name is whatever you want to call your new section)

You should see a response that says the new section has been successfully created, followed by a procedure report. Otherwise the opposite - create section failed and a report of reversed items setting the App back to how it was before you tried to create the section.
You can delete sections by entering php _system/section.php delete new_section. Be warned, this will remove all section files and delete the database table - this action is irreversible!

Reloading the browser home page should now show a new section, that displays content text from its respective database table.

This is as far as automation goes… now the App can be developed by editing the Model, View, Controller files, adding new fields into the database, altering main.css stylesheet (which needs reducing - on the todo list) etc...

To set a menu item as a list rather than a single link to a page, go to app/config/routes.txt for instructions. In the same directory is routes.json that can be edited to do this.

This code-base carries no warranty, by using this code the user assumes all liabilities and responsibility for their use of the contents of this repository.