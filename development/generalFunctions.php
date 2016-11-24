<?php
session_start(); 
// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");

function getDbConnection(){
   $db = mysqli_connect('localhost','xmatchge','$Yuval)0p','xmatchge_way2go');
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
   
   // fwrite($file, $timestamp . "starting compute bearing: " . $lat1_d . " " . $lon1_d . " " . $lat2_d . " " . $lon2_d . "\n");
   // calculate differences in lat and long between student and school
   $lonDiff = ($lon2_d - $lon1_d );
   $latDiff = ($lat2_d - $lat1_d );
   
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
   $bearingdegrees = rad2deg($bearingradians);
   $bearingdegrees = round($bearingdegrees);
   
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
   
   
   // calculate the adjusted angle
   $bearingdegrees = $bearingdegrees + $angleAdjust;
   
   return $bearingdegrees;
   
}
function getQuadrant($userName, $schoolLat, $schoolLng, $lati , $longi) {
$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");
$db=getDbConnection();
        // calculate quadrant compared to school lat and lng
        //  2 | 1
        //  3 | 4
        // $timestamp = date('m/d/Y h:i:s');   
        

        // fwrite($genFuncfile, "getQuadrat: " . $userName . "," .  $schoolLat . "," . $schoolLng . "," . $lati  . "," . $longi . "\n");
        // calculate bearing between school and student address
        $studentBearing = computeBearing($schoolLat, $schoolLng, $lati , $longi);
    	// get quadrant tilt from user_settings table
	    $query = "SELECT * FROM `user_settings` WHERE `user_name`='$userName';";
	    // fwrite($genFuncfile,  $query . "\n");
	    $result = mysqli_query($db, $query);
	    $row=mysqli_fetch_array($result);
	    // print_r($row);
	    $quadrantTilt= $row["quadrant_tilt"];     
	    $quadNum = $row["quadrant_number"];   
	    
	    $angleShift = ceil(360/$quadNum);  // quadNum is either 3 or 4 so shift will be either 90 or 120 degrees
        // fwrite($genFuncfile, "Quadratnumber: " . $quadNum . " angle shift: " . $angleShift . " Tile: " . $quadrantTilt . "\n");
        $quadrantBoundaryI = 0 + $quadrantTilt;
        $quadrantBoundaryII = $angleShift + $quadrantBoundaryI;  // was 90 + $quadrantBoundaryI
        $quadrantBoundaryIII = $angleShift*2 + $quadrantBoundaryI;   // was 180 + $quadrantBoundaryI
        // only do if there are 4 quads
        if ($quadNum > 3) 
            $quadrantBoundaryIV = $angleShift*3 + $quadrantBoundaryI; // was 270 + $quadrantBoundaryI
         else {
         	$quadrantBoundaryIV = ($quadrantBoundaryI == 0) ? 360 : $quadrantBoundaryI-1;
         }
        
// fwrite($genFuncfile, "quadrantBoundaryI : " . $quadrantBoundaryI . " quadrantBoundaryII : " . $quadrantBoundaryII .  " quadrantBoundaryIII: " .  $quadrantBoundaryIII . " quadrantBoundaryIV: " . $quadrantBoundaryIV . "\n");
        
        if ($studentBearing >= $quadrantBoundaryI && $studentBearing < $quadrantBoundaryII){
            $studentQuadrantShifted = 1;
        }
        if ($studentBearing >= $quadrantBoundaryII  && $studentBearing < $quadrantBoundaryIII){
            $studentQuadrantShifted = 2;
        }
        if ($studentBearing >= $quadrantBoundaryIII && $studentBearing < $quadrantBoundaryIV){
            $studentQuadrantShifted = 3;
        }
        if ($studentBearing >= $quadrantBoundaryIV && ($quadNum  > 3)){
            $studentQuadrantShifted = 4;
        }
        
        
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
 
$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");   
   
	         if ($a['student_group'] != $b['student_group']){
	           $val = ($a['student_group'] < $b['student_group']) ? -1 : 1;
	           return $val;
	         }
	         else {
	         if ($a['bearing'] == 0)
	            $a['bearing'] = 1;
	         if ($b['bearing'] == 0)
	            $b['bearing'] = 1;
	            
	           // if group is same then return distance based compariosn (air + driving devided by bearing)
//	           $aFactor = $a['bearing']/$a['student_group'];
//	           $bFactor = $a['bearing']/$b['student_group'];

/**
	           $aFactor = $a['bearing']/3;
	           $bFactor = $a['bearing']/3;
	           $aDist =  ($a['air_distance_to_school'] +( $a['time_to_school'] * 1.5) )/$aFactor;
	           $bDist =  ($b['air_distance_to_school'] + ($b['time_to_school'] * 1.5) )/$bFactor;
**/
                   $aDistStr = strval($a['bearing']) . strval(	$a['time_to_school'] );           
                   $bDistStr = strval($b['bearing']) . strval(	$b['time_to_school'] ); 

         // fwrite($genFuncfile, " aDistStr:" . $aDistStr ." bDistStr:" . $bDistStr . "\n"); 
          
                          
                   $aDist = floatval($aDistStr/100);       
                   $bDist = floatval($bDistStr/100);       
          // fwrite($genFuncfile, " aDist:" . $aDist . " bDist:" . $bDist . "\n"); 
	           
	           if ($aDist == $bDist)  {
	              return 0;
	           }  else {
	            return ($aDist < $bDist) ? -1 : 1;
	           }
	         } // else
         } // studentComp
         
         
         

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
 //       fwrite($genFuncfile, "students count: " . $studentCount . " boundaryLatQI: "  . $boundaryLatQI . "  boundaryLngQI: " . $boundaryLngQI. " groupAreaLatQI: " . $groupAreaLatQI . " groupAreaLngQI: " . $groupAreaLngQI  .  "\n" ); 
        
        for ($i=0; $i<count($studentsArray); $i++) {
           $latDiffQI = abs(abs($studentsArray[$i]['lat']) - abs($schoolLat));
           $lngDiffQI = abs(abs($studentsArray[$i]['lng']) - abs($schoolLng));

           // calcalute internal quad
           // $studentRow = $studentArray[$i];
           if (($latDiffQI > $groupAreaLatQI) &&  ($lngDiffQI > $groupAreaLngQI)) {
                 // internal quad 1
                 $studentsArray[$i]['student_group'] = 4; // was 1
              }
           if (($latDiffQI <= $groupAreaLatQI) &&  ($lngDiffQI > $groupAreaLngQI)) {
                 // internal quad 4
                 $studentsArray[$i]['student_group'] = 2; // was 4
              }
           if (($latDiffQI <= $groupAreaLatQI) &&  ($lngDiffQI <= $groupAreaLngQI)) {
                 // internal quad 3
                 $studentsArray[$i]['student_group'] = 1; // was 3
              }
           if (($latDiffQI > $groupAreaLatQI) &&  ($lngDiffQI <= $groupAreaLngQI)) {
                 // internal quad 2
                 $studentsArray[$i]['student_group'] = 3; // was 2 
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
        
 //       fwrite($genFuncfile, $i . " - group before: " . $studentsArray[$i]['student_group'] . " localGroupNo:" . $localGroupNo . "\n" ); 
        
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
           
 //   fwrite($genFuncfile, $i . " - group after: " . $studentsArray[$i]['student_group'] . " localGroupNo:" . $localGroupNo . "\n" );       

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
                 // fwrite($genFuncfile, $query . "\n" );      
            $result = mysqli_query($db, $query);
         }
       
       return 1;
} 




