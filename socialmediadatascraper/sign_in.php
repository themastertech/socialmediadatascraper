<?php
session_start();

require_once('library/facebook/facebook.php');
include("library/functions.php");

$facebook = new Facebook(array(
  'appId' => '1392410527722293',
  'secret' => '67724ff9a73da4b2f01d60d848fd2382',
));

if ($_SESSION["logged_in"] == true) {
	header("Location: /socialmediadatascraper/");	
}

if (isset($_REQUEST["account_type"])) {
	if ($_REQUEST["account_type"] == "facebook") {
	
	}
	else if ($_REQUEST["account_type"] == "twitter") {
	
	}
	else if ($_REQUEST["account_type"] == "googleplus") {
	
	}
	else {
		header("Location: /socialmediadatascraper/");	
	}
}
else {
	header("Location: /socialmediadatascraper/");	
}
?>
