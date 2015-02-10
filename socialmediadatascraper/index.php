<?php
session_start();

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Social Media Data Scraper</title>
<link href="css/styles.css" rel="stylesheet"/>
<link href='http://fonts.googleapis.com/css?family=Raleway:400,300,700' rel='stylesheet' type='text/css'>

<script type='text/javascript' src='js/jquery.js'></script>
</head>

<body>
<div id="fb-root"></div>
<script>
				window.fbAsyncInit = function() {
				  FB.init({
					appId      : '1392410527722293',
					cookie     : true,  // enable cookies to allow the server to access 
										// the session
					xfbml      : true,  // parse social plugins on this page
					version    : 'v2.1' // use version 2.1
				  });  
				  };
			
			  // logs the user in the application and facebook
			  function loginToFacebook(){
				  // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
				  // for any authentication related change, such as login, logout or session refresh. This means that
				  // whenever someone who was previously logged out tries to log in again, the correct case below 
				  // will be handled. 
				  FB.getLoginStatus(function(response) {
					// Here we specify what we do with the response anytime this event occurs. 
					if (response.status === 'connected') {
					  // The response object is returned with a status field that lets the app know the current
					  // login status of the person. In this case, we're handling the situation where they 
					  // have logged in to the app.
					  
						<?php
							if (isset($_GET["source"])) {
						?>
								window.location.href = 'library/facebook/verify_user.php?source=<?=$_GET["source"]?>';
						<?php
							} 
							else {
						?>
								window.location.href = 'library/facebook/verify_user.php';
						<?php
							}
						?>
					} else if (response.status === 'not_authorized') {
					  // In this case, the person is logged into Facebook, but not into the app, so we call
					  // FB.login() to prompt them to do so. 
					  // In real-life usage, you wouldn't want to immediately prompt someone to login 
					  // like this, for two reasons:
					  // (1) JavaScript created popup windows are blocked by most browsers unless they 
					  // result from direct interaction from people using the app (such as a mouse click)
					  // (2) it is a bad experience to be continually prompted to login upon page load.
					  FB.login(function(response) {
							  if(response.authResponse) {
								 if (response.perms)
									  window.location.href = 'library/facebook/verify_user.php';
							  } else {
								// user is not logged in
							  }
						},{scope:'email'}); // which data to access from user profile
					} else {
					  // In this case, the person is not logged into Facebook, so we call the login() 
					  // function to prompt them to do so. Note that at this stage there is no indication
					  // of whether they are logged into the app. If they aren't then they'll see the Login
					  // dialog right after they log in to Facebook. 
					  // The same caveats as above apply to the FB.login() call here.
					  FB.login(function(response) {
						  if(response.authResponse) {
							 if (response.perms)
								  window.location.href = 'library/facebook/verify_user.php';
						  } else {
							// user is not logged in
						   }
					  },{scope:'email'}); // which data to access from user profile
					}
				  });
				  
				  FB.Event.subscribe('auth.login', function(resp) {
						window.location = 'library/facebook/verify_user.php';
					});
				 }

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>


