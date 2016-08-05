<?php
include './generalFunctions.php';

// $file = fopen("test.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("test_" . $fileTimestamp . ".txt","a");
fwrite($file,"Hello World. Testing! \n");


$db=getDbConnection();
if ($db->connect_error) {
	echo '<p>connection failed</p>';
	fwrite($file,"connection failed \n");
}

$userName=$_COOKIE["userName"];


$timestamp = date('m/d/Y h:i:s');
// fwrite($file,"Hello World. Testing! \n");
fwrite($file, '['.$timestamp.']: ' . "user name: " . $userName . "\n");




//else echo 1;
if (is_ajax()) {
// echo '<p>running update settings</p>';
$timestamp = date('m/d/Y h:i:s');

  	if (isset($_POST["func"]) && ($_POST["func"]=='updateSettings')) { //Checks if action value exists to update user settings
  	        if($_POST["zoning"]=='true') {$userZoning='Y';} else {$userZoning= 'N';};
  	        $loadingTime=$_POST["loadingTime"];	  		  	
	  	$loadingTimeDisabled=$_POST["loadingTimeDisabled"];	  	
	  	$timeLimitPickup=$_POST["timeLimitPickup"];
	  	$timeLimitRelease=$_POST["timeLimitRelease"];
	  	$quadrantTilt = $_POST["quadrantTilt"];
	  	$LOB=$_POST["LOB"];

	  	$query ="UPDATE `user_settings`
	  	        SET `zoning` = '$userZoning', 
	  	            `loading_time` = $loadingTime,
	  	            `loading_time_disabled` = $loadingTimeDisabled,
	  	            `time_limit_pickup` = $timeLimitPickup,
	  	            `time_limit_release` = $timeLimitRelease,
	  	            `LOB` = '$LOB',
	  	            `quadrant_tilt` = '$quadrantTilt'
	  	        WHERE `user_name` = '$userName' ";

	  	$timestamp = date('m/d/Y h:i:s');       
	  	fwrite($file,'['.$timestamp.']: ' .$query . "\n");
	  	// echo $query;
	        $result = mysqli_query($db, $query);
	        fclose($file);
	        echo 1;
  
  	}   // update user settings
  	
  	if (isset($_POST["func"]) && ($_POST["func"]=='saveNewSchool')) { 
  	    if($_POST["firstGrade"]=='true') {$firstGrade='Y';} else {$firstGrade= 'N';};
	    if($_POST["secondGrade"]=='true') {$secondGrade='Y';} else {$secondGrade= 'N';};
	    if($_POST["thirdGrade"]=='true') {$thirdGrade='Y';} else {$thirdGrade= 'N';};
	    if($_POST["fourthGrade"]=='true') {$fourthGrade='Y';} else {$fourthGrade= 'N';};
	    if($_POST["fifthGrade"]=='true') {$fifthGrade='Y';} else {$fifthGrade= 'N';};
	    if($_POST["sixthGrade"]=='true') {$sixthGrade='Y';} else {$sixthGrade= 'N';};
	    if($_POST["seventhGrade"]=='true') {$seventhGrade='Y';} else {$seventhGrade= 'N';};
	    if($_POST["eighthGrade"]=='true') {$eighthGrade='Y';} else {$eighthGrade= 'N';};
	    if($_POST["ninthGrade"]=='true') {$ninthGrade='Y';} else {$ninthGrade= 'N';};
	    if($_POST["tenthGrade"]=='true') {$tenthGrade='Y';} else {$tenthGrade= 'N';};
	    if($_POST["eleventhGrade"]=='true') {$eleventhGrade='Y';} else {$eleventhGrade= 'N';};
	    if($_POST["twelfthGrade"]=='true') {$twelfthGrade='Y';} else {$twelfthGrade= 'N';};
	    $schoolName=$_POST["schoolName"];
  	    $schoolAddress=$_POST["schoolAddress"];
  	    $schooLAT=$_POST["lat"];
  	    $schooLNG=$_POST["lng"];
	   
	    
	    $query ="INSERT INTO`user_schools`
	  	        SET `user_name` = '$userName',
	  	            `school_name` = '$schoolName',
	  	            `school_address` = '$schoolAddress',
	  	            `first_grade` = '$firstGrade',
	  	            `second_grade` = '$secondGrade',
	  	            `third_grade` = '$thirdGrade',
	  	            `fourth_grade` = '$fourthGrade',
	  	            `fifth_grade` = '$fifthGrade',
	  	            `sixth_grade` = '$sixthGrade',
	  	            `seventh_grade` = '$seventhGrade',
	  	            `eighth_grade` = '$eighthGrade',
	  	            `ninth_grade` = '$ninthGrade',
	  	            `tenth_grade` = '$tenthGrade',
	  	            `eleventh_grade` = '$eleventhGrade',
	  	            `twelfth_grade` = '$twelfthGrade',
	  	            `lat` = '$schooLAT',
	  	            `lng` = '$schooLNG'";
	  	        
	  	        
  	$timestamp = date('m/d/Y h:i:s');       
	  	fwrite($file,'['.$timestamp.']: ' .$query . "\n");
	  	// echo $query;
	        $result = mysqli_query($db, $query);
	        fclose($file);
	        echo 1;
	        
  	}  	
  	
  	if (isset($_POST["func"]) && ($_POST["func"]=='deleteSchool')) { 
	    $schoolDeleteName=$_POST["schoolDeleteName"];
	    
    	    // $query = "DELETE FROM `user_schools` where `user_name` = '$userName' AND `school_name` = '$schoolDeleteName'";
    	    $query = "DELETE FROM `user_schools` where `user_name` = 'jfdksa' AND `school_name` = '$schoolDeleteName'";
  	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
	    fclose($file);
	    echo 1;  	      	
  	} // deleteSchool


if (isset($_POST["func"]) && ($_POST["func"]=='saveNewStudent')) { 

            $schoolName=$_POST["schoolName"];
            
         // get school lat, lng
        $query = "SELECT * FROM `user_schools` WHERE `user_name`='$userName' AND `school_name`='$schoolName';";
        $result = mysqli_query($db, $query);
        $row=mysqli_fetch_array($result);
        // print_r($row);
        $schoolLat = $row["lat"];
        $schoolLng = $row["lng"];  	
        
 	    $studentName=$_POST["studentName"];
  	    $studentAddress=$_POST["studentAddress"];
  	    $studentGrade=$_POST["studentGrade"];
  	    $studentSpecialNeeds=$_POST["studentSpecialNeeds"];
  	    $studentLAT=$_POST["lat"];
  	    $studentLNG=$_POST["lng"];
  	    $studentQuadrant = getQuadrant($userName, $schoolName, $studentLAT, $studentLNG);
	    $distanceToSchool =  $_POST["distToSchool"];     
	    $timeToSchool =  $_POST["timeToSchool"];
	    // calculate air distance to school

	    $airDistanceToSchool = getAirDistance($studentLAT, $studentLNG, $schoolLat, $schoolLng);  
	      
	    
	    $query ="REPLACE INTO`students` 
	  	        SET `school_name` = '$schoolName',
	  	            `student_name` = '$studentName',
	  	            `student_address` = '$studentAddress',
	  	            `student_grade` = '$studentGrade',
	  	            `student_special_needs` = '$studentSpecialNeeds',
	  	            `lat` = '$studentLAT',
	  	            `lng` = '$studentLNG',
	  	            `quadrant` = '$studentQuadrant',
	  	            `student_group` = '$studentQuadrant',         
	  	            `distance_to_school` = '$distanceToSchool',
	  	            `time_to_school` = '$timeToSchool',
	  	            `air_distance_to_school` = '$airDistanceToSchool'
	  	             ";
	  	             
	  	               	    
	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
	    echo 1;
  }    // save new student
  
if (isset($_POST["func"]) && ($_POST["func"]=='saveNewFleet')) { 
            $vehicleName=$_POST["vehicleName"];
	    $vehicleID=$_POST["vehicleID"];
	    
	    
	    $query ="REPLACE INTO`fleet` 
	  	        SET `user_name` = '$userName',
	  	            `vehicle_name` = '$vehicleName',
	  	            `vehicle_id` = '$vehicleID'
	  	            ";
	  	             
	  	               	    
	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
	    echo 1;
  }   // save new vehicle
  
if (isset($_POST["func"]) && ($_POST["func"]=='saveNewDriver')) { 
            $driverID=$_POST["driverID"];
	    $driverCell=$_POST["driverCell"];
	    
	    $query ="REPLACE INTO`drivers` 
	  	        SET `user_name` = '$userName',
	  	        `driver_id` = '$driverID',
	  	         `driver_cell` = '$driverCell'";
	  	             
	  	               	    
	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
	    echo 1;
  }   // save new driver
  
if (isset($_POST["func"]) && ($_POST["func"]=='saveNewVehicleType')) { 
            $vehicleName=$_POST["vehicleName"];
	    $vehicleCapacity=$_POST["vehicleCapacity"];
	    $vehicleSpecialNeeds=$_POST["vehicleSpecialNeeds"];
	    
	    $query ="REPLACE INTO`vehicle_type` 
	  	        SET `vehicle_name` = '$vehicleName',
	  	        `vehicle_capacity` = '$vehicleCapacity',
	  	         `special_needs` = '$vehicleSpecialNeeds'";
	  	             
	  	               	    
	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
	    echo 1;
  }   // save new fleet type
  
  
if (isset($_POST["func"]) && ($_POST["func"]=='updateStudentGroup')) { 
        $schoolName=$_POST["schoolName"];
	    $studentName=$_POST["studentName"];
	    $studentNewGroup=$_POST["studentNewGroup"];
	    
	    $query ="UPDATE `students` 
	  	        SET `student_group` = '$studentNewGroup'  WHERE `school_name` = '$schoolName' AND `student_name` = '$studentName'";
	  	             
	  	               	    
	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
		if($result){
			echo '1';
		}
	    
  }   // update student group manually 
  
}    // if is_ajax

