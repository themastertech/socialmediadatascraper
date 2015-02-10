<?php
session_start();

require_once('library/facebook/facebook.php');
include("library/functions.php");

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

if (isset($_GET["q"]) and isset($_GET["type"])) {
	$q = $_GET["q"];
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
			
			// Attempt to query the graph:
			$graph_url = "https://graph.facebook.com/search?q=".$q."&type=".$type."&access_token=".$access_token;
			
			//echo $graph_url."<br/><br/>";
			
			$response = curl_get_file_contents($graph_url);
			$decoded_response = json_decode($response, true);
	  
			//Check for errors 
			if ($decoded_response->error) {
				// check to see if this is an oAuth error:
				if ($decoded_response->error->type == "OAuthException") {
				 	echo $decoded_response->error->message;
				}
				else {
				  echo $decoded_response->error->message;
				}
			  } 
			  else {
			  	// success
				//echo("success" . $decoded_response->name);
				//echo($access_token);
				
				//echo $response;
				
				if ($type == "group") {		
					if (count($decoded_response['data'])>0) {			
						echo "<ul>";
						
						for($i=0; $i<count($decoded_response['data']); $i++) {						
							echo "<li id='" . $decoded_response['data'][$i]['id'] ."'>";
								echo "<div class='inner'>";
									//$img_url = "http://graph.facebook.com/".$group_id."/picture?type=square";
									echo $decoded_response['data'][$i]["name"];
								echo "</div>";
							echo "</li>";
						}
						
						echo "</ul>";
					}
					else {
						echo "<p>No results.</p>";	
					}
				}
				
				if ($type == "event" || $type == "place" || $type == "page" || $type == "user") {	
					if (count($decoded_response['data'])>0) {			
						echo "<ul>";
						
						for($i=0; $i<count($decoded_response['data']); $i++) {			
							$data_id = $decoded_response['data'][$i]['id'];			
							echo "<li id='" . $data_id ."'>";
								echo "<div class='inner'><div class='image'>";
									echo "<img src='http://graph.facebook.com/".$data_id."/picture?type=square'/>";
									echo "</div>";
									echo "<div class='name'>".$decoded_response['data'][$i]["name"]."</div>";
								echo "</div>";
							echo "</li>";
						}
						
						echo "</ul>";
					}
					else {
						echo "<p>No results.</p>";	
					}
				}
				//$_SESSION['paging'] = $decoded_response['paging']['next'];
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