<?php
include './generalFunctions.php';
$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}

$userName=$_COOKIE["userName"];
// echo ("User Name: " . $userName ."\n");

$schoolName= $_GET["schoolName"];

echo (" schoolName = " . $schoolName );



// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("../logs/debug_" . $fileTimestamp . ".txt","a");
fwrite($file,"In studentUpload.  Testing! \n");
$queryFile = fopen("query_" . $fileTimestamp . ".txt","a");

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
   // $targetFileName = $target_path . $uploadFileName  ;
     if (move_uploaded_file($_FILES['csv']['tmp_name'] , $targetFileName  )) {
        echo "The file was uploaded successfully.";
      } else {
        echo "The file was not uploaded successfully.";
        return;
      }

 

}

     $timestamp = date('m/d/Y h:i:s'); 
     fwrite($file,'['.$timestamp.']: Starting Students Upload ' . "\n"); 

// process students csv file
if ($_FILES['csv']['size'] > 0) { 

    // get school info for later quadrant calculations
    $query = "SELECT * FROM `user_schools` WHERE `user_name`='$userName' AND `school_name`='$schoolName';";
    $result = mysqli_query($db, $query);
    $row=mysqli_fetch_array($result);
    // print_r($row);
    $schoolLat = $row["lat"];
    $schoolLng = $row["lng"];
    // echo ("school data " . $schoolLat . " " . $schoolLng . "<br>");
    // fwrite($file,'['.$timestamp.']: schoolLng, schoolLat ' . $schoolLng . " " . $schoolLat . "\n");
    
    // get quadrant tilt from user_settings table
    $query = "SELECT * FROM `user_settings` WHERE `user_name`='$userName';";
    $result = mysqli_query($db, $query);
    $row=mysqli_fetch_array($result);
    // print_r($row);
    $quadrantTilt= $row["quadrant_tilt"];
    

    
    

    //get the csv file 
    // $studentsFile = $targetFileName; 
    $handle = fopen($targetFileName,"r"); 
    echo ("Starting input file processing for" . $targetFileName .  "<br>");
     
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
        $studentEncodedAddress = urlencode($data['address']);
        $studentAddress = $data['address'];
        $studentGrade = $data['grade'];
        $studentName = $data['name'];
        $studentSpecialNeeds = $data['spcial needs'];
        
        echo ("student name " . $studentName . "<br>");
         echo ("Processing record no " . $ndx . " data is " . $studentAddress . "," . $studentGrade. "," . $studentName . "," . $studentSpecialNeeds . " <br>" );
        
        $url = "http://maps.google.com/maps/api/geocode/json?address={$studentEncodedAddress}";
        $resp_json = file_get_contents($url);
        $resp = json_decode($resp_json, true);
        // echo ("url is " . $url . " <br>");
        // print_r($resp_json);
        
        if($resp['status']=='OK') {
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        // $formatted_address = $resp['results'][0]['formatted_address'];
        //echo ("lati = " . $lati . " <br>");
        //echo ("longi = " . $longi . " <br>");
        
        // calculate quadrant compared to school lat and lng
        //  2 | 1
        //  3 | 4
        $timestamp = date('m/d/Y h:i:s');   
        // calculate bearing between shcool and student address
        $studentBearing = computeBearing($schoolLat, $schoolLng, $lati, $longi);
	//fwrite($file,'['.$timestamp.']: Calculating quadrant. schoolLng, schoolLat, longi, lati ' . $schoolLng . " " . $schoolLat . " " . $longi . " " . $lati . "\n");
	//fwrite($file,'['.$timestamp.']: student bearing is: ' . $studentBearing . "\n");
	
        $lngDiff = ($schoolLng - $longi);
        
        // echo ("longitude diff: " . $lngDiff . "<br>");
        
        
        // calculate student quadrant without shift
       
        // echo (" quadrant without shift is: " . $studentQuandrant . "<br>");
        
        // calculate student quadrant with shift
        // $quadrantShift = 80;
        // echo (" quadrant tilt is: " . $quadrantTilt . " degrees <br>");
        $quadrantBoundaryI = 0 + $quadrantTilt;
        $quadrantBoundaryII = 90 + $quadrantBoundaryI;
        $quadrantBoundaryIII = 180 + $quadrantBoundaryI;
        $quadrantBoundaryIV = 270 + $quadrantBoundaryI;
        
        // if bearing is 0 set it to 360
        if ($studentBearing == 0)
           $studentBearing = 360;
        
        if ($studentBearing > $quadrantBoundaryI && $studentBearing < $quadrantBoundaryII){
            $studentQuadrantShifted = 1;
        }
        if ($studentBearing > $quadrantBoundaryII  && $studentBearing <= $quadrantBoundaryIII){
            $studentQuadrantShifted = 2;
        }
        if ($studentBearing > $quadrantBoundaryIII && $studentBearing <= $quadrantBoundaryIV ){
            $studentQuadrantShifted = 3;
        }
        if ($studentBearing > $quadrantBoundaryIV || $studentBearing <= $quadrantBoundaryI){
            $studentQuadrantShifted = 4;
        }
 
         // fwrite($file,'['.$timestamp.']: ' . 'student bearing:' .$studentBearing . ' Quadrant boundaries: ' . $quadrantBoundaryI . ', ' .  $quadrantBoundaryII . ', ' . $quadrantBoundaryIII . ', ' . $quadrantBoundaryIV . ' quadrant shifted: ' . $studentQuadrantShifted . "\n");         
        // echo (" quadrant with shift is: " . $studentQuadrantShifted . "<br><br>");
        
        // set initial group to same as quadrant
        $studentGroup = $studentQuadrantShifted;
        if ($studentSpecialNeeds == 'Y')
          $studentGroup = 0;
        
        // calculate distance and time from student to school
       $urlDetails = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$lati,$longi&destinations=$schoolLat,$schoolLng&mode=driving&sensor=false";
       $jsonResp = file_get_contents($urlDetails);
       $respDetails = json_decode($jsonResp, TRUE);
       $dist = $respDetails['rows'][0]['elements'][0]['distance']['value'];
       $time2School = $respDetails['rows'][0]['elements'][0]['duration']['value'];    
       
       // get shortest (air) distance to school
       $airDistToSchool = getAirDistance($lati, $longi, $schoolLat, $schoolLng);
       
       
 /**
      ob_start();
      var_dump($respDetails);
      $timeDistDump = ob_get_clean();
      fwrite($file,"time and distance record = " . $timeDistDump . "\n" );
  **/  
        
        
        // verify if data is complete
        if($lati && $longi){
        
            $query ="REPLACE INTO `students`
	  	        SET `student_address` = '$studentAddress',
	  	            `student_name` = '$studentName',
	  	            `school_name` = '$schoolName',
	  	            `lat` = '$lati',
	  	            `lng` = '$longi',
	  	            `student_grade` = '$studentGrade',
	  	            `student_special_needs` = '$studentSpecialNeeds',
	  	            `quadrant` = '$studentQuadrantShifted',
	  	            `student_group` = '$studentGroup',
	  	            `distance_to_school` = '$dist',
	  	            `time_to_school` = '$time2School',
	  	            `air_distance_to_school` = '$airDistToSchool',
	  	            `bearing` = '$studentBearing'
	  	             ";
	  	        
  	        
  	        $timestamp = date('m/d/Y h:i:s');       
	  	fwrite($file, $query . "\n");
	  	// echo $query;
	        $result = mysqli_query($db, $query);
        }
      }  
     }  // ($ndx>0) 
        
     $ndx++;   
    } ; 
  
    $timestamp = date('m/d/Y h:i:s'); 
    $ndx--;      
    fwrite($file,'['.$timestamp.']: Uploaded ' . $ndx . ' students for ' . $schoolName . "\n");   
    fclose($file); 
}

// return to students setting page
 header('location:settingsStudentsPage.php?schoolNameSelect='. $schoolName);

?>