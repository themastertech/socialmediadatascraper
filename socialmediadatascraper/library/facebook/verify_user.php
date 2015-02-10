<?PHP
session_start();

require_once 'facebook.php'; //include the facebook php sdk
include("../functions.php");
include("../mailer.php");

$facebook = new Facebook(array(
  'appId' => '1392410527722293',
  'secret' => '67724ff9a73da4b2f01d60d848fd2382',
));

$user = $facebook->getUser();
if ($user) { // check if current user is authenticated
    try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');  //get current user's profile information using open graph
            }
         catch(Exception $e){}
		 //var_dump($user_profile);
}else{
	header("location: http://commandq.org/socialmediadatascraper/");
	exit;
}

// Login or logout url will be needed depending on current user state.
if ($user) {
	$logoutUrl = $facebook->getLogoutUrl();
	$email     = $user_profile{'email'};
  	$name     = $user_profile{'name'};
	$username     = $user_profile{'username'};
	$id     = $user_profile{'id'};
	
  	$_SESSION['logged_in'] = true;
	$_SESSION['account_type'] = "facebook";
	
	if (isset($_GET["source"])) 
		header('location: http://commandq.org/socialmediadatascraper/'.$_GET["source"]);
	else 
		header("location: http://commandq.org/socialmediadatascraper/");
		
	exit;
	
  /*include("check_user.php");
  $data        = checkFBEmail($email);
  if($data){
	  $q2 = "SELECT * FROM
			users
			WHERE
			email='$email' 
			AND active=true 
			AND oauth_provider='facebook'
		";
	$r2 = mysql_query($q2) or die("ERROR 1_check user :: ".mysql_error());
	$row = mysql_fetch_array($r2);
	$_SESSION['user_id']=$row['user_id'];
	
	
	
  }else{	  
	  $rand_string = get_random_string(30);
				//echo date("Y-m-d H:i:s");
				$create_date = date("Y-m-d H:i:s");
		$q = "INSERT INTO users
				(name,email,password,create_date,hash)
				VALUES
				('$name','$email','$password','$create_date','$rand_string')
			";
		$r = mysql_query($q) or die("Error 1:: ".mysql_error());
		
	  	$subject = "Confirm Your Email Address!";
		$from        = "commandq.org";
		$to          = $email;

		$num_id      = num_of_users();
		$link        = "http://commandq.com/verify_email.php?hash=".$rand_string."&id=$num_id";
		$body        = "Hi $name,
		Thanks for registration! Your account has been created on commandq.org. Please click the link below to verify your email address.\r\n
		$link\r\n
		Thanks,";
		$mailer = new mailer($to,$subject,$body);
		header("location: http://commandq.org/reg_successful.php?email=".$email);
		exit;
		
	header("location: http://commandq.org/socialmediadatascraper/");	
	//You are signed into FB but not with this APP
	exit;
  }
  */
} else {
	header("location: http://commandq.org/socialmediadatascraper/");	
	//Either You are not signed into FB or this app doesn't have permission to work!
	exit;
}
?>