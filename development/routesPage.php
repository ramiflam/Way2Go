<?php
include './generalFunctions.php';
include './navigationBar.php';

$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}

$userName=$_COOKIE["userName"];

$schoolName = "";

// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("debug_" . $fileTimestamp . ".txt","a");
fwrite($file,"POST[schoolNameSelect = " . $_POST['schoolNameSelect'] . "\n" );
//fwrite($file,"GET[schoolNameSelect = " . $_GET['schoolNameSelect'] . "\n" );

if( isset($_POST['schoolNameSelect']))  {
echo("School Name Select is set");
 $showSchoolMap = true;
 $schoolName=$_POST['schoolNameSelect'];
}
else {
 echo("School Name Select Not Set: " );
  $showSchoolMap = false;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Routes Page</title>
  <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
  <link rel="stylesheet" type="text/css" href="routesPage.css">
  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>

<!--  
<script>
  $(document).ready(function() {
    
    // hide/show school map
    $(function () {
      $('#schoolNameSubmit').click( function() {
        $(this).parent().parent().parent().find('[name=schoolMap]').show();
      });
    });
</script>
-->
		
</head>
<body>   

<br><br><br>
<!--
<div class="content">
    <ul><a href="settingsGeneralPage.php"><li>GENERAL</li></a></ul>
</div>
-->

<div class = "routeSettingsForm" >

 
    <h1>ROUTES</h1>


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
      <input type = "submit" type="button" id="schoolNameSubmit" class="schoolNameSubmit">
  </form>


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
      
      // load students infor into array
      
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
	      $studentRec['title'] = $studentRow['student_name'] . " " . $studentRow['student_address'] . " Grade: " . $studentRow['student_grade'] . " Quad: " . $studentRow['quadrant'] . " GRP: " . $studentRow['student_group'];
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
     
 ?>
 
    <script>

      function initMap() {
        var showMap = <?php echo $showSchoolMap; ?>;
        var schoolLat = <?php echo $schoolLat; ?> ;
        var schoolLng = <?php echo $schoolLng; ?> ;
        var schoolTitle = "<?php echo $schoolTitle; ?>" ;
        
        if (showMap) {
        // create map per school location
	        var myLatLng = {lat: schoolLat, lng: schoolLng};
	        var mapDiv = document.getElementById('map');
	        var map = new google.maps.Map(mapDiv, {
	          center: myLatLng,
	          zoom: 14
	        });
	// add school marker
	        var marker = new google.maps.Marker({
	    	  position: myLatLng,
	    	  map: map,
	    	  title: schoolTitle
	        });
	 // add students markers 
         var studentjQArray= <?php echo json_encode($studnetArray); ?>;

	         for(var i=0; i<studentjQArray.length; i++){
	            var studentLat = parseFloat(studentjQArray[i].lat);
	            var studentLng = parseFloat(studentjQArray[i].lng);
	            var studentLatLng = {lat: studentLat, lng: studentLng};
	            var studentQuad = studentjQArray[i].quadrant;
	            var studentGroup = studentjQArray[i].student_group;
	            var studentIcon = getStudentIcon(studentGroup);
	            var studentMarker = new google.maps.Marker({
	    	    position: studentLatLng,
	    	    map: map,
	    	    icon: studentIcon,
	    	    title: studentjQArray[i].title
	          });
	            
	         }	        
	    };  // show map
        };
        
        
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
        };
 
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