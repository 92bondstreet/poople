Poople
=========

Poople is a PHP Google Search plugin: it allows you to query google search engine from PHP.

Search & Save news from google.


Requirements
------------
* PHP 5.2.0 or newer
* <a href="https://github.com/92bondstreet/swisscode" target="_blank">SwissCode</a>


What comes in the package?
--------------------------
1. `poople.php` - The Poople class functions to get results from request to Google (web, news, books...) and save them to database...
2. `example.php` - All Poople functions call
3. `google_urls.php` - Google search base url definitions
4. `token.php` - Token file with Database parameters
5. `sql/`- Directory with SQL schema to save results 


Example.php
-----------

	// Init constructor with false value: no dump log file
	$PoopleSearch = new Poople();

	//Search a keyword in google videos
	$results = $PoopleSearch->search("macklemore",5,"videos");
	print_r($results);

	//Search a keyword in google news
	$results = $PoopleSearch->search("macklemore",20);
	print_r($results);

	//Insert results in Database
	$insert = $PoopleSearch->insert_in_db($results, "results");
	var_dump($insert);


To start the demo
-----------------
1. Upload this package to your webserver.
2. In your database manager, browse sql directory import `results.sql`.
3. Update the `token.php` file with database host, name, user and password  
4. Open `example.php` in your web browser and check screen output and database. 
5. Enjoy !


Project status
--------------
Astreed is currently maintained by Yassine Azzout.


Authors and contributors
------------------------
### Current
* [Yassine Azzout][] (Creator, Building keeper)

[Yassine Azzout]: http://www.92bondstreet.com


License
-------
[MIT license](http://www.opensource.org/licenses/Mit)

