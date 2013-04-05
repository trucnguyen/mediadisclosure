<?php
	class ArticleFetcher{
	
		function __construct(){
		}
		
		public function getArticles(){
			$NUM_CATEGORIES = 4;
			$NUM_ARTICLES_PER_CAT = 5;

			$RANDOM_CATEGORY_ORDERINGS = array(
				array("world", "entertainment", "living", "business"),
				array("world", "entertainment", "business", "living"),
				array("entertainment", "world", "living", "business"),
				array("entertainment", "world", "business", "living"),
				
				array("living", "business", "entertainment", "world"),
				array("living", "business", "world", "entertainment"),
				array("business", "living", "entertainment", "world"),
				array("business", "living", "world", "entertainment"),
				
				array("living", "world", "entertainment", "business"),
				array("living", "world", "business", "entertainment"),
				array("world", "living", "entertainment", "business"),
				array("world", "living", "business", "entertainment"),

				array("entertainment", "business", "living", "world"),
				array("entertainment", "business", "world", "living"),
				array("business", "entertainment", "living", "world"),
				array("business", "entertainment", "world", "living")
			);		
			
			$categoriesRandomKey = array_rand($RANDOM_CATEGORY_ORDERINGS, 1);
			$categories = $RANDOM_CATEGORY_ORDERINGS[$categoriesRandomKey];
			
			$articleSet = array();
			
			for($i=0; $i < $NUM_CATEGORIES; $i++){
				// i+1 because indexing at 1 for R
				$categoryorder = ($i+1);
				$articles = $this->getArticlesByCategory($categories[$i], $categoryorder, $NUM_ARTICLES_PER_CAT);
				$articleSet["$categoryorder"] = $articles;
			}
			return json_encode($articleSet);
		}
	
    	public function getArticlesByCategory($category, $categoryorder, $num_articles){
			set_time_limit(250);
		
			if($category == "world"){
				$url = "http://rss.cnn.com/rss/cnn_world.rss";
			}
			else if($category == "entertainment"){
				$url = "http://rss.cnn.com/rss/cnn_showbiz.rss";
			}
			else if($category == "living"){
				$url = "http://www.nypost.com/rss/pagesix.xml";
			}
			else if($category == "business"){
				$url = "http://rss.cnn.com/rss/money_latest.rss";
			}
		
			$ch = curl_init();
			
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  
			// grab URL and pass it to the browser
			$news_us = curl_exec($ch);
		
			// close cURL resource, and free up system resources
			curl_close($ch);
		
			$docNews = new DOMDocument();
			$docNews->loadXML($news_us); 
			$nodesArticles = $docNews->getElementsByTagName("item");
			
			// built up from node list
			$articleSet = array();

			// concert all dom nodes into array elements instead
			foreach($nodesArticles as $nodesArticle){  
				// a newsItem is an associative array with two fields: "title" and "desc"
				$newsItem = array();

				$title = $nodesArticle->getElementsByTagName("title")->item(0)->nodeValue;
				$desc = $nodesArticle->getElementsByTagName("description")->item(0)->nodeValue;
				
				if(($title != "Sightings...") && ($title != "We hear...") && (substr($desc, 0, 4) != "<div")){
					// set the title property of the newsItem
					$newsItem["title"] = $title;
					$newsItem["desc"] = $desc;
					
					$articleSet[] = $newsItem;
				}
			}
		
			// randomize order of articles	
			shuffle($articleSet);
		
			// take only the number of articles the callee wants (e.g. 5 articles per category)
			$articleSet = array_slice($articleSet, 0, $num_articles);
			
			for($i=0; $i < $num_articles; $i++){
				$newsItem = $articleSet[$i];
				$desc = $newsItem["desc"];
			
				// set the desc property of the newsItem
				if($category == "world" || $category == "entertainment"){
					$desc = substr($desc, 0,strrpos($desc,"<div"));
		
				}
				else if($category == "business"){
					$desc = substr($desc, 0,strrpos($desc,"<img"));
				}
				// strip out funky chars
				$tokens = array("’", "‘" , "'", "'", "'", "'", "‘", "’", "“", "”");
				$desc = str_replace($tokens, "" ,$desc);
				$desc = str_replace("–","-",$desc);
				$desc = str_replace("—","-",$desc);
				$desc = preg_replace('/[^(\x20-\x7F)]*/','', $desc);
				
				// limit descriptions to 200 characters
/*
				if(strlen($desc)>=200){
					$desc = substr_replace($desc, '...', 200);  
				}
*/

				// set category
				$newsItem["cat"] = $category;
				$newsItem["catorder"] = $categoryorder;
				$newsItem["storyorder"] = ($i+1); // R indexes at 1 instead of 0
				$newsItem["storyid"] = "C" . $categoryorder . "S" . $newsItem["storyorder"];
				
				// set select time and hide time as 0 initially 
				$newsItem["selecttime"] = "-1";
				$newsItem["hidetime"] = "-1";
				$newsItem["guid"] = "";
				$newsItem["desc"] = $desc;

				// Again use array indexing at 1 because this is what R likes
				$returnArticles[$i+1] = $newsItem;
			}
		
		return $returnArticles;
		}
	}

	$ArticleFetcher = new ArticleFetcher();
	echo($ArticleFetcher->getArticles());

?>