function findNearest($initialStudentsArray, $quadrantTilt, $genFuncfile) {

        // find the closest student to shcool on the lowest bearing 
        $initialBearing = 361;
        $initialStudentDistance=100000;
        $pivotStudentNdx=0;
        
        for ($i=0; $i<count($initialStudentsArray); $i++) {
           //fwrite($genFuncfile, $i . " - dist to school: " . $initialStudentsArray[$i]['air_distance_to_school'] . "\n" );
           if (($initialStudentsArray[$i]['bearing'] + $quadrantTilt) < $initialBearing) {
              $pivotStudentNdx=$i;
              $initialStudentDistance= $initialStudentsArray[$i]['air_distance_to_school'];
              $initialBearing = $initialStudentsArray[$i]['bearing'];
           }
           else  if (($studentsArray[$i]['bearing'] + $quadrantTilt) == $initialBearing) {
              if ($initialStudentDistance > $initialStudentsArray[$i]['air_distance_to_school']) {
                 $pivotStudentNdx=$i;
                 $initialStudentDistance= $initialStudentsArray[$i]['air_distance_to_school'];
                 $initialBearing = $initialStudentsArray[$i]['bearing'];
              }
           }           
        }
        
        return $pivotStudentNdx;
}


function compByDist($a, $b) {
 if ($a['dist_from_pivot'] == $b['dist_from_pivot'])
    return 0;
 else 
    return ($a['dist_from_pivot'] < $b['dist_from_pivot']) ? -1 : 1;
} // compByDist