else {
fwrite($file,"did not run ajax");
fclose($file);
echo 0;
}

if (isset($_POST["func"]) && ($_POST["func"]=='insertBusStop')) { 
  
 
  
        $schoolName=$_POST["schoolName"];
	    $userName=$_POST["userName"];
  	    $lat=$_POST["lat"];
  	    $lng=$_POST["lng"];
  	    $desc=$_POST["desc"];
	   
	    
	    $query ="INSERT INTO `school_bus_stops`(`id`, `user_name`, `shcool_name`, `lat`, `lng`, `description`) VALUES ('','".$userName."','".$schoolName."','".$lat."','".$lng."','".$desc."')";
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
		
        $result = mysqli_query($db, $query);
		if($result){
			echo '1';
		}
		
  die();
}

if (isset($_POST["func"]) && ($_POST["func"]=='updateBusStop')) { 

        $schoolName=$_POST["schoolName"];
	    $userName=$_POST["userName"];
  	    $lat=$_POST["lat"];
  	    $lng=$_POST["lng"];
  	    $newDesc=$_POST["desc"];
		$id = $_POST['id'];
		
		 $query ="UPDATE `school_bus_stops` 
	  	        SET `user_name` = '$userName', `shcool_name` = '$schoolName', `lat` = '$lat', `lng` = '$lng'  , `description` = '$newDesc' WHERE `id` = '$id'";
	  	             
	  	               	    
	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
		if($result){
			echo '1';
		}
  
}
//delete bus stop
if (isset($_POST["func"]) && ($_POST["func"]=='deleteBusStop')) { 

    //echo '<pre>';print_r($_POST); echo '</pre>';
	
    $id = $_POST['id'];
    $query ="DELETE FROM `school_bus_stops` WHERE id='$id'";
    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
		if($result){
			echo '1';
		}

 die();

}

