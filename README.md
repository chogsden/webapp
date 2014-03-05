WebApp
======

An MVC Web Application framework.

******  DRAFT  *******

You need a web server, MySQL database and PHP (including php command line modules) installed.

To get this up and running (I know, I need to write documentation - it's on the todo list!) do the following:

1). download to the root directory of  web server;
2). Create a MySQL database (best to set character encoding to UTF-8)
3). update app/config/global.php:
- in the config array, enter the root directory of the app
- enter the settings to connect to a MySQL database into the db_config array - server IP address/localhost, username, password, database name. Note user must have read/write/create/drop privileges
3) Make sure all directories/files in the app can be written to by PHP, including .htaccess (this is likely to be ok as default)
4). go to a browser window and enter http://server_address/app_root_directory (e.g. http;//localhost/webapp/

If all goes well you should see a home page for the app.

To easily add new sections into the MVC structure:
1). open a command line prompt and change directory to the app root directory
2). enter php _system/section.php create section_name (where section_name is whatever you want to call your new section)

You should see a response that says the new section has been successfully created, followed by a procedure report. Otherwise the opposite - create section failed and a report of reversed items setting the App back to how it was before you tried to create the section.
You can delete sections by entering php _system/section.php delete new_section. Be warned, this will remove all section files and delete the database table - this action is irreversible!

Reloading the browser home page should now show a new section, that displays content text from its respective database table.

This is as far as automation goes… now the App can be developed by editing the Model, View, Controller files, adding new fields into the database, altering main.css stylesheet (which needs reducing - on the todo list) etc...

To set a menu item as a list rather than a single link to a page, go to app/config/routes.json. In the same directory is routes.txt that details how to do this.