function studentGroupCluster($db, $userName, $schoolName, $schoolLat, $schoolLng, $maxStudentsPerGroup, $quad ) {

$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");

 fwrite($genFuncfile, " in studentGroupCluster..." . "\n");

	$query = "SELECT * FROM `user_settings` WHERE user_name='$userName';";
	
	$result = mysqli_query($db, $query);
	If ($result)
	   {
	    	$row = mysqli_fetch_assoc($result);
		// $returnMsg = $row["message"];
	   }
	   else return 0;
	
	$quadrantTilt = $row["quadrant_tilt"];

        // set up 2 arrays
        $initialStudentsArray = array();
        $finalStudentArray = array();
        
        // get student lat, lng
        
        $query = "SELECT * FROM `students` WHERE `school_name`='$schoolName' AND `quadrant` = '$quad' AND `student_special_needs` = 'N';";
        
        fwrite($genFuncfile, " query is:" . $query ."\n");
        
        $result = mysqli_query($db, $query);
        // $row=mysqli_fetch_array($result);

      
        while ($row=mysqli_fetch_array($result)) {
           $studentRow = $row;
           $studentRow['student_group'] = 1;  // initialize
           $studentRow['dist_from_pivot']=0.0;
           $initialStudentsArray[] = $studentRow;
        }
        
        $localGroupNo = 100 * $quad;
        $localQuad = 1;
        $localGroupCount = 0;        
	
	// find the closest student to shcool on the lowest bearing 
	$pivotStudentNdx = findNearest($initialStudentsArray, $quadrantTilt, $genFuncfile);

        // repeat of clustering students into groups
        $initialStudentArrayCount = count($initialStudentsArray);
        while ($initialStudentArrayCount > 0) {
        
                fwrite($genFuncfile, " initialStudentsArray count ..." . $initialStudentArrayCount . "\n");
	        
	        // we now have first pivot student - move it to target array
	        $studentRec = $initialStudentsArray[$pivotStudentNdx];
	        fwrite($genFuncfile, " pivot student..." . $studentRec['student_name'] . "\n");
	        $finalStudentArray[] = $studentRec;
	        array_splice($initialStudentsArray, $pivotStudentNdx, 1);
	        
	        // calcualte air distance from pivot student to rest of them
	        for ($i=0; $i<count($initialStudentsArray); $i++) {
	           $airDist = getAirDistance($studentRec['lat'], $studentRec['lng'], $initialStudentsArray[$i]['lat'], $initialStudentsArray[$i]['lng']);
	           $initialStudentsArray[$i]['dist_from_pivot'] = $airDist;
	           //fwrite($genFuncfile, " dist for " . $studentRec['student_name'] . " is " . $initialStudentsArray[$i]['dist_from_pivot'] . "\n");
	        }
	        
	        // sort array by distance from pivot
	        usort ($initialStudentsArray, 'compByDist');
	        
	        // assign group number and move first $maxStudentsPerGroup (or upto array end) records to final array
	        $i = 0;
	        $recCount = 0;
	        while (($i<$maxStudentsPerGroup) && ($i < $initialStudentArrayCount ))  {
		        $rec = $initialStudentsArray[$i];
		        $rec['student_group'] = $localGroupNo;
		        fwrite($genFuncfile, "setting group no to " . $localGroupNo . "\n" );
		        $finalStudentArray[] = $rec;
		        $i++;
	        }
        
	        // remove from initial array
	        array_splice($initialStudentsArray, 0, $i);  

               if ($i >= $maxStudentsPerGroup) {
                  $localGroupNo += 10;
                  $localGroupCount = 0;  //reset group count
               }
               
	       // assign next pivot for next round as first student 
	       $pivotStudentNdx = 0;
               
               $initialStudentArrayCount = count($initialStudentsArray);
	        
      } // while   count($initialStudentsArray) > 0
      
      // now update student group in DB  from final student array           
      for ($i=0; $i<count($finalStudentArray); $i++) {
         $sGroup = $finalStudentArray[$i]['student_group'];
         $sName = $finalStudentArray[$i]['student_name'];
         $query = "UPDATE `students` SET `student_group` = '$sGroup' WHERE `school_name`='$schoolName' AND `student_name` = '$sName' ;";
         fwrite($genFuncfile, $query . "\n" );      
         $result = mysqli_query($db, $query);
      }
       
       return 1;      
        
}    // studentGroupCluster  

