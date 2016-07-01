<?php
include './generalFunctions.php';
$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}

$userName=$_COOKIE["userName"];
// echo ("User Name: " . $userName ."\n");

// $schoolName= $_GET["schoolName"];

// echo (" schoolName = " . $schoolName );



// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("debug_" . $fileTimestamp . ".txt","a");
fwrite($file,"In driverUpload.  Testing! \n");

if(!$_FILES['csv']['name'])
{
    echo 'No file selected<br>';
       //header('Location: fileLoad.php');
       return;
} else  {
$newFileName = $_FILES['csv']['name'];
// echo ('New file selected: ' . $newFileName  );
}

// echo ('New File Name: ' . $newFileName);
 $uploadOk = 1;
 $target_path= "../uploads/";
    
	$targetFileName =  $target_path . $newFileName ;
	// echo ("Target File Name: " . $targetFileName) ;
	//echo ("check file " . . " " ); 
	if (file_exists($targetFileName  )) {
	    echo 'Sorry, file &nbsp;' .  $targetFileName . '&nbsp; already exists.';
	    $uploadOk = 0;
}

if($uploadOk){
 
   	    $timestamp = date('m/d/Y h:i:s'); 
    	    
	    fwrite($file, $timestamp . "upload file : " . $_FILES['csv']['tmp_name'] . " to " . $targetFileName . "\n");
	    // echo ("upload file : " . $_FILES['csv']['tmp_name'] . " to " . $targetFileName );
   
     if (move_uploaded_file($_FILES['csv']['tmp_name'] , $targetFileName  )) {
        echo "The file was uploaded successfully.";
      } else {
        echo "The file was not uploaded successfully.";
        return;
      }

 

}

// process drivers csv file
if ($_FILES['csv']['size'] > 0) { 

 
    //get the csv file 
    $handle = fopen($targetFileName,"r"); 
    echo ("Starting input file processing <br>");
     
    //loop through the csv file and insert into database 
    $ndx = 0;
    $header = NULL;
    
    while ($row= fgetcsv($handle,1000,",")) { 
    if (!$header) {
       $header = $row;
    }
    else {
   	 $data = array_combine($header, $row);
    }
    
    // print_r($data);
    
    // do not process the header row
    if ($ndx > 0) {

        $driverID = $data['driver_id'];
        $driverCell = $data['driver_cell'];
        
        fwrite($file, "Processing record no " . $ndx . " data is " . $driverID . "," . $driverCell . "\n" );
        

            $query ="REPLACE INTO `drivers`
	  	        SET `user_name` = '$userName',
	  	        `driver_id` = '$driverID',
	  	         `driver_cell` = '$driverCell'
	  	          ";
	  	        
	  	        
  	        $timestamp = date('m/d/Y h:i:s');       
	  	fwrite($file,'['.$timestamp.']: ' .$query . "\n");
	  	// echo $query;
	        $result = mysqli_query($db, $query);

    
     }  // ($ndx>0) 
        
     $ndx++;   
    } ; 
  
    $timestamp = date('m/d/Y h:i:s');       
    fwrite($file,'['.$timestamp.']: Uploaded ' . $ndx-1 . ' drivers' . "\n");   
    fclose($file); 
}

// return to students setting page
 header('location:fleetsDriversPage.php');

?>