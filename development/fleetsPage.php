<?php
include './generalFunctions.php';
include './navigationBar.php';

$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}
$username=$_COOKIE["user"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fleets Page</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="fleetsPage.css">
</head>
<body>   
   





</body>
</html>