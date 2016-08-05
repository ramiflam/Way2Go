<?php

session_start(); 


// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("func_" . $fileTimestamp . ".txt","a");

function getDbConnection(){
   $db = mysqli_connect('localhost','xmatchge','$Dlior)0p','xmatchge_way2go');
   mysqli_query($db, "SET NAMES 'utf8'");
   return $db;
}

function closeDbConnection($db){
    mysqli_close($db);
}

function validateLogin($db, $userName, $userPassword){
    $msg='name exists';
    $sql="SELECT * FROM `user_details` WHERE user_name='$userName' and user_password='$userPassword'";
    $result = mysqli_query($db, $sql);
    $loginStatus = 'failed';
    
    if($_SERVER['REQUEST_METHOD']=='POST'){
    if($result->num_rows == 1){	  
	return true;
    }
    
    else{
        return false;
    }
    }
           	   
    return $loginStatus;

    exit;
}

// compute bearing(angle) in degress between 2 locations
function computeBearing( $lat1_d, $lon1_d, $lat2_d, $lon2_d ){
/***
   $lat1 = deg2rad($lat1_d) ; $long1 = deg2rad($lon1_d);
   $lat2 = deg2rad($lat2_d) ; $long2 = deg2rad($lon2_d);
***/
   
   // echo (' starting compute bearing: ' . $lat1_d . " " . $lon1_d . " " . $lat2_d . " " . $lon2_d . "<br>");
   fwrite($file, $timestamp . "starting compute bearing: " . $lat1_d . " " . $lon1_d . " " . $lat2_d . " " . $lon2_d . "\n");

   // calculate differences in lat and long between student and school
   $lonDiff = ($lon2_d - $lon1_d );
   // // echo ("longitude diff: " . $lonDiff . "<br>");
   $latDiff = ($lat2_d - $lat1_d );
   // echo ("latitude diff: " . $latDiff . "<br>"); 
   
   // calculate angle between student and school
   if ($lonDiff == 0) { 
       if ($latDiff > 0) {
           $bearingdegrees = 90; // no change in lng, positive change in lat
       }
       if ($latdif < 0) {
           $bearingdegrees = 270; // no change in lng, negative change in lat
       }
   }
   $bearingradians = atan(($latDiff)/($lonDiff));
   // echo ('bearingradians : ' . $bearingradians . "<br>");
   $bearingdegrees = rad2deg($bearingradians);
   // echo ('bearingdegrees : ' . $bearingdegrees . "<br>");
   $bearingdegrees = round($bearingdegrees);
   // echo ('bearingdegrees rounded : ' . $bearingdegrees . "<br>");
   
   // set adjustment of angle needed based on quadrant
   if ($lonDiff > 0 && $latDiff >= 0) {
       $angleAdjust = 0; // Q1: no adjustment needed
   }
   if ($lonDiff < 0 && $latDiff >= 0) {
       $angleAdjust = 180; // Q2: adjust by +180
   }
   if ($lonDiff < 0 && $latDiff <= 0) {
       $angleAdjust = 180; // Q3: adjust by +180
   }      
   if ($lonDiff > 0 && $latDiff <= 0) {
       $angleAdjust = 360; // Q4: adjust by +360 
   }
   
   // echo ('adjustment to angle : ' . $angleAdjust . "<br>");
   
   // calculate the adjusted angle
   $bearingdegrees = $bearingdegrees + $angleAdjust;
   // echo ('bearingdegrees after adjustment : ' . $bearingdegrees . "<br>");
   
   return $bearingdegrees;
   
};

