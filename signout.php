<?php
//****************************************************************************************************************************************************
//
//													Signout and destrou session to index/login page
//
//**************************************************************************************************************************************************** -->
     
 // Clear and destroy sessions and redirect user to home page url.
session_start();
$_SESSION = array();
session_destroy();
// Redirect to where the site home page is located -- Eg: localhost
header('Location: '.'index.php');
die;