<?php
require_once('library/facebook/facebook.php');
include("library/functions.php");

// Facebook PHP SDK v4.0.8

require_once( 'library/facebook/HttpClients/FacebookHttpable.php' );
require_once( 'library/facebook/HttpClients/FacebookCurl.php' );
require_once( 'library/facebook/HttpClients/FacebookCurlHttpClient.php' );

require_once( 'library/facebook/Entities/AccessToken.php' );
require_once( 'library/facebook/Entities/SignedRequest.php' );

require_once( 'library/facebook/FacebookSession.php' );
require_once( 'library/facebook/FacebookRedirectLoginHelper.php' );
require_once( 'library/facebook/FacebookRequest.php' );
require_once( 'library/facebook/FacebookResponse.php' );
require_once( 'library/facebook/FacebookSDKException.php' );
require_once( 'library/facebook/FacebookRequestException.php' );
require_once( 'library/facebook/FacebookOtherException.php' );
require_once( 'library/facebook/FacebookAuthorizationException.php' );
require_once( 'library/facebook/GraphObject.php' );
require_once( 'library/facebook/GraphSessionInfo.php' );

use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;

use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;

session_start();

$facebook = new Facebook(array(
  'appId' => '1392410527722293',
  'secret' => '67724ff9a73da4b2f01d60d848fd2382',
));

$app_id = "1392410527722293";
$app_secret = "67724ff9a73da4b2f01d60d848fd2382"; 
$my_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;

if ($_SESSION["logged_in"] == false) {
	header("Location: /socialmediadatascraper/");	
}

