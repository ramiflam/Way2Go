<?php
include './generalFunctions.php';

// $usrName = $_POST["userName"];
$userName=$_COOKIE["userName"];
echo ("User Name: " . $userName);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu Page</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="menuPage.css">
</head>
<body>   
   
<img class='logo'src= "../assets/way2goLogo.png" height=120/>

<br><br><br>


<div class='circleMenu'>
<div class='circleLink'>
<a href="fleetsPage.php">
  <img class='circle' src="../assets/fleetsLink.png">
</a>
  <label>FLEET</label>
</div>

<div class='circleLink'>  
<a href="routesPage.php">
  <img class='circle' src="../assets/routesLink.png">
</a>
<br>
  <label>ROUTES</label>
</div>

<div class='circleLink'>  
<a href="settingsGeneralPage.php">
  <img class='circle' src="../assets/settingsLink.png">
</a>
<br>
  <label>SETTINGS</label>
</div>
</div>

</body>
</html>