function setCentroidPerCircularOrder($numberOfGroups, $schoolLat, $schoolLng, $farthestLat, $farthestLng, $genFuncfile)  {

   // get middle point of quad
   $origLat = $schoolLat + (($farthestLat - $schoolLat)/2);
   $origLng = $schoolLng + (($farthestLng - $schoolLng)/2); 
   
   fwrite($genFuncfile,"farthest " . $farthestLat . " , " . $farthestLng . "\n" );
   fwrite($genFuncfile,"midpoint " . $origLat  . " , " . $origLng  . "\n" );
   
   $centroids = array();  
   
   // get min distance between lat and lng distances
   $distHoriz = getAirDistance($schoolLat, $schoolLng, $schoolLat, $farthestLng);
   $distVert = getAirDistance($schoolLat, $schoolLng, $farthestLat, $schoolLng);
   $minDist = min($distHoriz, $distVert);   // make it 1/6th of the dist (1/3 of the radius)
   $minDist = ceil($minDist/4);
   
   
   // calc angle diff (360/$numberOfGroups)
   $angleDiff = ceil(360/$numberOfGroups);
   $localAngle = 0;
   fwrite($genFuncfile,"min dist" . $minDist . " angle diff: " . $angleDiff . "\n" );
   
   // set up centroids
   $locationRec = array();
   $R=6378137;
   $dist = $minDist / $R;
   for ($i=0; $i<$numberOfGroups; $i++) {
      $locationRec['lat'] = $origLat + ($dist * cos(deg2rad($localAngle))); 
      $locationRec['lng'] = $origLng + ($dist * sin(deg2rad($localAngle)));
      $centroids[$i] = $locationRec;
      $centroids[$i]['studentsCount'] = 0;
      fwrite($genFuncfile,"centroids " . $i . " - " . $locationRec['lat'] . "," . $locationRec['lng'] . " angle= " . $localAngle . "\n" );
      $localAngle += $angleDiff;
   } // $i
        
        for ($i=0; $i < ($numberOfGroups); $i++) {
           // $centroids[$i]['studentsCount'] = 0;
           // fwrite($genFuncfile,"centroids " . $i . " - " . $centroids[$i]['lat'] . "," . $centroids[$i]['lng'] . "\n" );
        }   
  
  return  $centroids;
  
} // setCentroidPerCircularOrder  



function findQuadBoundary($initialStudentsArray, $schoolLat, $schoolLng, $genFuncfile) {

        // find the farthest lat and lng from school 
        $res = array();
        $res['farthestLat'] = 0.0;
        $res['farthestLng'] = 0.0;
        $maxLatDiff = 0.0;
        $maxLngDiff = 0.0;
        
        for ($i=0; $i<count($initialStudentsArray); $i++) {
        
           $latDiff = abs($initialStudentsArray[$i]['lat'] - $schoolLat);
           $lngDiff = abs($initialStudentsArray[$i]['lng'] - $schoolLng);
           
          if ($latDiff > $maxLatDiff) {
              $maxLatDiff = $latDiff;
              $res['farthestLat'] = $initialStudentsArray[$i]['lat'];
              
          }
           // fwrite($genFuncfile, $i . " - dist to school: " . $initialStudentsArray[$i]['air_distance_to_school'] .  " -- " . $initialMinDistance . "," . $initialMaxDist . "\n" );
          if ($lngDiff > $maxLngDiff) {
             $res['farthestLng'] = $initialStudentsArray[$i]['lng'];
             $maxLngDiff = $lngDiff;
          }
          
        }
        
        
        fwrite($genFuncfile, " res: " . $res['farthestLat'] . " , " . $res['farthestLng'] . "\n" );
        return $res;
        
} // findQuadBoundary

function findNearestAndFarthestByAirDistance($initialStudentsArray, $genFuncfile) {

        // find the closest and farthest student to shcool 
        $initialMinDistance=100000;
        $initialMaxDist=0;
        $resNdx = array();
        $resNdx['nearest'] = 0;
        $resNdx['farthest'] = 0;
        
        for ($i=0; $i<count($initialStudentsArray); $i++) {
           
           if ($initialStudentsArray[$i]['air_distance_to_school']  < $initialMinDistance) {
              $resNdx['nearest']=$i;
              $initialMinDistance = $initialStudentsArray[$i]['air_distance_to_school'];
           }
           fwrite($genFuncfile, $i . " - dist to school: " . $initialStudentsArray[$i]['air_distance_to_school'] .  " -- " . $initialMinDistance . "," . $initialMaxDist . "\n" );
           if ($initialStudentsArray[$i]['air_distance_to_school'] > $initialMaxDist) {
              $resNdx['farthest'] = $i;
              $initialMaxDist = $initialStudentsArray[$i]['air_distance_to_school'];
           }           
        }
        
        
        fwrite($genFuncfile, " resNdx: " . $resNdx['nearest'] . " , " . $resNdx['farthest'] . "\n" );
        return $resNdx;
        
} // findNearestAndFarthestByAirDistance


