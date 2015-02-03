WebApp
======

[ DRAFT ]


**** ABOUT ****

WebApp is a lightweight MVC Web Application framework for developers. It integrates a responsive interface using the Twitter Bootstrap code-base. Basic site sections cam easily be generated via the command line from which further development can then commence. It was originally developed for a museum online exhibition, but could be used for any site that conforms to a responsive approach.

The code-base is very much in its early stages of developemnt, and as such is relatively untested on different platforms. Please report any bugs experienced and contributions/thoughts welcome via the github development wiki.


**** REQUIREMENTS ****

Web server
Optional MySQL database
PHP installed (including php command line modules).


**** INSTALLATION ****

1). Clone to the root directory of a web server;
2). If wanting to use MySQL for content then Create a MySQL database - set character encoding to UTF-8;
3). Update app/config/global.php:
- in the config array, enter the name of the root directory of the app
- if usig MySQL ther update db_config array, entering the settings to connect to a MySQL database - server IP address, username, password, database name. Note user must have read/write/create/drop privileges;
4). Make sure all directories/files in the app can be written to by PHP, including .htaccess;
5). Go to a browser window and enter http://server_address/app_root_directory (e.g. http;//localhost/webapp/

If all goes well you should see a home page for the app.

To easily add new sections into the MVC structure:
1). Open a command line prompt and change directory to the app root directory
2). Enter php _system/section.php create section_name (where section_name is whatever you want to call your new section)
	[If MySQL has been configured, then this action will create the Model to connect to a corresponding table in the database. Otherwise, the Model will contain static content.]

You should see a response that says the new section has been successfully created, followed by a procedure report. Otherwise the opposite - create section failed and a report of reversed items setting the App back to how it was before you tried to create the section.
You can delete sections by entering php _system/section.php delete new_section. Be warned, this will remove all section files and delete the database table if created - this action is irreversible!

Reloading the browser home page should now show a new section, that displays content text from its respective database table or from app/model/section_name.php if no database has been set up.

This is as far as automation goes… for now the App can be further developed by editing the Model, View, Controller files, adding new fields into the database, altering custom-styles.css stylesheet etc...


**** CUSTOMISATION ****


==== NAVIGATION BAR ITEMS ====

To set a menu item as a list rather than a single link to a page, go to app/config/routes.txt for instructions. In the same directory is routes.json that can be edited to do this.


==== DATABASE MODEL ====

The default Model comprises two database queries, 'item' and 'all_items'. Additional queries can be added and subsequently selected by adding conditions to the Switch function in the Model. A number of parameters can be used to modify the query by adding the following elements. Setting no elements reduces the query to SELECT section_table.* FROM section_table.


Mode
	The type of query as a value.
	default = SELECT.
	options = SELECT COUNT, INSERT, DELETE, UPDATE.
	use = 

		'mode' => 'SELECT COUNT',

Route
	The table(s) that the section_source_table relates to as an array

	use = 
		
		'target_table' => array(

			'using' => 'related_field_name', 
			- the table to relate to
			
			'belongs_to' => 'one_record',
			- either a one to one relationship where
			related_field_name = target_table.id

		OR...

			'to_get' => 'many_records',
			- a one to many relationship where
			source_table.id = related_field_name

		options = 
			'condition' => filter clause as value

		),

Condition
	The WHERE clause to control which data is returned, listed as an array where each element extends the filter.
	use = 

		'condition' => array(
			'field_name = "data_value"',
		),

Limit
	The means to control how much data is returned as an array.
	use =

		'limit' => array(from_record_number, number of Records),

Return
	The fields to be returned as an array of key value pairs.
	Key is the table field_name, Value is optional if the field_name is to be re-named.
	default = * (all fields).
	use = 

		'return' => array(field_name => new_field_name_if_needed),

Order
	How the returned data should be sorted, as an array of key value pairs. Key is the field_name, Value is the order type - ASC (ascending) or DESC (descending).
	default Value = ASC (if empty).
	use =

		'order' => array('field_name' => 'order_type'),


**** COMMAND-LINE ACCESS ****

Command-line output from the app for development and debugging is enabled. In Unix change directory to the web app route directory and enter 'php index.php controller=application request=home' (without the apostrophes). This should return the data generated by the MODEL and CONTROLLER followed by the HTML output from the VIEW (Add output=json as a third argument to see the output in json). The same can be run for each section's controller to show just the output returned by the MODEL and the CONTROLLER by setting controller=Controller_Name.


**** WARRANTY ****

This code-base carries no warranty, by using this code the user assumes all liabilities and responsibility for their installation and use of the contents of this repository.
			