function getQuadrant($userName, $shcoolName, $lati , $longi) {
        // calculate quadrant compared to school lat and lng
        //  2 | 1
        //  3 | 4
        // $timestamp = date('m/d/Y h:i:s');   
        
        // get school lat, lng
        $query = "SELECT * FROM `user_schools` WHERE `user_name`='$userName' AND `school_name`='$schoolName';";
        $result = mysqli_query($db, $query);
        $row=mysqli_fetch_array($result);
        // print_r($row);
        $schoolLat = $row["lat"];
        $schoolLng = $row["lng"];        
        
        // calculate bearing between shcool and student address
        $studentBearing = computeBearing($schoolLat, $schoolLng, $lati , $longi);

    	// get quadrant tilt from user_settings table
	    $query = "SELECT * FROM `user_settings` WHERE `user_name`='$userName';";
	    $result = mysqli_query($db, $query);
	    $row=mysqli_fetch_array($result);
	    // print_r($row);
	    $quadrantTilt= $row["quadrant_tilt"];        
        

        // echo (" quadrant tilt is: " . $quadrantTilt . " degrees <br>");
        $quadrantBoundaryI = 0 + $quadrantTilt;
        $quadrantBoundaryII = 90 + $quadrantBoundaryI;
        $quadrantBoundaryIII = 180 + $quadrantBoundaryI;
        $quadrantBoundaryIV = 270 + $quadrantBoundaryI;
        
        if ($studentBearing >= $quadrantBoundaryI && $studentBearing < $quadrantBoundaryII){
            $studentQuadrantShifted = 1;
        }
        if ($studentBearing >= $quadrantBoundaryII  && $studentBearing < $quadrantBoundaryIII){
            $studentQuadrantShifted = 2;
        }
        if ($studentBearing >= $quadrantBoundaryIII && $studentBearing < $quadrantBoundaryIV){
            $studentQuadrantShifted = 3;
        }
        if ($studentBearing >= $quadrantBoundaryIV){
            $studentQuadrantShifted = 4;
        }
        
        // echo (" quadrant with shift is: " . $studentQuadrantShifted . "<br><br>");
        
        return $studentQuadrantShifted;
};

function getTimeAndDistance($userName, $schoolName, $studentLat, $studentLng) {

        // get school lat, lng
        $query = "SELECT * FROM `user_schools` WHERE `user_name`='$userName' AND `school_name`='$schoolName';";
        $result = mysqli_query($db, $query);
        $row=mysqli_fetch_array($result);
        // print_r($row);
        $schoolLat = $row["lat"];
        $schoolLng = $row["lng"];    

        // calculate distance and time from student to school
       $urlDetails = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$studentLat,$studentLng&destinations=$schoolLat,$schoolLng&mode=driving&sensor=false";
       $jsonResp = file_get_contents($urlDetails);
       $respDetails = json_decode($jsonResp, TRUE);
       $timeDist = array();
       $timeDist['dist'] = $respDetails['rows'][0]['elements'][0]['distance']['value'];
       $timeDist['time'] = $respDetails['rows'][0]['elements'][0]['duration']['value'];   
       echo ("time is: " . $$timeDist['time'] . "Dist is: " . $timeDist['dist'] . "\n" );
 
       
       return $result;
};


function getStudentGroupQI($userName, $schoolName, $schoolLat, $schoolLng, $studentLat, $studentLng  ) {

        // get school lat, lng
        $query = "SELECT * FROM `user_schools` WHERE `user_name`='$userName' AND `school_name`='$schoolName';";
        $result = mysqli_query($db, $query);
        $row=mysqli_fetch_array($result);
        // print_r($row);
        $schoolLat = $row["lat"];
        $schoolLng = $row["lng"];    
        
        // get student lat, lng
        $query = "SELECT * FROM `students` WHERE `school_name`='$schoolName' AND 'quadrant' = '1';";
        $result = mysqli_query($db, $query);
        $row=mysqli_fetch_array($result);
        // print_r($row);
        $studentLat = $row["lat"];
        $studentLng = $row["lng"];    
        
        // calculate diff in lat and lng between school and student for each student in QI
        $latDiffQI = abs($studentLat - $schoolLat);
        $lngDiffQI = abs($studentLng - $schoolLng);
        
        //array of all lat Diff between school and each student in QI
        $latDiffArrayQI = array();
        $latDiffArrayQI = abs($studentLat - $schoolLat);
        
        //array of all lng Diff between school and each student in QI
        $lngDiffArrayQI = array();
        $lngDiffArrayQI = abs($studentLng - $schoolLng);
        
        //calculate Lat and Lng Boundary for QI
        $boundaryLatQI = max($latDiffArrayQI);
        $boundaryLngQI = max($lngDiffArrayQ1);
        
        //calculate total area of quadrant
        $quadAreaLatQI = $boundaryLatQI -$schoolLat;
        $quadAreaLngQI = $boundaryLngQI -$schoolLng;
        
        //# of sections to divide by
        $latSections = 2;
        $lngSections = 2;
        
        //define area of groups(latdiff and lng diff per group)
        $groupAreaLatQI = $quadAreaLatQI / $latSections;
        $groupAreaLngQI = $quadAreaLngQI / $lngSections;
 
       
       return $result;
};

function getAirDistance($lat1, $lon1, $lat2, $lon2)  {

// reutrns shortest distance between 2 locations in meters

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  
  $km = $miles * 1.609344;
  $metersDist = round($km * 1000);
  
  return $metersDist;


}; // getAirDistance


?>