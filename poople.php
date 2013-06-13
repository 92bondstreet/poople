<?php
/**
 * Poople
 *
 * PHP Google Search: this plugins allows you to query google search engine from PHP
 * Search & Save news from google.
 *
 * Copyright (c) 2013 - 92 Bond Street, Yassine Azzout
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *	The above copyright notice and this permission notice shall be included in
 *	all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Poople
 * @version 1.0
 * @copyright 2013 - 92 Bond Street, Yassine Azzout
 * @author Yassine Azzout
 * @link http://www.92bondstreet.com Poople
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

 
// Google search urls
require_once('google_urls.php');	
// token to database
require_once('token.php');	
// SwissCode plugin
// Download on https://github.com/92bondstreet/swisscode
require_once('swisscode.php');	
 
 //Report all PHP errors
error_reporting(E_ALL);
set_time_limit(0);





class Poople {
	
	// Database to save
	private  $pdodb;
		
	// file dump to log
	private  $enable_log;
	private  $log_file_name = "poople.log";
	private  $log_file;
	
	
	/**
	 * Constructor, used to input the data
	 *
	 * @param bool $log
	 */
	public function __construct($log=false){
	
		if(defined('DB_NAME') && defined('DB_USER') && defined('DB_PWD') ){
			try{
				$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;		
				$this->pdodb = new PDO(DB_NAME, DB_USER, DB_PWD,$pdo_options);
				$this->pdodb->exec("SET CHARACTER SET utf8");
			}
			catch (PDOException $e) {
				echo 'Connection failed: ' . $e->getMessage();
				$this->pdodb = null;
			}
			
		}
		else
			$this->pdodb = null;
				
		$this->enable_log = $log;
		if($this->enable_log)
			$this->log_file = fopen($this->log_file_name, "w");
		else
			$this->log_file = null;
			
	}
	
	/**
	 * Destructor, free datas
	 *
	 */
	public function __destruct(){
	
		// and now we're done; close it
		$this->pdodb = null;
		if(isset($this->log_file))
			fclose($this->log_file);
	}
	
	/**
	 * Write to log file
	 *
	 * @param $value string to write 
	 *
	 */
	function dump_to_log($value){
		fwrite($this->log_file, $value."\n");
	}
	
	
	/**
	 * Search a keyword
	 *
	 * @param 	$keywords 		to search in Google News 
	 * @param 	$base 			where to search (web, news, books....)
	 * @param 	$language		language code
	 * @param 	$nb_results 	maximum 64 (8 results/page * 8 pages)
	 *
	 * @return array|null
	 */
	
	function search($keywords, $nb_results=64, $base="news", $language="en"){

		$results = array();
		
		// Step 0 : init max results number (due google limitation)
		if($nb_results>MAX_RESULTS)
			$nb_results = MAX_RESULTS;
		
		$max_page_results = ceil($nb_results / 8.0);
		$max_results = $max_page_results * 8;
		
		// Step 2 : parse with cursor		
		for($start=0;$start<$max_results;$start+=8){
			$google_url = custom_request($keywords,$start);
			if(!isset($google_url))
				continue;
			
			// Step 3 : connect to url
			$response =  MacG_connect_to($google_url);
			
			// Step 4 : Decode JSON response
			$json_response = json_decode($response);
			$current_results = $json_response->responseData->results;
			
			// Step 5 : merge
			if(isset($current_results))
				$results = array_merge($results,$current_results);
		}

		return array_slice($results,0,$nb_results);		// get only number of results defined by users
	}	
	
	
	/**
	 * Insert results in Database
	 *
	 * @param 	$results 				of google search requests
	 * @param 	$results_table_name 	where to insert
	 *
	 * @return bool
	 */
	
	function insert_in_db($results, $results_table_name){
		
		if(!isset($this->pdodb))
			return false;
		
			
		// Step 0 : save to database
		if(count($results)>0){
		
			// insert query prepared statement
			$query = 'INSERT INTO '.$results_table_name.' (id, title, link, image) VALUES (?, ?, ?, ?);';			
			$pdodb_stmt = $this->pdodb->prepare($query);
		
			
			foreach ($results as $current_result){
			
				$title = $current_result->titleNoFormatting;
				$id = MacG_toAscii($title, "'"); // French accent in title
				$url = 	$current_result->unescapedUrl;
				$image = NULL;
				if(isset($current_result->image))
					$image = $current_result->image->url;
								
				// Continue if id exists in DB
				$id_in_db = 'SELECT * FROM '.$results_table_name.' WHERE id=\''.$id.'\'';
				$request = $this->pdodb->query($id_in_db);		
				if($request->rowCount()>0){					
					$request->closeCursor(); // end request
					continue;
				}
				
				// step 2 : insert in db	
				$pdodb_stmt->bindValue(1, $id);
				$pdodb_stmt->bindValue(2, $title);
				$pdodb_stmt->bindValue(3, $url);
				$pdodb_stmt->bindValue(4, $image);		
				$pdodb_stmt->execute();					
			}
		}
		else
			return false;
		
		return true;
	}
}


 
 ?>