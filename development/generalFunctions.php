<?php
session_start(); 
// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");

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
   
}
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
}

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
 
       
       return $timeDist;
}


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
} // getAirDistance




         function studentComp($a, $b) {
         
	         if ($a['student_group'] != $b['student_group']){
	           $val = ($a['student_group'] < $b['student_group']) ? -1 : 1;
	           return $val;
	         }
	         else {
	         if ($a['bearing'] == 0)
	            $a['bearing'] = 1;
	         if ($b['bearing'] == 0)
	            $b['bearing'] = 1;
	            
	           // calulate relative bearing (split 90 degress into 3 areas)
	           /***
	           If (($a['bearing'] >= 1*$a['quadrant']) && ($a['bearing'] < 30*$a['quadrant']))
	               $aBearing = 10;
	           If (($a['bearing'] >= 30*$a['quadrant']) && ($a['bearing'] < 60*$a['quadrant']))
	               $aBearing = 20;
	           If (($a['bearing'] >= 60*$a['quadrant']) && ($a['bearing'] < 90*$a['quadrant']))
	               $aBearing = 30;

	           If (($b['bearing'] >= 1*$b['quadrant']) && ($b['bearing'] < 30*$b['quadrant']))
	               $bBearing = 10;
	           If (($b['bearing'] >= 30*$b['quadrant']) && ($b['bearing'] < 60*$b['quadrant']))
	               $bBearing = 20;
	           If (($b['bearing'] >= 60*$b['quadrant']) && ($b['bearing'] < 90*$b['quadrant']))
	               $bBearing = 30;
***/	               
	           // if group is same then return distance based compariosn (air + driving devided by bearing)
	           $aFactor = $a['bearing']/$a['student_group'];
	           $bFactor = $a['bearing']/$b['student_group'];
	          
	           $aDist =  ($a['air_distance_to_school'] + $a['distance_to_school'])/$aFactor;
	           $bDist =  ($b['air_distance_to_school'] + $b['distance_to_school'])/$bFactor;
	           if ($aDist == $bDist)  {
	              return 0;
	           }  else {
	            return ($aDist < $bDist) ? -1 : 1;
	           }
	         }
         }
         
         
         

