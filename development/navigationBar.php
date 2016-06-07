<?php
/*
include './generalFunctions.php';


$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}
$username=$_COOKIE["user"];
*/
?>
<!DOCTYPE html>
<html>
   <body>
<head>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="navigationBar.css" />
<title> Way2Go </title>

 
  <div class="header" id="header">
    <a href="menu.php">
       <img src="../assets/way2goLogoSmall.png" style="padding:25px 0px 0px 20px;"></a>
  </div>     
         
  <div class="menu" id="menu" style="display: inline-block;">

<a href="settingsGeneralPage.php" id="settingsGeneral"> GENERAL SETTINGS </a>

<a href="fleetsPage.php" id="fleet"> FLEET </a>

<a href="routesPage.php" id="routes"> ROUTES </a>

</div>

</head>

</html>