if (isset($_POST["func"]) && ($_POST["func"]=='saveNewStudent')) { 

            $schoolName=$_POST["schoolName"];
            
         // get school lat, lng
        $query = "SELECT * FROM `students` WHERE `school_name`='$schoolName' AND `student_name`='$studentName' AND 'quadrant' = '1';";
        $result = mysqli_query($db, $query);
        $row=mysqli_fetch_array($result);
        // print_r($row);
        $schoolLat = $row["lat"];
        $schoolLng = $row["lng"];  	
        
// 	    $studentName=$_POST["studentName"];
//  	    $studentAddress=$_POST["studentAddress"];
//  	    $studentGrade=$_POST["studentGrade"];
//  	    $studentSpecialNeeds=$_POST["studentSpecialNeeds"];
//  	    $studentLAT=$_POST["lat"];
//  	    $studentLNG=$_POST["lng"];
  	    $studentGroup = getStudentGroupQI($userName, $schoolName, $schoolLat, $schoolLng, $studentLat, $studentLng);
//	    $distanceToSchool =  $_POST["distToSchool"];     
//	    $timeToSchool =  $_POST["timeToSchool"];	    
	    
/*	    $query ="REPLACE INTO`students` 
	  	        SET `school_name` = '$schoolName',
	  	            `student_name` = '$studentName',
	  	            `student_address` = '$studentAddress',
	  	            `student_grade` = '$studentGrade',
	  	            `student_special_needs` = '$studentSpecialNeeds',
	  	            `lat` = '$studentLAT',
	  	            `lng` = '$studentLNG',
  	                    `quadrant` = '$studentQuadrant',
	  	            `student_group` = '$studentGroup',         
	  	            `distance_to_school` = '$distanceToSchool',
	  	            `time_to_school` = '$timeToSchool'
	  	             ";
*/	  	   

	$query ="REPLACE INTO`students` 
	  	        SET `student_group` = '$studentGroup',";          
	  	               	    
	    $timestamp = date('m/d/Y h:i:s');       
	    fwrite($file,'['.$timestamp.']: ' . $query . "\n");
	    $result = mysqli_query($db, $query);
	    echo 1;
  }    // update student group automatically
  

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

?>