if (isset($_GET["id"]) and isset($_GET["type"])) {
	$id = $_GET["id"];
	$type = $_GET["type"];
	
	if ($_SESSION["account_type"] == "facebook") {
		$valid_user = false;
		
	//If one exists, retrieve the long-term token from `$_COOKIE['FB_LONG_AC_TOKEN']`
	  if (isset($_COOKIE["FB_LONG_AC_TOKEN"]))
	  { // Have long term token, attempt to validate.
		// Attempt to query the graph:
		$graph_url = "https://graph.facebook.com/me?"
		  . "access_token=" . $_COOKIE["FB_LONG_AC_TOKEN"];
		$response = curl_get_file_contents($graph_url);
		$decoded_response = json_decode($response);
	
		// If we don't have an error then it's valid.
		//If valid, use the renew the token and update the cookie if one is returned. (Only one token will be returned per day)
		if (!$decoded_response->error) {
		  $valid_user = true;
		  $access_token = $_COOKIE["FB_LONG_AC_TOKEN"];
		  $out = "Have long life token.<br />";
		}
		else {
		  //Else mark the long-term token as invalid and clear the cookie.
		  // Stored token is invalid.
		  // Attempt to renew token.
	
		  // Exchange short term token for long term.
		  $ch = curl_init("https://graph.facebook.com/oauth/access_token?client_id=".$app_id."&client_secret=".$app_secret."&grant_type=fb_exchange_token&fb_exchange_token=".$facebook->getAccessToken());
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		  $data = curl_exec($ch);
		  curl_close($ch);
	
		  $params = null;
		  parse_str($data, $params);
	
		  if (isset($params["access_token"]))
		  {
			$access_token = $params["access_token"];
	
			$out = "Got long life token.<br />";
			setcookie("FB_LONG_AC_TOKEN", $access_token, time() + (3600 * 24 * 60), "/");
		  }
		  else
		  { // Clear invalid token.
			setcookie("FB_LONG_AC_TOKEN", "false", time() - 3600, "/");
			$out = "Long life token invalid.<br />";
		  }
		}
	  }
	  else if ($facebook->getUser())
	  { // Have short term access token.
		// Verify short term token is valid still.
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');
		}
		catch (FacebookApiException $e) { }
	
		if (is_array($user_profile)) { // Have user.
		  $valid_user = true;
	
		  // Exchange short term token for long term.
		  $ch = curl_init("https://graph.facebook.com/oauth/access_token?client_id=".$app_id."&client_secret=".$app_secret."&grant_type=fb_exchange_token&fb_exchange_token=".$facebook->getAccessToken());
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		  $data = curl_exec($ch);
		  curl_close($ch);
	
		  $params = null;
		  parse_str($data, $params);
	
		  if (isset($params["access_token"]))
		  {
			$access_token = $params["access_token"];
	
			$out = "Got long life token2.<br />";
			setcookie("FB_LONG_AC_TOKEN", $access_token, time() + (3600 * 24 * 60), "/");
		  }
		}
	  }
	
		//echo $out;
		
	  if ($access_token) {
		$facebook->setAccessToken($access_token);
	
		// See if there is a user from a cookie
		$user = $facebook->getUser();
	
		if ($user) {
		  try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');
			
			//var_dump($user_profile)."<br/>";
			
			// init app with app id and secret
			FacebookSession::setDefaultApplication($app_id, $app_secret);
			
			$session = new FacebookSession($access_token);	
			
			// Attempt to query the graph:
			$request = new FacebookRequest(
			  $session,
			  'GET',
			  '/'.$id
			);
			$response = $request->execute();
			$graphObject = $response->getGraphObject();
			
			/* handle the result */
			$results = $graphObject->asArray();
			
			//print_r($results);			
						
			if ($type == "group") {
				$description = $results["description"];
			  	$email = $results["email"]; 
			  	$icon = $results["icon"]; 
			  	$name = $results["name"]; 
			  	$owner = $results["owner"]->name;
			  	$owner_id = $results["owner"]->id;
			  	$privacy = $results["privacy"];
			  	$updated_time = $results["updated_time"];
				
				echo "<b>Group ID:</b> $id <br/><br/>";  
				echo "<b>Group Name:</b> <a href='http://facebook.com/groups/$id' target='_blank'>$name</a> <br/><br/>";  
				echo "<b>Group Description:</b> <br/><br/> $description <br/><br/>"; 
				echo "<b>Owner:</b> <a href='http://facebook.com/$owner_id' target='_blank'>$owner</a> <br/><br/>";  
				echo "<b>Email:</b> <a href='mailto:$email'>$email</a> <br/><br/>";  
				echo "<b>Privacy:</b> $privacy <br/><br/>";  
				echo "<b>Updated Time:</b> $updated_time <br/><br/>";  
				
				// Attempt to query the graph:
				$request = new FacebookRequest(
				  $session,
				  'GET',
				  '/'.$id.'/members'
				);
				
				$response = $request->execute();
				$graphObject = $response->getGraphObject();
				
				/* handle the result */
				$group_members = $graphObject->asArray();
				$member_count = count($group_members["data"]);
				
				echo "<b>Total Members:</b> $member_count <br/><br/>";  
				
				//print_r($group_members);
				
				for($i=0; $i<count($group_members['data']); $i++) {	
					$user_id = $group_members['data'][$i]->id;
					$img_url = "http://graph.facebook.com/".$user_id."/picture?type=square";
					
					echo "<div class='profile-pic'><a href='http://facebook.com/$user_id' target='_blank'><img src='$img_url'/></a></div>";
				}
			}
			
			if ($type == "event") {
				$description = $results["description"];
			  	$email = $results["email"]; 
			  	$name = $results["name"]; 
			  	$owner = $results["owner"]->name;
			  	$owner_id = $results["owner"]->id;
			  	$privacy = $results["privacy"];
			  	$updated_time = $results["updated_time"];
			  	$start_time = $results["start_time"];
			  	$end_time = $results["end_time"];
				
				echo "<b>Event ID:</b> $id <br/><br/>";  
				echo "<b>Event Name:</b> <a href='http://facebook.com/events/$id' target='_blank'>$name</a> <br/><br/>";  
				echo "<b>Event Description:</b> <br/><br/> $description <br/><br/>"; 
				echo "<b>Owner:</b> <a href='http://facebook.com/$owner_id' target='_blank'>$owner</a> <br/><br/>";  
				echo "<b>Email:</b> <a href='mailto:$email'>$email</a> <br/><br/>";  
				echo "<b>Privacy:</b> $privacy <br/><br/>";  
				echo "<b>Start Time:</b> $start_time <br/><br/>";  
				echo "<b>End Time:</b> $end_time <br/><br/>";  
				echo "<b>Updated Time:</b> $updated_time <br/><br/>";  
				
				// Attempt to query the graph:
				$request = new FacebookRequest(
				  $session,
				  'GET',
				  '/'.$id.'/attending'
				);
				
				$response = $request->execute();
				$graphObject = $response->getGraphObject();
				
				/* handle the result */
				$event_attending = $graphObject->asArray();
				$member_count = count($event_attending["data"]);
				
				echo "<b>Total Attendees:</b> $member_count <br/><br/>";  
				
				//print_r($group_members);
				
				for($i=0; $i<count($event_attending['data']); $i++) {	
					$user_id = $event_attending['data'][$i]->id;
					$img_url = "http://graph.facebook.com/".$user_id."/picture?type=square";
					
					echo "<div class='profile-pic'><a href='http://facebook.com/$user_id' target='_blank'><img src='$img_url'/></a></div>";
				}
			}
			
			if ($type == "place") {
				$description = $results["description"];
			  	$category = $results["category"]; 
			  	$name = $results["name"]; 
			  	$lot = $results["parking"]->lot;
			  	$street = $results["parking"]->street;
			  	$valet = $results["parking"]->valet;
			  	$talking_about_count = $results["talking_about_count"];
			  	$website = $results["website"];
			  	$were_here_count = $results["were_here_count"];
			  	$likes = $results["likes"];
			  	$link = $results["link"];
			  	$checkins = $results["checkins"];
			  	$city = $results["location"]->city;
			  	$country = $results["location"]->country;
			  	$latitude = $results["location"]->latitude;
			  	$longitude = $results["location"]->longitude;

				echo "<b>Place ID:</b> $id <br/><br/>";  
				echo "<b>Place Name:</b> <a href='$link' target='_blank'>$name</a> <br/><br/>";  
				echo "<b>Place Description:</b> <br/><br/> $description <br/><br/>"; 
				echo "<b>Place Location:</b> $city, $country <br/><br/>"; 
				
				/*add google map here (API)*/
				
				echo "<b>Likes:</b> $likes <br/><br/>"; 
				echo "<b>Checkins:</b> $checkins <br/><br/>"; 
				echo "<b>Parking:</b> <br/><br/> &nbsp;<b>Lot:</b> $lot <br/><br/> &nbsp;<b>Street:</b> $street <br/><br/> &nbsp;<b>Valet:</b> $valet <br/><br/>"; 
				echo "<b>Website:</b> <a href='$website' target='_blank'>$website</a> <br/><br/>";  
				echo "$talking_about_count people currently talking about this place. <br/><br/>"; 
				echo "$were_here_count people were here. <br/><br/>"; 
				
			}
			
			if ($type == "page") {
				$description = $results["about"];
			  	$category = $results["category"]; 
			  	$name = $results["name"]; 
			  	$lot = $results["parking"]->lot;
			  	$street = $results["parking"]->street;
			  	$valet = $results["parking"]->valet;
			  	$talking_about_count = $results["talking_about_count"];
			  	$website = $results["website"];
			  	$were_here_count = $results["were_here_count"];
			  	$likes = $results["likes"];
			  	$link = $results["link"];
			  	$checkins = $results["checkins"];

				echo "<b>Page ID:</b> $id <br/><br/>";  
				echo "<b>Page Name:</b> <a href='$link' target='_blank'>$name</a> <br/><br/>";  
				echo "<b>Page Description:</b> <br/><br/> $description <br/><br/>"; 
				echo "<b>Category:</b> $category <br/><br/>"; 
				echo "<b>Likes:</b> $likes <br/><br/>"; 
				echo "<b>Parking:</b> <br/><br/> &nbsp;<b>Lot:</b> $lot <br/><br/> &nbsp;<b>Street:</b> $street <br/><br/> &nbsp;<b>Valet:</b> $valet <br/><br/>"; 
				echo "<b>Website:</b> <a href='$website' target='_blank'>$website</a> <br/><br/>";  
				echo "$talking_about_count people currently talking about this place. <br/><br/>"; 
				echo "$were_here_count people were here. <br/><br/>"; 
				
			}
			
			if ($type == "user") {
				$user_id = $results["id"];
				$first_name = $results["first_name"];
			  	$last_name = $results["last_name"]; 
			  	$middle_name = $results["middle_name"]; 
			  	$name = $results["name"]; 
			  	$link = $results["link"]; 

				echo "<b>User ID:</b> $user_id <br/><br/>";  
				echo "<b>Name:</b> <a href='$link' target='_blank'>$name</a> <br/><br/>";  
				
			}

		  } catch (FacebookApiException $e) {
			$out = '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
			$user = null;
		  }
		}
	  }
	}
}

// note this wrapper function exists in order to circumvent PHP’s
//strict obeying of HTTP error codes.  In this case, Facebook
//returns error code 400 which PHP obeys and wipes out
//the response.
function curl_get_file_contents($URL) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_URL, $URL);
	$contents = curl_exec($c);
	$err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
	curl_close($c);
	if ($contents) return $contents;
		else return FALSE;
}

?>