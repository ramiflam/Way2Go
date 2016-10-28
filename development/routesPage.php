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

<!--
  $(document).ready(function() {
    
    // hide/show school map
    $(function () {
      $('#schoolNameSubmit').click( function() {
        $(this).parent().parent().parent().find('[name=schoolMap]').show();
        $(this).parent().parent().parent().find('[name=groupButton]').show();
      });
    });
    });
-->

    
    
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
      <input type = "submit" type="button" id="schoolNameSubmit" class="schoolNameSubmit"> &nbsp &nbsp &nbsp <button id='groupStudents' type="button" name="groupStudents" class='submit' input type="submit">GROUP</button> &nbsp &nbsp &nbsp <button id='assignStudents' type="button" name="assignStudents" class='submit' input type="submit">ASSIGN</button>

      
 </form> 
 </div> 
  




<div id="map" class="mapSnippet">

<?php 
 if ($showSchoolMap) {
    
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
      	      $studentRec = array();
	      $studentRec['student_address'] = $studentRow['student_address'];
	      $studentRec['lat'] = $studentRow['lat'];
	      $studentRec['lng'] = $studentRow['lng'];
	      $studentRec['student_name'] = $studentRow['student_name'];
	      $studentRec['title'] = $studentRow['student_name'] . " " . $studentRow['student_address'] . " Grade: " . $studentRow['student_grade'] . " Quad: " . $studentRow['quadrant'] . " GRP: " . $studentRow['student_group'] . " bearing:" . $studentRow['bearing'];
	      $studentRec['quadrant'] = $studentRow['quadrant'];
	      $studentRec['student_group'] = $studentRow['student_group'];
/**
      ob_start();
      var_dump($studentRec);
      $studentDump = ob_get_clean();
      fwrite($file,"student record = " . $studentDump . "\n" );
      **/

	      
	      // add record to array
	      $studnetArray[] = $studentRec;
	      $i++;
	
      }
/***      
      ob_start();
      var_dump($studnetArray);
      $arrDump = ob_get_clean();
      fwrite($file,"student array = " . $arrDump  . "\n" );
       
 ***/     
 
 
      // load bus stops from db to local array
      $queryBusStop = "SELECT * FROM `school_bus_stops` WHERE `user_name` = '$userName' and `school_name` = '$schoolName' ;";
      $rowBusStop = mysqli_fetch_array($queryBusStop,MYSQLI_ASSOC);
      $resultBusStop = mysqli_query($db, $queryBusStop);
      
      $timestamp = date('m/d/Y h:i:s');
      fwrite($file,'['. $timestamp. ']: ' . $queryBusStop . "\n"); 
 	        
      $busStopArray = array();    
      
      while ($busStopRow = mysqli_fetch_array($resultBusStop)) 
      {
      	      $busStopRec = array();
	      $busStopRec['id'] = $busStopRow['id'];
	      $busStopRec['lat'] = $busStopRow['lat'];
	      $busStopRec['lng'] = $busStopRow['lng'];
	      $busStopRec['description'] = $busStopRow['description'];
	      $busStopRec['title'] = $busStopRow['description'] . " Loc: " . $busStopRow['lat'] .  " : " . $busStopRow['lng'];
	      
	      // add record to array
	      $busStopArray[] = $busStopRec;
	
      }
      
 /**    
      ob_start();
      var_dump($busStopArray);
      $bsDump = ob_get_clean();
      fwrite($file,"bus stop array = " . $bsDump  . "\n" );
 **/      
  
//die();   
 ?>
 
    <script>
    var map;
      function initMap() {
        var showMap = <?php echo $showSchoolMap; ?>;
        var schoolLat = <?php echo $schoolLat; ?> ;
        var schoolLng = <?php echo $schoolLng; ?> ;
        var schoolTitle = "<?php echo $schoolTitle; ?>" ;
        
        if (showMap) {
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
						  dataType: "json",
						  url: "ajax.php", //Relative or absolute path to response.php file
						  data: data,
						  success: function(response) {
								console.log(response);
							  }
				  
						}); // ajax call  
						// need to update description here to new value
						this.desc = newDesc;
						var newTitle = newDesc + ' Loc: ' + lat + ' : ' + lng;
						this.setTitle(newTitle);
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
			if(desc != ''){
				
				
				var busStopMarker = new google.maps.Marker({
				  position: location,
				  map: map,
				  desc: desc,
				  icon: micon
				});
				
			   busStopMarker.setMap(map);
			   infowindow.setContent(desc); 
			   infowindow.open(map,busStopMarker);
			   
			   var data = {
							func : 'insertBusStop',
							schoolName: "<?php echo $schoolName; ?>" ,
							userName: '<?php echo $userName; ?>',
							lat: lat,
							lng: lng,
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
				busStopMarker.setTitle(desc + ' ' + lat + ':' + lng);
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
        	     groupH = Math.floor(inputStudentGroup/100);
        	     var groupMod = inputStudentGroup % 100 ;  //+ (groupH*10);
        	     if (groupMod == 0 || groupMod == 60) {
        	        return "../assets/blue-dot.png";
        	     }
        	     if (groupMod == 10 || groupMod == 70) {
        	        return "../assets/orange-dot.png";
        	     }
        	     if (groupMod == 20 || groupMod == 80) {
        	        return "../assets/purple-dot.png";
        	     }
        	     if (groupMod == 30 || groupMod == 90) {
        	        return "../assets/green-dot.png";
        	     }
        	     if (groupMod == 40) {
        	        return "../assets/pink-dot.png";
        	     }
        	     if (groupMod == 50) {
        	        return "../assets/ltblue-dot.png";
        	     }
        	     
        	  }
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
       $('#groupStudents').click( function() {
          // window.alert('groupStudents clicked:');
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
       
    }); // function
    

    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAw5gl1LJqMre1o3JztvMM7jK_qDbB5pBk&&sensor=false&callback=initMap"  async defer>
    </script>
    
<?php
 
};
?>

</div>
</div>

</body>
</html>