function getStudentGroupQI($db, $userName, $schoolName, $schoolLat, $schoolLng, $latSections, $lngSections, $maxStudentsPerGroup, $quad ) {

$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");

// fwrite($genFuncfile, " in getStudentGroupQI..." . "\n");


        // set up 3 arrays
        $studentsArray = array();
        $latDiffArray = array();
        $lngDiffArray = array();
        
        // get student lat, lng
        
        $query = "SELECT * FROM `students` WHERE `school_name`='$schoolName' AND `quadrant` = '$quad' AND `student_special_needs` = 'N';";
        
        fwrite($genFuncfile, " query is:" . $query ."\n");
        
        $result = mysqli_query($db, $query);
        // $row=mysqli_fetch_array($result);

        $boundaryLatQI = 0;
        $boundaryLngQI = 0;
      
        while ($row=mysqli_fetch_array($result)) {
           $studentRow = $row;
           $studentRow['student_group'] = 1;  // initialize to 0
           $studentsArray[] = $studentRow;
           $latDiffQI = abs(abs($studentRow['lat']) - abs($schoolLat));
           $lngDiffQI = abs(abs($studentRow['lng']) - abs($schoolLng));
           if ($latDiffQI > $boundaryLatQI)
              $boundaryLatQI = $latDiffQI;
           if ($lngDiffQI > $boundaryLngQI)
              $boundaryLngQI = $lngDiffQI;
              
              
        }
 /**       
      ob_start();
      var_dump($studentsArray);
      $stDump = ob_get_clean();
      fwrite($genFuncfile,"students array beffore sort = " . $stDump . "\n" );
      **/
        
        
        //define area of groups(latdiff and lng diff per group)
        $groupAreaLatQI = $boundaryLatQI / $latSections;
        $groupAreaLngQI = $boundaryLngQI / $lngSections;
        
        // loop students array and assign internal quad
        $internalQuad = 0;
 
        $studentCount = count($studentsArray);
        fwrite($genFuncfile, "students count: " . $studentCount . " boundaryLatQI: "  . $boundaryLatQI . "  boundaryLngQI: " . $boundaryLngQI. " groupAreaLatQI: " . $groupAreaLatQI . " groupAreaLngQI: " . $groupAreaLngQI  .  "\n" ); 
        
        for ($i=0; $i<count($studentsArray); $i++) {
           $latDiffQI = abs(abs($studentsArray[$i]['lat']) - abs($schoolLat));
           $lngDiffQI = abs(abs($studentsArray[$i]['lng']) - abs($schoolLng));

           // calcalute internal quad
           // $studentRow = $studentArray[$i];
           if (($latDiffQI > $groupAreaLatQI) &&  ($lngDiffQI > $groupAreaLngQI)) {
                 // internal quad 1
                 $studentsArray[$i]['student_group'] = 1;
              }
           if (($latDiffQI <= $groupAreaLatQI) &&  ($lngDiffQI > $groupAreaLngQI)) {
                 // internal quad 4
                 $studentsArray[$i]['student_group'] = 4;
              }
           if (($latDiffQI <= $groupAreaLatQI) &&  ($lngDiffQI <= $groupAreaLngQI)) {
                 // internal quad 3
                 $studentsArray[$i]['student_group'] = 3;
              }
           if (($latDiffQI > $groupAreaLatQI) &&  ($lngDiffQI <= $groupAreaLngQI)) {
                 // internal quad 2
                 $studentsArray[$i]['student_group'] = 2;
              }
              
              
             // fwrite($genFuncfile, $i . "- lat: "  . $studentsArray[$i]['lat'] . "  lng: " . $studentsArray[$i]['lng'] . " latDiffQI: " . $latDiffQI . " lngDiffQI: " . $lngDiffQI . " internal quad: " . $studentsArray[$i]['student_group'] . "\n" );           
         }
        
         // sort the array by student_group first and a combination of air distance and driving distance second 
         
         usort ($studentsArray, 'studentComp');
       
         fwrite($genFuncfile, "sorted array count: "  . count($studentsArray) . "\n" ); 
  /**        
 for ($i=0; $i<count($studentsArray); $i++) {
 
 $aDist =  ($studentsArray[$i]['air_distance_to_school'] + $studentsArray[$i]['distance_to_school'])/2;
 
 fwrite($genFuncfile, $i . " - " . $studentsArray[$i]['air_distance_to_school'] . ", " . $studentsArray[$i]['distance_to_school']  . ", " . $aDist . ", " . $studentsArray[$i]['student_group'] . "\n" ); 
    
}
 **/
 
 
       
      // for each local quad loop sorted array and build groups the size of input parameter $maxStudentsPerGroup
      $localGroupNo = 100 * $quad;
      $localQuad = 1;
      $localGroupCount = 0;
        for ($i=0; $i<count($studentsArray); $i++) {
        
        fwrite($genFuncfile, $i . " - group before: " . $studentsArray[$i]['student_group'] . " localGroupNo:" . $localGroupNo . "\n" ); 
        
           // check if local quad changed and if so start the count again for new group
 /*     
           if ($studentsArray[$i]['student_group']  != $localQuad)  {
             $localGroupNo += 10;  // increase group no by 10
             $localQuad = $studentsArray[$i]['student_group'];
             $localGroupCount = 0;
           }
 */         
           
           $studentsArray[$i]['student_group'] = $localGroupNo;
           $localGroupCount++;
           
           // if group count exceeds limit while still in local quad just set new group number
           if ($localGroupCount >= $maxStudentsPerGroup) {
              $localGroupNo += 10;
              $localGroupCount = 0;  //reset group count
           }
           
    fwrite($genFuncfile, $i . " - group after: " . $studentsArray[$i]['student_group'] . " localGroupNo:" . $localGroupNo . "\n" );       

         }

         
 /**        
      ob_start();
      var_dump($studentsArray);
      $stDump = ob_get_clean();
      fwrite($genFuncfile,"students array = " . $stDump . "\n" );         
**/
         
         // now update student group in DB             
         for ($i=0; $i<count($studentsArray); $i++) {
         $sGroup = $studentsArray[$i]['student_group'];
         $sName = $studentsArray[$i]['student_name'];
            $query = "UPDATE `students` SET `student_group` = '$sGroup' WHERE `school_name`='$schoolName' AND `student_name` = '$sName' ;";
                  fwrite($genFuncfile, $query . "\n" );      
            $result = mysqli_query($db, $query);
         }
       
       return 1;
} 



?>