<?php
include './generalFunctions.php';
include './navigationBar.php';

$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failFed</p>';
}

$userName=$_COOKIE["userName"];

$schoolName = "";

// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("../logs/debug_" . $fileTimestamp . ".txt","a");
fwrite($file,"POST[schoolNameSelect = " . $_POST['schoolNameSelect'] . "\n" );
//fwrite($file,"GET[schoolNameSelect = " . $_GET['schoolNameSelect'] . "\n" );


if( isset($_POST['schoolNameSelect']))  {
// echo("School Name Select is set");
 $showSchoolMap = true;
 $showGroupButton = true;
 $schoolName=$_POST['schoolNameSelect'];
}
else {
 echo("School Name Select Not Set: " );
  $showSchoolMap = false;
  $showGroupButton = false;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Routes Page</title>
  <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
  <link rel="stylesheet" type="text/css" href="routesPage.css">
  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>

 
<script>


  $(document).ready(function() {
    
    // hide/show school map
    
    $(function () {
    /***
      $('#schoolNameSubmit').click( function() {
        $(this).parent().parent().parent().find('[name=schoolMap]').show();
        $(this).parent().parent().parent().find('[name=groupButton]').show();
      });
      ***/
     
      
    });
    
    
    });


    
    
</script>

		
</head>
<body>   

<br><br><br>
<!--
<div class="content">
    <ul><a href="settingsGeneralPage.php"><li>GENERAL</li></a></ul>
</div>
-->

<div class = "routeSettingsForm" >

 
    <h1>ROUTES ( <?php echo $schoolName; ?> )</h1>


<div>
  <form name="schoolNameSelect" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" > 
    <td>Select a School:<td> 
    <select name="schoolNameSelect" class= 'routeSettingsForm'>
                    
      <?php 
      $querySchools = "SELECT * FROM `user_schools` WHERE `user_name`='$userName';";
      $rowSchools=mysqli_fetch_array($querySchools,MYSQLI_ASSOC);
      $resultSchools = mysqli_query($db, $querySchools);
      while($fetch_options = mysqli_fetch_array($resultSchools)) { 
      ?> 
  
      <option value ="<?php echo $fetch_options['school_name']; ?>"><?php echo $fetch_options['school_name']; ?></option>
    </selcet>
  
      <?php 
      }; 
      ?>
      <input type = "submit" type="button" id="schoolNameSubmit" class="schoolNameSubmit"> &nbsp &nbsp &nbsp <button id='groupStudents' type="button" name="groupStudents" class='submit' input type="submit">GROUP</button> &nbsp &nbsp &nbsp <button id='assignStudents' type="button" name="assignStudents" class='submit' input type="submit">ASSIGN</button> &nbsp &nbsp &nbsp  <button id='showAssignment' type="button" name="showAssignment" class='showAssignment' input type="submit">SHOW ASSIGNMENT</button>  &nbsp &nbsp &nbsp <button id='optimize' type="button" name="optimize" class='submit' input type="submit">OPTIMIZE</button>

      
 </form> 
 </div> 
  




<div id="map" class="mapSnippet">

<?php 
 if ($showSchoolMap) {
 
 
 function compareStops($a, $b) {
    // compare by quadrant and then by student group
    if ($a['quadrant'] != $b['quadrant'])  
       return ($a['quadrant']  < $b['quadrant']) ? -1 : 1;

     if ($a['student_group'] == $b['student_group']) 
         return 0;
     return ($a['student_group']  < $b['student_group']) ? -1 : 1;
       
    // compare by bearing
    /**
    if ($a['bearing'] == $b['bearing']) 
      return 0;
      
    return ($a['bearing']  < $b['bearing']) ? -1 : 1;
    **/
    
 }
 
       $query = "SELECT * FROM `user_settings` WHERE user_name='$userName';";

       $result = mysqli_query($db, $query);
       If ($result) {
    	$userSettingsRow = mysqli_fetch_assoc($result);
	// $returnMsg = $row["message"];
      }
      
      $querySchool = "SELECT * FROM `user_schools` WHERE `user_name`='$userName' and `school_name` = '$schoolName' ;";
      $rowSchool=mysqli_fetch_array($querySchool,MYSQLI_ASSOC);
      $resultSchool = mysqli_query($db, $querySchool);
      $resultRow = mysqli_fetch_array($resultSchool );
       
      $schoolAddress = $resultRow['school_address'];
      $schoolTitle = $schoolName . " " . $schoolAddress;
      $schoolLat = $resultRow['lat'];
      $schoolLng = $resultRow['lng'];
      
      echo "school info: " . $schoolAddress . " " . $schoolTitle ;
      
      // load students info into array
      
      $queryStudent = "SELECT * FROM `students` WHERE `school_name` = '$schoolName' ;";
      $rowStudent=mysqli_fetch_array($queryStudent,MYSQLI_ASSOC);
      $resultStudent = mysqli_query($db, $queryStudent);
      
      $studnetArray = array();
      $i = 0;
      
      while ($studentRow = mysqli_fetch_array($resultStudent)) 
      {

              $studentRec = $studentRow;
	      $studentRec['title'] = $studentRow['student_name'] . " " . $studentRow['student_address'] . " Grade: " . $studentRow['student_grade'] . " Quad: " . $studentRow['quadrant'] . " GRP: " . $studentRow['student_group'] . " bearing:" . $studentRow['bearing'];
	      
	      // add record to array
	      $studnetArray[] = $studentRec;
	      $i++;
	
      }
    
      // load bus stops from db to local array
      $queryBusStop = "SELECT * FROM `school_bus_stops` WHERE `user_name` = '$userName' and `school_name` = '$schoolName' ;";
      // $rowBusStop = mysqli_fetch_array($queryBusStop,MYSQLI_ASSOC);
      $resultBusStop = mysqli_query($db, $queryBusStop);
      
      $timestamp = date('m/d/Y h:i:s');
      fwrite($file,'['. $timestamp. ']: ' . $queryBusStop . "\n"); 
 	        
      $busStopArray = array();    
      
      while ($busStopRow = mysqli_fetch_array($resultBusStop)) 
      { 
              $busStopRec = $busStopRow;
              $busStopRec['title'] = $busStopRow['description'] . " Loc: " . $busStopRow['lat'] .  " : " . $busStopRow['lng'] . "(" . $busStopRow['nearest_address'] . ")";	
	      // add record to array
	      $busStopArray[] = $busStopRec;
	
      }
  
      // sort bust stop array 
      usort ($busStopArray, 'compareStops');
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 //    load routes from DB
 
       $routeType = "to_school";
       
       $query = "SELECT * FROM `school_routes` WHERE `user_name` = '$userName' and `school_name` = '$schoolName' and `route_type` = '$routeType' ;";
       $resultRoute = mysqli_query($db, $query);
      
       $routeArray=array();
     
       while($routeRow= mysqli_fetch_array($resultRoute)){

                $routeRec = array();
                $jsonRoute = $routeRow['route_json'];
                $routJsonArr = json_decode($jsonRoute, true);
                // $routJsonArr = unserialize($serializedRoute);
                
  /***
     ob_start();
      // var_dump($routePath);
      var_dump($routJsonArr);
      $stDump = ob_get_clean();
      fwrite($file, "route rec json encoded = " . $stDump . "\n" );	    
     ***/         
                
                $routeRec['id'] = $routeRow['id'];
                $routeRec['user_name'] = $routeRow['user_name'];
                $routeRec['school_name'] = $routeRow['school_name'];
                $routeRec['route_type'] = $routeRow['route_type'];
                $routeRec['route_number'] = $routeRow['route_number'];
                $routeRec['group_number'] = $routeRow['group_number'];
                $routeRec['color'] = $routeRow['color'];
                $routeRec['route_json'] = $routeRow['route_json'];
                $routeRec['route_json_blob'] = $routeRow['route_json_blob'];
                $routeArray[]=$routeRec;
                
              $timestamp = date('m/d/Y h:i:s');
              // fwrite($file,'['. $timestamp. ']: ' . ' route record  ' . ' route number - ' . $routeRec['route_number'] . ' Group number - ' . $routeRec['group_number'] . ' color - ' . $routeRec['color'] . "\n"); 
              
              // fwrite($file,'['. $timestamp. ']: ' . ' route json - ' . $routeRow['route_json'] . "\n");    
              // fwrite($file,'['. $timestamp. ']: ' . ' route json - ' . $routeRec['route_json'] . "\n");            
       }
    //   var_dump($route_stops_array);exit;
    
      // load group numbers to array (to be used later to decide group color)
      
      $queryGroups = "SELECT distinct `student_group` FROM `students` where `student_group` > 0 order by `student_group` ;";
      $resultGroup = mysqli_query($db, $queryGroups);
      
      $groupsArray = array();
      $i = 0;
      
      while ($rowGroup = mysqli_fetch_array($resultGroup))  {
           $grpNo = (string)$rowGroup['student_group'];
           $groupsArray[]=$rowGroup['student_group'];
           $timestamp = date('m/d/Y h:i:s');
           fwrite($file,'['. $timestamp. ']: ' . ' student_group - ' . $rowGroup['student_group'] . '  ' . $grpNo . "\n");
	   $i++;
      }
 
 /** 
     ob_start();
      // var_dump($routePath);
      var_dump($groupsArray);
      $stDump = ob_get_clean();
      fwrite($file, "groups array = " . $stDump . "\n" );	    
   **/       
    
      
 ?>
 
    <script>
    var map;
    var groupsArray = <?php echo json_encode($groupsArray); ?>;
    // var groupsArray = <?php echo $groupsArray; ?>;
    var groupIcons = ["ltblue-dot.png", "green-dot.png", "orange-dot.png", "pink-fot.png", "purple-dot.png", "red-dot.png" , "blue-dot.png", "maroon.png", "gray.png"];
    
    
      function initMap() {
        var showMap = <?php echo $showSchoolMap; ?>;
        var schoolLat = <?php echo $schoolLat; ?> ;
        var schoolLng = <?php echo $schoolLng; ?> ;
        var schoolTitle = "<?php echo $schoolTitle; ?>" ;
       
        if (showMap) {
        var routeZIndex = 1;
        // create map per school location
        
	        var myLatLng = {lat: schoolLat, lng: schoolLng};
	        var mapDiv = document.getElementById('map');
	         map = new google.maps.Map(mapDiv, {
	          center: myLatLng,
			  mapTypeId: google.maps.MapTypeId.TERRAIN,
	          zoom: 14
	        });
	// add school marker
	        var marker = new google.maps.Marker({
	    	  position: myLatLng,
	    	  map: map,
	    	  title: schoolTitle
	        });
	        
	 // add bus stops markers  
     var infowindow = new google.maps.InfoWindow({});     
	 var busStopJQarray = <?php echo json_encode($busStopArray); ?>;
         for(var i=0; i<busStopJQarray.length; i++){
			 
	    var id = parseFloat(busStopJQarray[i].id);
            var busStopLat = parseFloat(busStopJQarray[i].lat);
            var busStopLng = parseFloat(busStopJQarray[i].lng);
            var busStopLatLng = {lat: busStopLat, lng: busStopLng};
            var busStopDesc = busStopJQarray[i].description;
            var busStopIcon = "../assets/pin_yellow.png";
            var busStopMarker = new google.maps.Marker({
    	       position: busStopLatLng,
    	       map: map,
    	       icon: busStopIcon,
    	       title: busStopJQarray[i].title,
			   draggable:true,
			   desc: busStopDesc,
			   id:id
            });
			
			//show description,lat,lng on marker mouseover
			 google.maps.event.addListener(busStopMarker, 'mouseover', function() {
				 // infowindow.setContent(this.desc); 
				 // infowindow.open(map, this);
			 });
			 //close infowindow on mouseout
			google.maps.event.addListener(busStopMarker, 'mouseout', function() {
				 infowindow.close();
			 });
			 //drag marker and when end update db
			  google.maps.event.addListener(busStopMarker, 'dragend', function(e) {
				//alert(this.id);
				var lat = e.latLng.lat();
				var lng = e.latLng.lng();
				var id = this.id;
				var newDesc = prompt("Update description:" , this.desc);
				if (newDesc != null) {
				    var data = {
							func : 'updateBusStop',
							schoolName: "<?php echo $schoolName; ?>",
							userName: '<?php echo $userName; ?>',
							lat: lat,
							lng: lng,
							desc: newDesc,
							id: id
							};
                                                 
					  $.ajax({
						  type: "POST",
						  url: "./ajax.php", //Relative or absolute path to response.php file
						  data: data,
                                                  dataType: "json",
						  success: function(response) {
                                                   
								console.log(response);
							  }
				  
						}); // ajax call  
						// need to update description here to new value
						this.desc = newDesc;
						var newTitle = newDesc + ' Loc: ' + lat + ' : ' + lng;
						this.setTitle(newTitle);
						
					};
			 });
			 
			 //delete bust stop marker on rightclick
			  google.maps.event.addListener(busStopMarker, 'rightclick', function() {
				 //alert(this.id);
				google.maps.event.clearListeners(this,'mouseout');
				 var id = this.id;
				 infowindow.setContent('<div class="dropdpwn"><h3>Do you want to delete this marker?</h3><br><select onchange="deleteBusStop(this.value, '+id+');"><option value="">--please select--</option><option value="yes">Yes</option></select></div>'); 
				 infowindow.open(map, this);
				 
			 });
          };
	        
	 // add students markers 
         var studentjQArray= <?php echo json_encode($studnetArray); ?>;
		 //console.log(studentjQArray);
         
	         for(var i=0; i<studentjQArray.length; i++){
	            var studentLat = parseFloat(studentjQArray[i].lat);
	            var studentLng = parseFloat(studentjQArray[i].lng);
	            var studentLatLng = {lat: studentLat, lng: studentLng};
	            var studentQuad = studentjQArray[i].quadrant;
	            var studentGroup = studentjQArray[i].student_group;
	            var studentIcon = getStudentIcon(studentGroup);
	            var studentName = studentjQArray[i].student_name;
	            var studentMarker = new google.maps.Marker({
	    	    position: studentLatLng,
	    	    map: map,
	    	    icon: studentIcon,
	    	    title: studentjQArray[i].title,
				customInfo : studentGroup,
				student_name : studentName
	          });
			  //right click event listener
	         google.maps.event.addListener(studentMarker, 'rightclick', function(event) {
				 
				    //alert(event.latLng+ '>>' + this.student_name + '>>' + this.customInfo);
				
				
                                var newGroup = prompt("Enter student new group:");
                                // alert(new&& Group);
                                if (newGroup && (newGroup != this.customInfo)) {
									
									
									//update map marker
									/****
									var newIcon =  getStudentIcon(newGroup);
									var newMarker = new google.maps.Marker({
										   position: event.latLng,
										   map: map,
										   icon: newIcon
									});
									newMarker.setMap(map);
									***/
                                	 // update student_group field in DB
                                	 // make ajax call for school update
									 var data = {
										func : 'updateStudentGroup',
										schoolName: "<?php echo $schoolName; ?>" ,
										studentName: this.student_name,
										studentNewGroup: newGroup
										};
									  $.ajax({
										  type: "POST",
										  dataType: "json",
										  url: "ajax.php", //Relative or absolute path to response.php file
										  data: data,
										  success: function(response) {
										  // change marker icon pre new group
														   
												// location.reload();
												console.log(response);
											  }
								  
										}); // ajax call  
										this.setIcon(getStudentIcon(newGroup)); 
										// update title of marker to include new group
										var newGroupTitle = 'GRP: ' + newGroup;
										var newTitle = this.getTitle().replace(/GRP: [0-9]+/g, newGroupTitle);
										this.setTitle(newTitle);
			              
			        }; // if newGroup
			  }); // student marker event
             
            
                }   // i++

            // add routes
            
            var routeArray= <?php echo json_encode($routeArray); ?>;	
            // display routes if array is not empty	
            var i=0;
            
            for (i=0; i<routeArray.length; i++) {
                      var routeColor = routeArray[i].color;
                      // rendererCalled++;
                      var tmpStr = routeArray[i].route_json;
                      var routeDecoded = decodeURIComponent(tmpStr);
                      // tmpStr = escape(tmpStr);
                      // var schoolRoute = JSON.parse(routeArray[i].route_json);
                      var polyDisplay = JSON.parse(routeArray[i].route_json);

               
                      var schoolRoute = routeArray[i].route_json_blob;
                       // var schoolRoute = JSON.parse(tmpStr);
			// var polyDisplay = schoolRoute.overview_path;
			var routePolyline = new google.maps.Polyline({strokeColor: routeColor, strokeWeight: 5, clickable: true, path: polyDisplay});
			routePolyline.setMap(map);   
			
			google.maps.event.addListener(routePolyline, 'click', function() {
			   // display route on top
			   this.setOptions({ zIndex: routeZIndex++ });
			});			
			         
            }
           
	   
        } // show map
                 //Add marker when dblclick on  map
		 map.addListener('dblclick', function(event) {
				    //alert(event.latLng);
                                    
				    addMarker(event.latLng, event.latLng.lat(), event.latLng.lng());
				  
				});
                                

            
            
                
	    };  // init map
        
		function addMarker(location, lat, lng){
			
			var infowindow = new google.maps.InfoWindow({});
			var desc = prompt("Enter Bus Stop Description:");
			var micon = "../assets/pin_yellow.png";
			if(desc != null){
				
				
				var busStopMarker = new google.maps.Marker({
				  position: location,
				  map: map,
				  desc: desc,
				  icon: micon
				});
				
			   busStopMarker.setMap(map);
			   infowindow.setContent(desc); 
			   infowindow.open(map,busStopMarker);
			   
			   // get nearest address for lat lng location
			   var geocoder = new google.maps.Geocoder;
			   var busStoplatlng = {lat: lat, lng: lng};
			   var nearestAddr = "";
			   geocoder.geocode({'location': busStoplatlng}, function(results, status) {
			    if (status == 'OK') {
			      if (results[0]) {
			         nearestAddr = results[0].formatted_address;
		                 var newAddr = nearestAddr.replace(/\,\sUSA$/, '');
			   
			   var data = {
							func : 'insertBusStop',
							schoolName: "<?php echo $schoolName; ?>" ,
							userName: '<?php echo $userName; ?>',
							lat: lat,
							lng: lng,
							nearestAddr: newAddr,
							desc: desc
							};
					  $.ajax({
						  type: "POST",
						  dataType: "json",
						  url: "ajax.php", //Relative or absolute path to response.php file
						  data: data,
						  success: function(response) {
                                                      
								console.log(response);
							  }
				  
						}); // ajax call  
				busStopMarker.setTitle(desc + ' ' + lat + ':' + lng + '(' + nearestAddr + ')' );
			      } // if resluts[0]
			    }	// status == OK
			   });	 	//  geocoder.geocode			
				
			}
			
			 
		} // add marker
		
        function getStudentIcon(inputStudentGroup) 
        {
        	if (inputStudentGroup == 1) {
        	     return "../assets/blue-dot.png";
        	  }
        	  else if (inputStudentGroup == 2) {
        	     return "../assets/green-dot.png";
        	  }
        	  else if (inputStudentGroup == 3) {
        	     return "../assets/pink-dot.png";
        	  }
        	  else if (inputStudentGroup == 4) {
        	     return "../assets/purple-dot.png";
        	  }
        	  else if (inputStudentGroup < 1 ) {
        	     return "../assets/yellow-dot.png"
        	  } else if (inputStudentGroup  >= 100) {
        	  
        	     var groupNdx = groupsArray.indexOf(inputStudentGroup);
        	     var groupMod = groupNdx % groupIcons.length;
        	     var groupIcon = "../assets/" + groupIcons[groupMod];
        	     
        	     return groupIcon;
/****        	  
        	     groupH = Math.floor(inputStudentGroup/100);
        	     var groupMod = inputStudentGroup % 100 ;  //+ (groupH*10);
        	     if (groupMod == 0 ) {
        	        return "../assets/blue-dot.png";
        	     }
        	     if (groupMod == 10 || groupMod == 80) {
        	        return "../assets/orange-dot.png";
        	     }
        	     if (groupMod == 20  || groupMod == 90) {
        	        return "../assets/purple-dot.png";
        	     }
        	     if (groupMod == 30) {
        	        return "../assets/green-dot.png";
        	     }
        	     if (groupMod == 40) {
        	        return "../assets/pink-dot.png";
        	     }
        	     if (groupMod == 50) {
        	        return "../assets/ltblue-dot.png";
        	     }
        	     if (groupMod == 60) {
        	        return "../assets/maroon.png";
        	     }
        	     if (groupMod == 70) {
        	        return "../assets/drkgreen.png";
        	     }
****/

        	     
        	  }  // if (inputStudentGroup  >= 100)
        };
        
		//delete bus stop
		function deleteBusStop(value, id){
			
			
			if(value == 'yes'){
				    var cnf =  confirm("Please confirm!.");
					if(cnf == true){
						
						var data = {
							func : 'deleteBusStop',
							id: id
							};
					  $.ajax({
						  type: "POST",
						  dataType: "json",
						  url: "ajax.php", //Relative or absolute path to response.php file
						  data: data,

					});
					
					// this.setMap(null);

			}
			else{
				return false;
			}
		}
		else {
		   return false;
		}
		
		} // deleteBusStop
		
  
  
    $(function () {
    var routeStrokeColors = ["blue", "darkgreen", "maroon", "yellow", "hotpink", "olivedrab", "lavender" , "limegreen", "royalblue", "tomato"];
    var assignPolygons = [];
    var directionsDisplayArray = [];
    var workGroup;
    var rendererCalled=0;
    var routeZIndex = 1;
    
       $('#groupStudents').click( function() {
           window.alert('groupStudents clicked:');
          var maxStudentsPerGroups = prompt("Enter Max Students Per Group:");
          var userName = '<?php echo $userName; ?>';
          var schoolName = '<?php echo $schoolName; ?>';
          if (userName && schoolName) {
             var data = {
				func : 'calculateGroups',
				maxStudentsPerGroup: maxStudentsPerGroups,
				schoolName: schoolName ,
				userName: userName
				};
			  $.ajax({
				  type: "POST",
				  dataType: "json",
				  url: "ajax.php", //Relative or absolute path to response.php file
				  data: data,
				  success: function(response) {
				  console.log(response);
					  location.reload();					   
				  }
						  
	                }); // ajax call  
	                location.reload(true);		
          };
       }); // groupStudents
       $('#assignStudents').click( function() {
          // window.alert('groupStudents clicked:');
          var userName = '<?php echo $userName; ?>';
          var schoolName = '<?php echo $schoolName; ?>';
          if (userName && schoolName) {
             var data = {
				func : 'assignStudents',
				schoolName: schoolName ,
				userName: userName
				};
			  $.ajax({
				  type: "POST",
				  dataType: "json",
				  url: "ajax.php", //Relative or absolute path to response.php file
				  data: data,
				  success: function(response) {
				  console.log(response);
					  location.reload();					   
				  }
						  
	                }); // ajax call  
	                location.reload(true);		
          };
       }); // assignStudents
       
        $('#showAssignment').click( function() {      
           var showAssignmentButton = this; 
           var borderWidth = this.style.borderWidth;
           var borderStyle = this.style.borderStyle;
           
 
           if (borderWidth  == "0px" || borderWidth  == "") {
           
             // showAssignmentButton.style.borderWidth = "5px 5px 5px 5px";
             // showAssignmentButton.style.borderColor = "gold";
             
              var busStopJQarray = <?php echo json_encode($busStopArray); ?>;
              var studentsArray = <?php echo json_encode($studnetArray); ?>;
              
              // loop students array and create line from student to its bus stop
              var i;
              for (i=0; i<studentsArray.length; i++) {
                 if (studentsArray[i].bus_stop_id > 0) {
                    // find bustop from bus stop array
                    var j;
                    var stopNdx=0;
                    for (j=0; j<busStopJQarray.length; j++) {
                       if ( busStopJQarray[j].id == studentsArray[i].bus_stop_id) {
                          stopNdx = j;
                          break;
                       } 
                    }
                    // get bus stop x,y as well as student 
                    var studentLat = parseFloat(studentsArray[i].lat);
                    var customData = {bsid: studentsArray[i].bus_stop_id, bsDesc: studentsArray[i].bus_stop_description, bsLat: busStopJQarray[stopNdx].lat, bsLng: busStopJQarray[stopNdx].lng, studentName: studentsArray[i].student_name, schoolName: studentsArray[i].school_name, studentLat: studentsArray[i].lat, studentLng: studentsArray[i].lng, studentQuad: studentsArray[i].quadrant};
                    var studentLng = parseFloat(studentsArray[i].lng);
                    var stopLat = parseFloat(busStopJQarray[stopNdx].lat);
                    var stopLng = parseFloat(busStopJQarray[stopNdx].lng);
                    var studentCoords = [{lat: studentLat, lng: studentLng} , {lat: stopLat, lng: stopLng}];
                    var sLoc1 = studentCoords[0];
                    var sLoc2 = studentCoords[1];
					var wayCoordinates = [
						new google.maps.LatLng(studentLat, studentLng),
						new google.maps.LatLng(stopLat,stopLng)
					  ];
				     var studentPath = new google.maps.Polyline({
                                     path: wayCoordinates,
                                     strokeColor: 'gold',
                                     strokeOpacity: 1.0,
                                     strokeWeight: 3,
				     custom: customData,
                                     geodesic: true,
                                     //editable: true,
                                     draggable: true});
                    studentPath.setMap(map);
                    assignPolygons.push(studentPath);
                    
                    // add listener for drag/drop of polygon used for bus stop change

                    google.maps.event.addListener(studentPath, 'dragend', function(e) {
                       var origPoly = this.getPath();
                       
                       // var newPoly = [];
		       // alert('sid'+this.custom.studentName + '>>lat'+e.latLng.lat()+ '>> lng'+ e.latLng.lng() );
		       // find closest bus stop to dragend location
		       var studentQuad = this.custom.studentQuad;
		       var minDist = 10000;
                      var j;
                      var stopNdx=0;
                      for (j=0; j<busStopJQarray.length; j++) {
                         // calc distance between new location and bus stop
                         var latDiff = Math.abs(e.latLng.lat() - busStopJQarray[j].lat);
                         var lngDiff = Math.abs(e.latLng.lng() - busStopJQarray[j].lng);
                         var dist = Math.sqrt(latDiff * latDiff + lngDiff * lngDiff);
                         if ( dist < minDist ) {
                            stopNdx = j;
                            minDist = dist;
                         } 
                      }	
                      
                     // now we have nearest bus stop - check it is not same as original one and has same quadrant
                     if  ((busStopJQarray[stopNdx].quadrant == studentQuad) && ( busStopJQarray[stopNdx].id != this.custom.bsid)) {
                        // prompt for approval and if approved - add new polygon from student to new bus stop, remove orig polygon and make ajax call to update student record for new bus stop
                        var confirmStr =  "Confirm moving " + this.custom.studentName + " to new stop " + this.custom.bsDesc;
                        var newStopReply = confirm( confirmStr );
                        if (newStopReply) {
                           var newLoc = new google.maps.LatLng(busStopJQarray[stopNdx].lat, busStopJQarray[stopNdx].lng);
                           var studentLoc = new google.maps.LatLng(this.custom.studentLat, this.custom.studentLng);
                           origPoly.setAt(0, studentLoc);
                           origPoly.setAt(1, newLoc);
                           
                           // make ajax call to update Db
                         var data = {
				func : 'updateStudentBusStop',
				schoolName: this.custom.schoolName,
				studentName: this.custom.studentName,
				studentNewBSDesc:  this.custom.bsDesc,
				studentNewBSID: this.custom.bsid
				};
			  $.ajax({
				  type: "POST",
				  dataType: "json",
				  url: "ajax.php", //Relative or absolute path to response.php file
				  data: data,
				  success: function(response) {
				  console.log(response);
					  //location.reload();					   
				  }
						  
	                  }); // ajax call  
                           
                        }
                        else {
                          // display orinal assignment polygon
                           var newLoc = new google.maps.LatLng(this.custom.bsLat, this.custom.bsLng);
                           var studentLoc = new google.maps.LatLng(this.custom.studentLat, this.custom.studentLng);
                           origPoly.setAt(0, studentLoc);
                           origPoly.setAt(1, newLoc);
                          
                        }
                     }
		       
					   
                    }); // dragend 
                    
                    google.maps.event.addListener(studentPath, 'drag', function(e) {
                       var origPoly = this.getPath();
                       var newLoc = new google.maps.LatLng(e.latLng.lat(), e.latLng.lng());
                       var studentLoc = new google.maps.LatLng(this.custom.studentLat, this.custom.studentLng);
                       origPoly.setAt(0, studentLoc);
                       origPoly.setAt(1, newLoc);
                    }); // drag
                 };  // if bus stop > 0
              };  // students loop
              
               this.style.borderWidth = "2px 2px 2px 2px";
               this.style.borderColor = "gold";             
           }
           else {
               this.style.borderWidth = "0px";
               var i;
               for (i=0; i<assignPolygons.length; i++) {
                  assignPolygons[i].setMap(null);
               }
               
               
           };
          
        }); // showAssignment
        
        $('#optimize').click( function() {
            //var newDesc = prompt("Optimize?:" , "");
		// if (newDesc != null) {        
		// };
            // get sorted bus stop array - sorted by quadrant and then by group number
        
	    var busStopJQarray = <?php echo json_encode($busStopArray); ?>;
	    var userSettings = <?php echo json_encode($userSettingsRow); ?>;
	
	    // sort by quadrant and then by group number
	    busStopJQarray.sort(function(a, b) {
                
	     if (a.quadrant != b.quadrant) 
	       return (a.quadrant < b.quadrant) ? -1 : 1;
	     if (a.group_number == b.group_number)
	        return 0;
	     return (a.group_number < b.group_number) ? -1 : 1;
	       
	    });
            
	    // pepare start (later also stop) point from bus depo lat lng
	    var userSettings = <?php echo json_encode($userSettingsRow); ?>;
            var busDepoLat = parseFloat(userSettings.bus_depo_lat);
            var busDepoLng = parseFloat(userSettings.bus_depo_lng);
            var busDepoLatLng = {lat: busDepoLat, lng: busDepoLng};
            
            // prepare school location
            var schoolLat = parseFloat(<?php echo $schoolLat; ?>);
            var schoolLng = parseFloat(<?php echo $schoolLng; ?>);
            var schoolLatLng = {lat: schoolLat, lng: schoolLng};
            
            var userName = <?php echo json_encode($userName); ?>;

	    
	    // loop quadrants and create routes per quadrant groups
	    var currentQuad = 1;
	    // var workGroup;
	    var directionsService = new google.maps.DirectionsService();

	    var infow = new google.maps.InfoWindow({});
	    
	
	    var polylines =[];
            var counter=0;
            var customDataArray = [];
	    for (currentQuad=1; currentQuad<userSettings.quadrant_number ; currentQuad++) {
	       var i=0;   
        
               // loop all groups for given quad and create route for each group
               while (busStopJQarray[0] && (busStopJQarray[0].quadrant == currentQuad))  {
     /////              
                var route_name ="My route" + counter;
             
		    // create new array of bus stops for route - first stop taken from bus depo and last stop is school (this is for to_school route)
		    // get a copy of group bus stops to prepate array of locations for route calculation
		 
		    // var workGroup;
		    // if bus stop array is empty then break for next quadrant
		    if (busStopJQarray[0]) {
		       workGroup = busStopJQarray[0].group_number;
		    }
		    else break;
		       
		       
		
		    for (i=0; i<busStopJQarray.length ; i++) {
		     // if (workGroup != busStopJQarray[i].group_number || busStopJQarray[i].quadrant != currentQuad)
		       if (workGroup != busStopJQarray[i].group_number)
		         break;
		    };
		    var workGroupNo = busStopJQarray[0].group_number;
		    
		    // now we have bus stops from location 0 to i-1 in busStopJQarray and we copy it to local array as prep for population stop points array
		    var workBusStopGroup = busStopJQarray.splice(0, i);
		    var waypts = [];
		    var j;
		    for (j=0; j<workBusStopGroup.length; j++) {
                       var newLoc = new google.maps.LatLng(workBusStopGroup[j].lat, workBusStopGroup[j].lng);
		       waypts.push({
	                 location: newLoc,
	                 stopover: true
	                });
		    }
		    
                   var customData = {counter: counter, workGroupNo: workGroupNo};   
                   customDataArray.push(customData);
		    

		    var request = {
		      origin: busDepoLatLng,
		      destination: schoolLatLng,
		      waypoints: waypts,
		      optimizeWaypoints: true,
		      travelMode: 'DRIVING'
		    };
		    
                    // var workGroupNo = workBusStopGroup[0].group_number;
                    
		    var routeCalled = 0;
		    
		    directionsService.route(request, function(response, status) {
		      if (status == google.maps.DirectionsStatus.OK) {
		      // var groupNdx = (workGroup % 100)/10;
                      var routeColor = routeStrokeColors[rendererCalled];
                      rendererCalled++;
                      if (rendererCalled >= routeStrokeColors.length)
                         rendererCalled  = 0;
                      
		      var directionsDisplay = new google.maps.DirectionsRenderer({polylineOptions: {strokeColor: routeColor, strokeWeight: 5}});
		        directionsDisplay.setDirections(response);
		        // console.log(directionsDisplay.getDirections());
		        
		        directionsDisplay.setMap(map);
		        
		        //var workGroupNoTest = workBusStopGroup[0].group_number;
		        //var counterTest = counter;
		      
			var polyDisplay = response.routes[0].overview_path;
			var stepPolyline = new google.maps.Polyline({strokeColor: routeColor, strokeWeight: 5, clickable: true, path: polyDisplay});
			stepPolyline.setMap(map);
			stepPolyline.setOptions({ zIndex: routeZIndex++ });
			
			google.maps.event.addListener(stepPolyline, 'click', function() {
			   // display route on top
			   this.setOptions({ zIndex: routeZIndex++ });
			});
			
			var customDataElement = customDataArray[routeCalled]; 
			var routeCounter = customDataArray[routeCalled].counter;
			var routeGroupNo = customDataArray[routeCalled].workGroupNo;
			routeCalled++;
			
			var routeType = "to_school"; // temp till read from table
			// ajax call to add route to db
			// var routePathEncoded = encodeURIComponent(response.routes[0]);
			var routePathJson = JSON.stringify(response.routes[0].overview_path);
		        var responseJson = JSON.stringify(response);
			var route0Json = JSON.stringify(response.routes[0]);
			var routeJB = response.routes[0];  // non strigified to be stored as blob
			
			// calculate total time and duration (seconds/meters) and number of stops
			var routeDistance= 0;
			var routeTime = 0;
			var routeStops = response.routes[0].legs.length;
			for(i = 0; i < response.routes[0].legs.length; i++){
			   routeDistance += parseFloat(response.routes[0].legs[i].distance.value);
			   routeTime += parseFloat(response.routes[0].legs[i].duration.value);
			       //for each 'leg'(route between two waypoints) we get the distance and add it to the total
			} 			
		
			
			// var tmpRoute = JSON.parse(routePathJson);
			// routePathJson = encodeURIComponent(routePathJson);
			// var routePathEncoded = google.maps.geometry.encoding.encodePath(response.routes[0]);
                       var data = {
                           //routePath: routePathJson,
                           func: "saveRoute",
                           responseJson: responseJson,
                           //routeJB: routeJB,
                           route0Json: route0Json,
                           routePath: routePathJson,
                           userName: userName,
                           schoolName: "<?php echo $schoolName; ?>",
                           groupNumber: routeGroupNo,
                           routeNumber: routeCalled,
                           routeType: routeType,
                           color: routeColor,
                           routeStops: routeStops,
                           routeDistance: routeDistance,
                           routeTime: routeTime
                           };
                   
                         $.ajax({
				  type: "POST",
				  dataType: "json",
				  url: "ajax.php", //Relative or absolute path to response.php file
				  data: data,
				  success: function(response) {
                                    //  counter=0;
					  //location.reload();					   
				  }
						  
	                  }); // ajax call  			
					        
		      }
		      else {
                            window.alert('Directions request failed due to ' + status);
                      }

		    });    
		             
		 counter++;
	    };  // while loop
	
	  }; // for quadrant loop
	  

        }); // optimize
       
    }); // function
        
	
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAw5gl1LJqMre1o3JztvMM7jK_qDbB5pBk&&sensor=false&callback=initMap&libraries=geometry"  async defer>
    </script>
 

<?php
 
};  // show school map
?>

</div>
</div>

</body>
</html>