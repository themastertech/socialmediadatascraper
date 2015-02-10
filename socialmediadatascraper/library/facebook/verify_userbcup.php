<?php

/**
* Copyright 2011 Facebook, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may
* not use this file except in compliance with the License. You may obtain
* a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations
* under the License.
*/

require 'facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId' => '383429858432508',
  'secret' => '7dfd53af2f81d9d7b7a44a5b26d86750',
));

// Get User ID
$user = $facebook->getUser();


if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
  $email     = $user_profile{'email'};
  include("check_user.php");
  if(checkFBEmail($email)>-1){
  	
  	header("location: ../makethekal.php");
	exit;
  }else{
  	header("location: ../login_error.php?error=1");			//You are signed into FB but not with this APP
	exit;
  }
} else {
  	header("location: ../login_error.php?error=2");			//Either You are not signed into FB or this app doesn't have permission to work!
	exit;
}