function groupByKmeans($db, $userName, $schoolName, $schoolLat, $schoolLng, $maxStudentsPerGroup, $quad ) {

$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");

 fwrite($genFuncfile, " in studentGroupCluster..." . "\n");

	$query = "SELECT * FROM `user_settings` WHERE user_name='$userName';";
	
	$result = mysqli_query($db, $query);
	If ($result)
	   {
	    	$row = mysqli_fetch_assoc($result);
		// $returnMsg = $row["message"];
	   }
	   else return 0;
	
	$quadrantTilt = $row["quadrant_tilt"];

        // set up 2 arrays
        $initialStudentsArray = array();
        $finalStudentArray = array();
        
        // get student lat, lng
        
        $query = "SELECT * FROM `students` WHERE `school_name`='$schoolName' AND `quadrant` = '$quad' AND `student_special_needs` = 'N';";
        
        fwrite($genFuncfile, " query is:" . $query ."\n");
        
        $result = mysqli_query($db, $query);
        // $row=mysqli_fetch_array($result);

      
        while ($row=mysqli_fetch_array($result)) {
           $studentRow = $row;
           $studentRow['centroid'] = -1;
           $initialStudentsArray[] = $studentRow;
        }
        
        // get nearest and farthest students from school
        $nearestRec = array();
        $farthestRec = array();
        
        // $resNdx = findNearestAndFarthestByAirDistance($initialStudentsArray, $genFuncfile);
        // $nearestRec = $initialStudentsArray[$resNdx['nearest']];
        // $farthestRec = $initialStudentsArray[$resNdx['farthest']];
        
        // fwrite($genFuncfile, " nearest location: " . $nearestRec['lat'] . " : " . $nearestRec['lng'] . "  - farthst location: " . $farthestRec ['lat'] . " : " . $farthestRec ['lng'] ."\n");
        
        
        $res = findQuadBoundary($initialStudentsArray, $schoolLat, $schoolLng, $genFuncfile);
        
        // set number of groups (clusters) to be total students in quad devided by groupsize and rounded up
        $number2Round = count($initialStudentsArray)/$maxStudentsPerGroup;
        $numberOfGroups = ceil($number2Round);
        fwrite($genFuncfile, " number to round: " . $number2Round ." number of groups: " . $numberOfGroups . "\n");
        
        // now build array of centroid (2 already set as nearest and farthest so add $numberOfGroups-2 to it) each centroid is a location
        $locationRec = array();
        $centroids = array();
        
        // build centroide as circle of points around middle of the quadrant
        // $centroids = setCentroidPerCircularOrder($numberOfGroups, $schoolLat, $schoolLng, $res['farthestLat'], $res['farthestLng'], $genFuncfile);
 
        $farthestRec['lat'] = $res['farthestLat'];
        $farthestRec['lng'] = $schoolLng; // $res['farthestLng'];
        $nearestRec['lat'] = $schoolLat;
        $nearestRec['lng'] = $res['farthestLng']; // $schoolLng;
       
        // $locationRec['lat'] = $nearestRec['lat']; 
        // $locationRec['lng'] = $nearestRec['lng'];
         $locationRec['lat'] = $schoolLat;
         $locationRec['lng'] = $schoolLng;
        $centroids[0] = $locationRec;
        $centroids[0]['studentGroup'] = 0;
    
  
  
  
        $locationRec['lat'] =  $farthestRec['lat']; 
        $locationRec['lng'] =  $farthestRec['lng'];
        $centroids[$numberOfGroups-1] = $locationRec;
        $centroids[$numberOfGroups-1]['studentGroup'] = 0;
        
        
        // there are ($numberOfGroups-1) parts among centroids
        $latDiff = ($farthestRec['lat'] - $nearestRec['lat'])/($numberOfGroups-1);
        $lngDiff = ($farthestRec['lng'] - $nearestRec['lng'])/($numberOfGroups-1);
        fwrite($genFuncfile, " diffs: " . $latDiff . " , "  . $lngDiff . "\n");
        
        for ($i=0; $i < ($numberOfGroups-2); $i++) {
           $locationRec['lat'] = $nearestRec['lat'] + $latDiff*($i+1);
           $locationRec['lng'] = $nearestRec['lng'] + $lngDiff*($i+1);
           $centroids[$i+1] = $locationRec;
           $centroids[$i+1]['studentGroup'] = 0;
        }
      
        for ($i=0; $i < ($numberOfGroups); $i++) {
           $centroids[$i]['studentsCount'] = 0;
           fwrite($genFuncfile,"centroids " . $i . " - " . $centroids[$i]['lat'] . "," . $centroids[$i]['lng'] . "\n" );
        }
        
       
              
      // now start the KMeans passes while pass number is less then 12 and students still change their centroied  
      $totalPasses = 0;
      $maxPasses = 12;
      $changedCentroidCount = 1;
      
      $centroidDistances = array();
      
      while ($totalPasses < $maxPasses  && $changedCentroidCount > 0) {
         $changedCentroidCount = 0;
         // for each student calculate air distance from each centroid and assign it the closest one
         for ($i=0; $i<count($initialStudentsArray); $i++) {
            $closestCentroidNdx=0;
            $minDistance = 100000;
            for ($j=0; $j<$numberOfGroups; $j++) {
                //fwrite($genFuncfile, "calling getAirDistance  lat1= " . $initialStudentsArray[$i]['lat'] . "lng1= " . $initialStudentsArray[$i]['lng'] .  " lat2= " . $centroids[$j]['lat'] . " lng2= " . $centroids[$j]['lng'] . "\n" );
                // $changedCentroidCount[$j] = getAirDistance($initialStudentsArray[$i]['lat'], $initialStudentsArray[$i]['lng'], $centroids[$j]['lat'], $centroids[$j]['lng']);
                $dist2Centroid = getAirDistance($initialStudentsArray[$i]['lat'], $initialStudentsArray[$i]['lng'], $centroids[$j]['lat'], $centroids[$j]['lng']);
                if ($dist2Centroid < $minDistance) {
                   $closestCentroidNdx = $j;
                   $minDistance = $dist2Centroid;
                }
                //fwrite($genFuncfile, "j= " . $j . "distance = " . $dist2Centroid . " : " . " i= " . $i . " minDistance= " . $minDistance . "\n" );
            }
            
            // set centroid value for student
            if ( $initialStudentsArray[$i]['centroid'] != $closestCentroidNdx) {
               $initialStudentsArray[$i]['centroid'] = $closestCentroidNdx;
               $changedCentroidCount++;
               
               // fwrite($genFuncfile, "student " . $initialStudentsArray[$i]['student_name'] . " changed centroid to  " . $closestCentroidNdx . "\n" );
            }
            // fwrite($genFuncfile, "pass " . $totalPasses . " : " . $i . " - centroid is: " . $closestCentroidNdx . "\n" );
         }  // for loop on students array
         
         // once all students were assigned their centroid - recalc centroid location as average of all its students coordinates
         $newLoc = array();
         $locArray = array();
         $centroidCount=array();
         for ($j=0; $j<$numberOfGroups; $j++) {
            $centroidCount[$j]=0;
            $newLoc['lat']=0.0;
            $newLoc['lng']=0.0;
            $locArray[$j] = $newLoc;
            // fwrite($genFuncfile, "j= " . $j. " centroidcount= " . $centroidCount[$j] . " - newloc=: " . $locArray[$j]['lat'] . "," . $locArray[$j]['lng'] . "\n" );
         }
         for ($i=0; $i<count($initialStudentsArray); $i++) {
            $centroidNdx=$initialStudentsArray[$i]['centroid']; 
            $centroidCount[$centroidNdx]++;
            $locArray[$centroidNdx]['lat'] += $initialStudentsArray[$i]['lat'];
            $locArray[$centroidNdx]['lng'] += $initialStudentsArray[$i]['lng'];
            // fwrite($genFuncfile, "i= " . $i . " centroidNdx= " . $centroidNdx . " - locaArray=: " . $locArray[$centroidNdx]['lat'] . "," . $locArray[$centroidNdx]['lng'] . "\n" );
         }
         // calc new lat,lng for centroids
         for ($j=0; $j<$numberOfGroups; $j++) {
         if ($centroidCount[$j] > 0) {
              $centroids[$j]['lat'] = $locArray[$j]['lat']/$centroidCount[$j];
              $centroids[$j]['lng'] = $locArray[$j]['lng']/$centroidCount[$j];
          }
            fwrite($genFuncfile, "new centroid loc for j= " . $j . " is " . $centroids[$j]['lat'] . "," . $centroids[$j]['lng'] . " quad - " . $quad . "\n" );
         }
         
        fwrite($genFuncfile, "end of pass " . $totalPasses . " changedCentroidCount= " . $changedCentroidCount . "\n" ); 
        $totalPasses++; 
         
      } // while total passes loop
       
      // once K-Means passes completed loop students array and assign group based on centroid value
      $baseGroup = $quad * 100 + (($quad-1) * 100); // starts at 100, 300, 500 and 700
      // fwrite($genFuncfile, "Base group is - " . $baseGroup . " Quad is " . $quad . "\n" );
      for ($i=0; $i<count($initialStudentsArray); $i++) {
         $newGroup = $baseGroup + ($initialStudentsArray[$i]['centroid']) * 10;  // for example centroid 3 will add 30 
         // fwrite($genFuncfile,  $i . " - centroid= " . $initialStudentsArray[$i]['centroid'] . " New Group= " . $newGroup . "\n" );
         $initialStudentsArray[$i]['student_group'] = $newGroup;
         // update centroid with student_group number
         $centNdx = $initialStudentsArray[$i]['centroid'];
         $centroids[$centNdx]['studentGroup'] = $newGroup;
      } 
      
      // now update in DB
       for ($i=0; $i<count($initialStudentsArray); $i++) {
         $sGroup = $initialStudentsArray[$i]['student_group'];
         $sName = $initialStudentsArray[$i]['student_name'];
         $query = "UPDATE `students` SET `student_group` = '$sGroup' WHERE `school_name`='$schoolName' AND `student_name` = '$sName' ;";
         // fwrite($genFuncfile, $query . "\n" );      
         $result = mysqli_query($db, $query);
      }
      
      // put here updates of centroids into centroid table
         $query = "DELETE FROM `centroids` WHERE  `user_name` = '$userName' AND  `school_name`= '$schoolName' AND `quadrant` = '$quad' ;";
         fwrite($genFuncfile, $query . "\n" );      
         $result = mysqli_query($db, $query);
        for ($i=0; $i<count($centroids); $i++) {
         $cLat = $centroids[$i]['lat'];
         $cLng = $centroids[$i]['lng'];
         $sGrp = $centroids[$i]['studentGroup'];
         
         $query = "REPLACE INTO  `centroids` SET `user_name` = '$userName' , `school_name`= '$schoolName' ,  `quadrant` = '$quad' , `lat` = '$cLat' , `lng` = '$cLng' , `student_group` = '$sGrp' ;";
         fwrite($genFuncfile, $query . "\n" );      
         $result = mysqli_query($db, $query);
      }      
       return 1;           

} // groupByKmeans


