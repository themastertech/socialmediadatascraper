<?PHP
session_start();

	require_once("../../db.php");

	if(isset($_SESSION['twitter_oauth_token'])){
		$twitter_oauth  = $_SESSION['twitter_oauth_token'];
		$twitter_secret = $_SESSION['twitter_oauth_token_secret'];
		var_dump($twitter_oauth,$twitter_secret);
		/*
		
		$q = "SELECT * FROM
				users
				WHERE
				twitter_oauth='$twitter_oauth'
				AND
				twitter_secret='$twitter_secret'
			";
		$r = mysql_query($q) or die("Error 1 in verify_user.php");
		
		$url = '../..login.php?twitter=1';

		
		if(mysql_num_rows($r)>0){
			$d = mysql_fetch_assoc($r);
			$id = $d['id'];
			$url = 'signInAuth.php?id='.$id;
		}
		
		header('location:'.$url);
		exit;
		*/
	}
?>