<?PHP
/****************************
	Aim is to check if the user is registered on our system or not!
	
****************************/

	function checkFBEmail($email){
	include("../../connect.php");
	 $sql_check = mysql_query("SELECT * FROM users WHERE email='".$email."' AND oauth_provider='facebook'");
	  
	  $row_check = mysql_fetch_array($sql_check);
	  
	  if ($row_check["active"] == false) {
		$sql_del = mysql_query("DELETE FROM users WHERE email='".$email."'");  
	  }
	  else {
		$q = "SELECT * FROM
				users
				WHERE
				email='$email' 
				AND active=true 
				AND oauth_provider='facebook'
			";
		$r = mysql_query($q) or die("ERROR 1_check user :: ".mysql_error());
		if(mysql_num_rows($r)>0){
			return true;
		}else{
			return false;
		}
	  }
	}
?>