<div class="container">
	<h2><a href="/socialmediadatascraper/">Social Media Data Scraper</a></h2>
    
    <div id="main-wrapper">
    	<?php 
			if ($_SESSION["logged_in"] == false) {
		?>
            <form method="POST" action="#" name="the-form" id="the-form" onSubmit="return false">
                <div class="inner">
                   <label style="margin-top: 7px;">Login with:</label>
                   
                   <select name="account_type" id="account_type" style="margin: 7px 5px 0 0;">
                   	<option value="facebook">Facebook</option>
                      <option value="twitter" disabled>Twitter</option>
                      <option value="googleplus" disabled>Google+</option>
                   </select>
                  
                   <input type="button" value="Go" class="btn" onClick="loginToSocialAccount(document.getElementById('account_type').value)"/>
                </div>
            </form>
     	<?php
			}
			else {
		?>
        		<h3 style="text-align: center;font-size: 14px;font-weight: 500;margin-bottom: 15px;">Logged in with
        <?php
			$account_type = "";
			
				if ($_SESSION["account_type"] == "facebook") {
					$account_type = "Facebook";
					echo $account_type;
				}
				else if ($_SESSION["account_type"] == "twitter") {
					$account_type = "Twitter";
					echo $account_type;
				}
				else if ($_SESSION["account_type"] == "googleplus") {
					$account_type = "Google+";
					echo $account_type;
				}
		?>
             </h3>
             
             <a href="logout.php" style="text-align: center;display: block;">Logout</a>

        		<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
              		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
        <?php
				if ($_SESSION["account_type"] == "facebook") {
		?>
        		
                    <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active ui-state-hover" role="group" tabindex="0" aria-controls="tabs-1" aria-labelledby="ui-id-1" aria-selected="true">
                        <a href="#tabs-1" class="ui-tabs-anchor" role="group" tabindex="-1" id="ui-id-1">Groups</a>
                        <span class="triangle"></span>
                    </li>
                    <li class="ui-state-default ui-corner-top" role="event" tabindex="0" aria-controls="tabs-2" aria-labelledby="ui-id-2" aria-selected="true">
                        <a href="#tabs-2" class="ui-tabs-anchor" role="event" tabindex="-1" id="ui-id-2">Events</a>
                        <span class="triangle"></span>
                    </li>
                    <li class="ui-state-default ui-corner-top" role="place" tabindex="0" aria-controls="tabs-3" aria-labelledby="ui-id-3" aria-selected="true">
                        <a href="#tabs-3" class="ui-tabs-anchor" role="place" tabindex="-1" id="ui-id-3">Places</a>
                        <span class="triangle"></span>
                    </li>
                    <li class="ui-state-default ui-corner-top" role="page" tabindex="0" aria-controls="tabs-4" aria-labelledby="ui-id-4" aria-selected="true">
                        <a href="#tabs-4" class="ui-tabs-anchor" role="page" tabindex="-1" id="ui-id-4">Pages</a>
                        <span class="triangle"></span>
                    </li>
                    <li class="ui-state-default ui-corner-top" role="user" tabindex="0" aria-controls="tabs-5" aria-labelledby="ui-id-5" aria-selected="true">
                        <a href="#tabs-5" class="ui-tabs-anchor" role="user" tabindex="-1" id="ui-id-5">People</a>
                        <span class="triangle"></span>
                    </li>

        <?php
				}
		?>
        			</ul>  
                    
                    <div class="tabssection">
                    		<div id="tabs-1" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="group" aria-expanded="true" aria-hidden="false" style="display: block;">
                            
                            		<div class="left-content">
                                		<input type="text" name="searchbox_q" placeholder="Search Groups" class="search-box" id="fb-groups-search-box"/> <input type="button" value="Search" class="btn" id="fb-groups-search-btn" onclick="populateListWithEntertedQuery(document.getElementById('fb-groups-search-box').value, 'group');"/>
                                        
                                        <div style="clear:both;"></div>
                                        
                                        <div class="search-results" id="group-search-results">
                                        	<div class="header">Search Results</div>

                                                <div class="loading">Finding Groups...<br><img src="img/loading.gif"/></div>
                                                
                                            <div class="data">
                                            </div>
                                        </div>
                                </div>
                                
                                <div class="right-content" id="group-right-content">
                                    <div class="loading">Loading Group...<br><img src="img/loading.gif"/></div>
										
                                        <div class="data">
                                            </div>
                                </div>
                            </div>
                            <div id="tabs-2" aria-labelledby="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="event" aria-expanded="false" aria-hidden="true" style="display: none;">
                            
                            		<div class="left-content">
                                		<input type="text" name="searchbox_q" placeholder="Search Events" class="search-box" id="fb-events-search-box"/> <input type="button" value="Search" class="btn" id="fb-events-search-btn" onclick="populateListWithEntertedQuery(document.getElementById('fb-events-search-box').value, 'event');"/>
                                        
                                        <div style="clear:both;"></div>
                                        
                                        <div class="search-results" id="event-search-results">
                                        	<div class="header">Search Results</div>

                                                <div class="loading">Finding Events...<br><img src="img/loading.gif"/></div>
                                                
                                            <div class="data">
                                            </div>
                                        </div>
                                </div>
                                
                                <div class="right-content" id="event-right-content">
                                    <div class="loading">Loading Event...<br><img src="img/loading.gif"/></div>
										
                                        <div class="data">
                                            </div>
                                </div>
                            </div>
                            <div id="tabs-3" aria-labelledby="ui-id-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="event" aria-expanded="false" aria-hidden="true" style="display: none;">
                            
                            		<div class="left-content">
                                		<input type="text" name="searchbox_q" placeholder="Search Places" class="search-box" id="fb-places-search-box"/> <input type="button" value="Search" class="btn" id="fb-places-search-btn" onclick="populateListWithEntertedQuery(document.getElementById('fb-places-search-box').value, 'place');"/>
                                        
                                        <div style="clear:both;"></div>
                                        
                                        <div class="search-results" id="place-search-results">
                                        	<div class="header">Search Results</div>

                                                <div class="loading">Finding Places...<br><img src="img/loading.gif"/></div>
                                                
                                            <div class="data">
                                            </div>
                                        </div>
                                </div>
                                
                                <div class="right-content" id="place-right-content">
                                    <div class="loading">Loading Place...<br><img src="img/loading.gif"/></div>
										
                                        <div class="data">
                                            </div>
                                </div>
                            </div>
                            <div id="tabs-4" aria-labelledby="ui-id-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="event" aria-expanded="false" aria-hidden="true" style="display: none;">
                            
                            		<div class="left-content">
                                		<input type="text" name="searchbox_q" placeholder="Search Pages" class="search-box" id="fb-pages-search-box"/> <input type="button" value="Search" class="btn" id="fb-pages-search-btn" onclick="populateListWithEntertedQuery(document.getElementById('fb-pages-search-box').value, 'page');"/>
                                        
                                        <div style="clear:both;"></div>
                                        
                                        <div class="search-results" id="page-search-results">
                                        	<div class="header">Search Results</div>

                                                <div class="loading">Finding Pages...<br><img src="img/loading.gif"/></div>
                                                
                                            <div class="data">
                                            </div>
                                        </div>
                                </div>
                                
                                <div class="right-content" id="page-right-content">
                                    <div class="loading">Loading Page...<br><img src="img/loading.gif"/></div>
										
                                        <div class="data">
                                            </div>
                                </div>
                            </div>
                            <div id="tabs-5" aria-labelledby="ui-id-5" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="event" aria-expanded="false" aria-hidden="true" style="display: none;">
                            
                            		<div class="left-content">
                                		<input type="text" name="searchbox_q" placeholder="Search People" class="search-box" id="fb-people-search-box"/> <input type="button" value="Search" class="btn" id="fb-people-search-btn" onclick="populateListWithEntertedQuery(document.getElementById('fb-people-search-box').value, 'user');"/>
                                        
                                        <div style="clear:both;"></div>
                                        
                                        <div class="search-results" id="user-search-results">
                                        	<div class="header">Search Results</div>

                                                <div class="loading">Finding People...<br><img src="img/loading.gif"/></div>
                                                
                                            <div class="data">
                                            </div>
                                        </div>
                                </div>
                                
                                <div class="right-content" id="user-right-content">
                                    <div class="loading">Loading User...<br><img src="img/loading.gif"/></div>
										
                                        <div class="data">
                                            </div>
                                </div>
                            </div>
                            <div style="clear:both;"></div>
                    </div>
              </div>
        <?php
			}
		?>
    </div>
    
    
    <div id="process"></div>
    
    <div style="clear:both;"></div>