function updateQuadrants($db, $userName, $schoolName, $quadrantTilt) {
$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");

 fwrite($genFuncfile, " in updateQuadrants..." . "\n");
 
 
        $query = "SELECT * FROM `user_schools` WHERE `user_name`='$userName' AND `school_name`='$schoolName';";
        $result = mysqli_query($db, $query);
        $row=mysqli_fetch_array($result);
        // print_r($row);
        $schoolLat = $row["lat"];
        $schoolLng = $row["lng"];  
        
        fwrite($genFuncfile, " Query: " . $query . " school Lat: " . $schoolLat . " school Lng: " . $schoolLng . "\n");

      $queryStudent = "SELECT * FROM `students` WHERE `school_name` = '$schoolName' ;";
      //$rowStudent=mysqli_fetch_array($queryStudent,MYSQLI_ASSOC);
      $resultStudent = mysqli_query($db, $queryStudent);
      
      $studnetArray = array();
      $i = 0;
      
      while ($studentRow = mysqli_fetch_array($resultStudent))   {
         $newQuadrant = getQuadrant($userName, $schoolLat, $schoolLng,  $studentRow['lat'], $studentRow['lng']);
         $sName = $studentRow['student_name'];
         $spcialNeeds = $studentRow['student_special_needs'];
         $sGroup = ($spcialNeeds == 'Y') ? 0 : $newQuadrant;
         // update studnet with new quad (tile is fetched in getquad function
         $updateQuery = "UPDATE `students` SET `quadrant` = '$newQuadrant',  `student_group` = '$sGroup'  WHERE `school_name` = '$schoolName' AND `student_name` = '$sName' ;";
         $result = mysqli_query($db, $updateQuery);
         fwrite($genFuncfile, " Update Query: " . $updateQuery . "\n");
      } // while
      
      return 1;
 
} // updateQuadrants

