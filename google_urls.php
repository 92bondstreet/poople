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
 

define("GOOGLE_WEB",'http://ajax.googleapis.com/ajax/services/search/web');
define("GOOGLE_LOCAL",'http://ajax.googleapis.com/ajax/services/search/local');
define("GOOGLE_VIDEO",'http://ajax.googleapis.com/ajax/services/search/video');
define("GOOGLE_BLOGS",'http://ajax.googleapis.com/ajax/services/search/blogs');
define("GOOGLE_NEWS",'http://ajax.googleapis.com/ajax/services/search/news');
define("GOOGLE_BOOKS",'http://ajax.googleapis.com/ajax/services/search/books');
define("GOOGLE_IMAGE",'http://ajax.googleapis.com/ajax/services/search/images');
define("GOOGLE_PATENT",'http://ajax.googleapis.com/ajax/services/search/patent');


define("MAX_RESULTS",64);
define("MAX_CURSOR",56);


/**
 * Get search base url
 *
 * @param 	$base 			where to search (web, news, books....)
 *
 * @return string|null
 */

function search_base_url($base="news"){
	
	switch($base){
		case "web":
			return GOOGLE_WEB;
		case "local":
			return GOOGLE_LOCAL;
		case "video":
			return GOOGLE_VIDEO;
		case "blogs":
			return GOOGLE_BLOGS;
		case "news":
			return GOOGLE_NEWS;
		case "books":
			return GOOGLE_BOOKS;
		case "image":
			return GOOGLE_IMAGE;
		case "patent":
			return GOOGLE_PATENT;
		default:
			return NULL;
	}
}


/**
 * Custom a search request
 *
 * @param 	$keywords 		to search in Google News 
 * @param 	$base 			where to search (web, news, books....)
 * @param 	$language		language code
 * @param 	$cursor 		start page index (max 7)
 * @param 	$version	 	1.0 
 * @param 	$results_size	small (4) or large (8)
 *
 * @return string|null
 */

function custom_request($keywords, $cursor="0",$base="news", $language="en", $version="1.0", $results_size="8"){
		
	// Step 0 : request parameters
	if($cursor < -1)
		$cursor = 0;
	else if($cursor > MAX_CURSOR)
		$cursor = MAX_CURSOR;
		
	$param = "v=".$version."&rsz=".$results_size."&hl=".$language."&q=".urlencode($keywords)."&start=".$cursor;
	
	// Step 1 : get Search base url
	$search_base_url = search_base_url($base);
	if(isset($search_base_url))
		return $search_base_url."?".$param;
	else
		return NULL;	
}
		
		

 
 ?>