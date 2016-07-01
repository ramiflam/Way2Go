<?php
include './generalFunctions.php';
$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}

$userName=$_COOKIE["userName"];
// echo ("User Name: " . $userName ."\n");



// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("debug_" . $fileTimestamp . ".txt","a");
fwrite($file,"In driver select file. \n");


?>

<!DOCTYPE html>
<html>
<head>
    <title>Drivers Upload</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="settingsPage.css">
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    

</head>

<body>  
<?php
// $schoolName= $_GET['schoolName'];
// echo ("SchoolName = " . $schoolName);
?>

<div id="driverUpload"> 
<form action="driverUpload.php" method="post" enctype="multipart/form-data"  > 
    <h2> Upload Driver File:</h2> <input name="csv" type="file" id="csv" accept=".csv" pattern=".{6,}" /> 
    <input type="submit" name="fileUploadSubmit"  value="File Upload"  /> 
</form> 
</div>
</body>

<!--
    <input type="file" name="fileToUpload"  id="fileToUpload"  accept=".csv,.xls,.txt" "> 
    <input type="submit" value="Upload file" name="submit"><br/><br/>
-->