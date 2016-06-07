<?php

session_start(); 

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
function computeBearing( $lat1_d, $lon1_d, $lat2_d, $lon2_d )
{
/***
   $lat1 = deg2rad($lat1_d);
   $long1 = deg2rad($lon1_d);
   $lat2 = deg2rad($lat2_d);
   $long2 = deg2rad($lon2_d);
   ***/
   
   // echo (' starting compute bearing: ' . $lat1_d . " " . $lon1_d . " " . $lat2_d . " " . $lon2_d . "<br>");

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
       $angleAdjust = 0; // Q3: adjust by +180
   }      
   if ($lonDiff > 0 && $latDiff <= 0) {
       $angleAdjust = 0; // Q4: adjust by +360 
   }
   
   // echo ('adjustment to angle : ' . $angleAdjust . "<br>");
   
   // calculate the adjusted angle
   $bearingdegrees = $bearingdegrees + $angleAdjust;
   // echo ('bearingdegrees after adjustment : ' . $bearingdegrees . "<br>");
   
   return $bearingdegrees;
   
};

?>