</div>
</body>

<script type="text/javascript">
	var queryType = "group"
	
	jQuery(document).ready(function($) {
		$(".ui-tabs-anchor").click(function() {
			var the_anchor = $(this).attr("href");
			
			$("#tabs ul li").removeClass("ui-tabs-active");
			$("#tabs ul li").removeClass("ui-state-active");
			$("#tabs ul li").removeClass("ui-state-hover");
			
			$(this).parent().addClass("ui-tabs-active");
			$(this).parent().addClass("ui-state-active");
			$(this).parent().addClass("ui-state-hover");
			
			$(".ui-tabs-panel").hide();
			$(the_anchor).show();
			
			queryType = $(this).attr("role");
				
			return false;
		});
		
		//populateListWithEntertedQuery('soccer', 'group');
		
		$("#fb-groups-search-box").keyup(function(event){
			if(event.keyCode == 13){
				$("#fb-groups-search-btn").click();
			}
		});
		
		$("#fb-events-search-box").keyup(function(event){
			if(event.keyCode == 13){
				$("#fb-events-search-btn").click();
			}
		});
		
		$("#fb-places-search-box").keyup(function(event){
			if(event.keyCode == 13){
				$("#fb-places-search-btn").click();
			}
		});
		
		$("#fb-pages-search-box").keyup(function(event){
			if(event.keyCode == 13){
				$("#fb-pages-search-btn").click();
			}
		});
		
		$("#fb-people-search-box").keyup(function(event){
			if(event.keyCode == 13){
				$("#fb-people-search-btn").click();
			}
		});
	});
	
	var xmlHttp

	function populateListWithEntertedQuery(q, type)
	{
		if (q.length!=0)
			{ 
				jQuery("#"+type+"-search-results .loading").show();

				xmlHttp=GetXmlHttpObject()
		
				if (xmlHttp==null)
					{
						alert("Browser does not support HTTP Request");
						return
					} 
					
				var url="populatesearchlist.php"
				url=url+"?q="+q
				url=url+"&type="+type
				
				xmlHttp.onreadystatechange=function(){stateChangedQuery(type);};
				xmlHttp.open("GET",url,true)
				xmlHttp.send(null)
			}
	} 
		
	function stateChangedQuery(type) 
	{ 
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 				
				jQuery("#"+type+"-search-results .data").html(xmlHttp.responseText);
				jQuery("#"+type+"-search-results .loading").hide();
				
				jQuery("#"+type+"-search-results ul li").click(function() {
					jQuery("#"+type+"-search-results ul li").css("background", "#fff");
					jQuery("#"+type+"-search-results ul li").css("color", "#000");
					jQuery(this).css("background", "rgb(52, 152, 219)");
					jQuery(this).css("color", "#fff");
					
					var elem_id = jQuery(this).attr("id");
					
					showFacebookData(type, elem_id)
				});
			} 
	} 
	
	function stateChangedGetData(type) 
	{ 
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				jQuery("#"+type+"-right-content .data").html(xmlHttp.responseText);
				jQuery("#"+type+"-right-content .loading").hide();
			} 
	}
		
	function GetXmlHttpObject()
	{ 
		var objXMLHttp=null
		if (window.XMLHttpRequest)
			{
				objXMLHttp=new XMLHttpRequest()
			}
		else if (window.ActiveXObject)
			{
				objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
			}
		return objXMLHttp
	} 
	
	function showFacebookData(dataType, dataId) {
		if (dataId.length!=0)
		{ 
			//jQuery("#"+dataType+"-right-content .data").html("");
			jQuery("#"+dataType+"-right-content .loading").show();

			xmlHttp=GetXmlHttpObject()
	
			if (xmlHttp==null)
				{
					alert("Browser does not support HTTP Request");
					return
				} 
				
			var url="getfacebookdata.php"
			url=url+"?id="+dataId
			url=url+"&type="+dataType
			
			xmlHttp.onreadystatechange=function(){stateChangedGetData(dataType);};
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
	}
	
	jQuery(function($){
		$('.search-results .data').bind('scroll', function() {
			if($(this).scrollTop() + $(this).innerHeight()>=$(this)[0].scrollHeight) {
			 	<?php
					if ($_SESSION['paging'] != "") {
				?>
						//$("#search-results .loading-more")

				<?php
					}
				?>
			}
		})
	});
	
	function loginToSocialAccount(account_type) {
		if (account_type == "facebook") {
			loginToFacebook();
		}
	}
	
</script>

</html>