function assignStudents2Stops($db, $userName, $schoolName, $schoolLat , $schoolLng, $quad, $distRange)  {

$fileTimestamp = date('Ymd');
$genFuncfile = fopen("../logs/func_" . $fileTimestamp . ".txt","a");

 fwrite($genFuncfile, " in assignStudents2Stops..." . "\n");

       // get bus stops for this quad from db
        $query = "SELECT * FROM `school_bus_stops` WHERE `user_name`='$userName' AND `school_name`='$schoolName' AND `quadrant` = '$quad' ;";
        $result = mysqli_query($db, $query);
        fwrite($genFuncfile, $query . "\n" );
        
        $busStops = array();
        while ($row=mysqli_fetch_array($result)) {
           $stopRow = $row;
           $stopRow['studentGroup'] = 0;
           $busStops[] = $stopRow;
           // fwrite($genFuncfile, " adding bus stop " . $stopRow['description'] . " to array " . "\n");
        }

        $query = "SELECT * FROM `students` WHERE `school_name`='$schoolName' AND `quadrant` = '$quad' AND `student_special_needs` = 'N' ;";
        
        // fwrite($genFuncfile, " query is:" . $query ."\n");
        
        $result = mysqli_query($db, $query);
        fwrite($genFuncfile, $query . "\n" );
        // $row=mysqli_fetch_array($result);

        $studentsArray = array();
        while ($row=mysqli_fetch_array($result)) {
           $studentRow = $row;
           $studentsArray[] = $studentRow;
        }
        
        // read centroids from table for this quad
        $query = "SELECT * FROM `centroids` WHERE `user_name`='$userName' AND `school_name`='$schoolName' AND `quadrant` = '$quad' AND `student_group` > '0' ;";
        $result = mysqli_query($db, $query);
        fwrite($genFuncfile, $query . "\n" );
        
        $centroids = array();
        while ($row=mysqli_fetch_array($result)) {
           $centRow = $row;
           $centroids[] = $centRow;
        }
        
        // calculate centoid (=group) for each bus stop
        fwrite($genFuncfile, " Looping  " . count($busStops) . " bus stops " .  "\n");
        for ($i=0; $i<count($busStops); $i++) {
           // loop centroids and select closest one then take group number from it and assign it to the bus stop record
           $maxDist = 100000;
           $closestCentroid = 0;
           for ($k=0; $k<count($centroids); $k++) {
             $dist = getAirDistance($busStops[$i]['lat'], $busStops[$i]['lng'], $centroids[$k]['lat'], $centroids[$k]['lng']);
             if ($dist < $maxDist) {
               $closestCentroid = $k;
               $maxDist = $dist;
             }
           }
           
           // assing group to bus stop and update it in DB for later use
           $busStops[$i]['studentGroup'] = $centroids[$closestCentroid]['student_group'];
           fwrite($genFuncfile, " bustop " . $busStops[$i]['description'] . " in group " . $busStops[$i]['studentGroup'] . "\n");
           $gNum = $busStops[$i]['studentGroup'];
           $busStopId = $busStops[$i]['id'];
           $updateQuery = "UPDATE `school_bus_stops` SET `group_number` = '$gNum' WHERE `id` = '$busStopId' ;";
           $result = mysqli_query($db, $updateQuery);
           
        } // for $i loop
        
        // loop students and look for closest bus stop with same group value and within range 
        for ($i=0; $i<count($studentsArray); $i++) {
           // loop bus stops with same group number and check which one is closest and with in given distance
           $minDist = 100000;
           $stopNdx = count($busStops) +1 ;
           for ($k=0; $k<count($busStops); $k++) {
           // fwrite($genFuncfile, " checking student   " . $studentsArray[$i]['student_name'] . " and bus stops " .  $busStops[$k]['description'] . "\n");
             if ($studentsArray[$i]['student_group'] == $busStops[$k]['studentGroup']) {
                 $dist = getAirDistance($busStops[$k]['lat'], $busStops[$k]['lng'], $studentsArray[$i]['lat'], $studentsArray[$i]['lng']);
                 fwrite($genFuncfile, " distance is    " . $dist . " min dist is  " .  $minDist  . "\n");
                 if (($dist < $minDist) && ($dist <= $distRange) ) {
                    $minDist = $dist;
                    $stopNdx = $k;
                 } // if $dist
             }  // if same group
           } // if $k
           // now we have closest stop in distance range
           fwrite($genFuncfile, " closest to   " . $studentsArray[$i]['student_name'] . " is bus stops " .  $busStops[$stopNdx]['description'] . " stopNdx = " . $stopNdx . " stop count= " . count($busStops) . "\n");
           if ($stopNdx < count($busStops)) {
              $studentsArray[$i]['bus_stop_description'] = $busStops[$stopNdx]['description'];
              $studentsArray[$i]['bus_stop_id'] = $busStops[$stopNdx]['id'];
           }
           else {
              $studentsArray[$i]['bus_stop_description'] = null;
              $studentsArray[$i]['bus_stop_id'] = 0;
           }
           
           // now update student record in db
           $busDesc = $studentsArray[$i]['bus_stop_description'];
           $busStopId = $studentsArray[$i]['bus_stop_id'];
           $sName = $studentsArray[$i]['student_name'];
           $updateQuery = "UPDATE `students` SET `bus_stop_description` = '$busDesc',  `bus_stop_id` = '$busStopId'  WHERE `school_name` = '$schoolName' AND `student_name` = '$sName' ;";
           $result = mysqli_query($db, $updateQuery);
           // fwrite($genFuncfile, " Update Query: " . $updateQuery . "\n");           
        } // for $i
        
}// assignStudents2Stops

?>