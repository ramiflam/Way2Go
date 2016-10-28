<?php
include './generalFunctions.php';
include './navigationBar.php';
$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}

$userName=$_COOKIE["userName"];
// echo ("User Name: " . $userName ."\n");

$schoolName = "";

// $file = fopen("debug.txt","a");
$fileTimestamp = date('Ymd');
$file = fopen("debug_" . $fileTimestamp . ".txt","a");
// fwrite($file,"Hello World. Testing! \n");
if( isset($_POST['schoolNameSelect'])  OR isset($_GET['schoolNameSelect']) )  {
// echo("School Name Select is set");
$showStudents = true;
  if ($_POST['schoolNameSelect'])
      $schoolName = $_POST['schoolNameSelect'];
  if ($_GET['schoolNameSelect'])
      $schoolName = $_GET['schoolNameSelect'];
}
else {
// echo("School Name Select Not Set: " );
$showStudents = false;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Students Settings</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="settingsPage.css">
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    
<script>

  $(document).ready(function() {
    
    // hide/show new student form
    $("#newStudentDiv").hide();
  }); 
    
    
    $(function () {
      $('#schoolNameSubmit').click( function() {
        $(this).parent().parent().parent().find('[name=addStudentDiv]').show();
      });
    });
  
 
    $(function () {
      $('#addStudent').click( function() {
      // window.alert('addStudent clicked');
      $parentObject = $(this).parent().parent().parent().find('[name=newStudentDiv]');
        $(this).parent().parent().parent().parent().find('[name=newStudentDiv]').show();
      $('#cancelAddNewStudent').click( function() {
      $parentObject = $(this).parent().parent().parent().find('[name=newStudentDiv]');
        $(this).parent().parent().parent().parent().find('[name=newStudentDiv]').hide();       
      });
    });
  });
    
    // hide/show upload students button
    // $("#studentUpload").hide();
    $(function () {
      $('#schoolNameSubmit').click( function() {
        $(this).parent().find('[class=studentUpload]').show();
      });
    });
  
  
   $(function ()  {
   var moveLeft = 20;
   var moveDown = 10;
      $('#addStudent').mouseenter(function() { 
      $('div#tipAddStudent').show();
      })
      $('#addStudent').mouseleave(function () {
      $('div#tipAddStudent').hide();
      })   
      $('#addStudent').mousemove(function(e) {
      $('div#tipAddStudent').css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
      })
  });
  
   $(function ()  {
   var moveLeft = 10;
   var moveDown = 5;
      $('#studentUpload').mouseenter(function() { 
      $('div#tipUploadStudent').show();
      })
      $('#studentUpload').mouseleave(function () {
      $('div#tipUploadStudent').hide();
      })   
      $('#studentUpload').mousemove(function(e) {
      $('div#tipUploadStudent').css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
      })
  });
  
 
  
</script>		
</head>

<body>   

<!--  
<img class='logo'src= "../assets/way2goLogo.png" height=120/>
-->

<br><br><br>
<div class="content">
    <ul><a href="settingsGeneralPage.php"><li>GENERAL</li></a></ul>
    <ul><a href="settingsSchoolsPage.php"><li>SCHOOLS</li></a></ul>
    <ul><a href="settingsStudentsPage.php"><li>STUDENTS</li></a></ul>
</div>




<div class=studentsSettingsForm>

<div> 
    <h1>STUDENTS ( <?php echo $schoolName; ?> )</h1>
</div>

<div>
  <form name="schoolNameSelect" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" > 
    <td>Select a School:</td>
    <select name="schoolNameSelect" class= 'studentsSettingsForm'>
                    
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
</div>

<?php  
// display rest of form if showStudents is true
if ($showStudents) {

?>

<div id="addStudentDiv" class='addStudentDiv' name='addStudentDiv'>
    <h1><button id="addStudent" class='addStudent' type="button" name="addStudent"  input type="submit">+</button></h1>
</div>

<!-- <div id="studentUpload"> -->

    <h1><button id='studentUpload' type="button" name="studentUpload" class="studentUpload" input type="submit">Load Students</button> &nbsp <button id='resetQuad' type="button" name="resetQuad" class="resetQuad" input type="submit">Reset Quad</button> </h1>
    
<!-- </div> -->

<!--
<div id="resetQuad">
    <h1><button id='resetQuad' type="button" name="resetQuad" class="resetQuad" input type="submit">Reset Quad</button></h1>
</div>
-->

<div id="newStudentDiv" class="newStudentDiv" name="newStudentDiv">  
  <form name='studentsAddForm' method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
    <li>
      <label for="newStudent" >&nbsp Student Name &nbsp </label>
      <input type="text" id="studentName" name="studentName" required pattern="[A-Za-z0-9]{1,}" required >
    </li>
    <li>
      <label for="studentAddress" >&nbsp Student Address &nbsp </label>
      <input type="text" id="studentAddress" name="studentAddress" required pattern="[A-Za-z0-9]{1,}" required  >
    </li>
    <li>
      <label for="studentGrade" >&nbsp Student Grade &nbsp </label>
      <input type="text" id="studentGrade" name="studentGrade" required pattern="[A-Za-z0-9]{1,}" required  >
    </li>
    <li>
      <label for="studentSpecialNeeds" >&nbsp Special Needs &nbsp </label>
      <input type="text" id="studentSpecialNeeds" name="studentSpecialNeeds" required pattern="[A-Za-z0-9]{1,}" required  >
    </li> 
    <table>
        <h1><button id='saveNewStudent' type="button" name="saveNewStudent" class='submit' input type="submit">SAVE</button><button id='cancelAddNewStudent' type="button" name="cancelAddNewStudent" class='submit' input type="submit">CANCEL</button></h1>
    </table>
  </form>
</div>


<div id='studentsTableDiv' class='studentsTableDiv'>
  <table id='studentsTable' class='studentsTable'>
    <tr>
      <th class=studentsTable>School Name</th>
      <th class=studentsTable>Student Name</th>
      <th class=studentsTable>Student Address</th>
      <th class=studentsTable>Lat</th>
      <th class=studentsTable>Lng</th>
      <th class=studentsTable>Student Grade</th>
      <th class=studentsTable>Special Needs</th>
    </tr>

  <?php
  // Loop studentrecords and present it
  $schoolName = "";
  if ($_POST['schoolNameSelect'])
      $schoolName = $_POST['schoolNameSelect'];
  if ($_GET['schoolNameSelect'])
      $schoolName = $_GET['schoolNameSelect'];
      
  // get school lat,lng for later use
      $querySchools = "SELECT * FROM `user_schools` WHERE `user_name`='$userName';";
      $rowSchools=mysqli_fetch_array($querySchools,MYSQLI_ASSOC);
      $resultSchools = mysqli_query($db, $querySchools);
      $fetch_options = mysqli_fetch_array($resultSchools);
      $schoolLat = $fetch_options['lat'];
      $schoolLng = $fetch_options['lng'];

  // echo ('school name is: ' . $schoolName);
  $queryStudents = "SELECT * FROM `students` WHERE `school_name`= '$schoolName';";

  $rowStudents=mysqli_fetch_array($queryStudents,MYSQLI_ASSOC);
  $resultStudents = mysqli_query($db, $queryStudents);
  
  
    while ($rowStudents=mysqli_fetch_array($resultStudents)) {
      $schoolName = $rowStudents["school_name"];
      $studentName = $rowStudents["student_name"];
      $studentAddress= $rowStudents["student_address"];
      $studentLat = $rowStudents["lat"];
      $studentLng = $rowStudents["lng"];
      $studentGrade = $rowStudents["student_grade"];	
      $studentSpecialNeeds  = $rowStudents["student_special_needs"];
  ?>	

    <tr>
      <td class=studentsTable><?php echo $schoolName; ?></td>
      <td class=studentsTable><?php echo $studentName; ?></td>
      <td class=studentsTable><?php echo $studentAddress; ?></td>
      <td class=studentsTable><?php echo $studentLat; ?></td>
      <td class=studentsTable><?php echo $studentLng; ?></td>
      <td class=studentsTable><?php echo $studentGrade; ?></td>
      <td class=studentsTable><?php echo $studentSpecialNeeds; ?></td>
    </tr>
  
  <?php
  };
  ?>   
  
  </table>
</div>

</div>


<?php
// if ($showStudents)
}
?>

<div id= tipAddStudent> Add a New Student </div>
<div id= tipUploadStudent> Upload Students From CSV File </div>

<script>
   var flag = 0;
   $(function () {
   
      
     $('#studentUpload').click( function() {
     var schName = "<?php echo $schoolName; ?>";
          window.location.replace("selectStudentFile.php?schoolName=" + schName);
     });  

 
      $('#saveNewStudent').click( function() {
     // get lat and lng from address
       window.alert("entering save new student");
      var saveButton = $(this); // assign button object for later use when $this will mean other object
      var newStudentAddress = saveButton.parent().parent().find('[name=studentAddress]').val(); 
      window.alert(newStudentAddress);		      
      var studentLatLng;
      var geocoder = new google.maps.Geocoder();
      // window.alert("Entering loadAddress with address: " + schoolAddress);
      
      geocoder.geocode( { 'address': newStudentAddress}, function(results, status) {   
         if (status == google.maps.GeocoderStatus.OK) {
            if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
            // window.alert(results);
               studentLatLng = results[0].geometry.location;
               // window.alert(schoolLatLng);
            }
            else {
            window.alert("no result");
            }
         }
         else {
         window.alert("Status not ok");
         }
         
         var schoolLat = "<?php echo $schoolLat; ?>";
         var schoolLng = "<?php echo $schoolLng; ?>";
         var origin1 = new google.maps.LatLng(schoolLat, schoolLng);
         var destination1 = studentLatLng;
         
         var service = new google.maps.DistanceMatrixService();
	service.getDistanceMatrix(
	  {
	    origins: [origin1],
	    destinations: [destination1],
	    travelMode: google.maps.TravelMode.DRIVING
	  }, timeDistCallback);

	   var time2School = 0;
	   var distToSchool = 0;	
	   
	function timeDistCallback(response, status) {
	   
	   if (status == google.maps.DistanceMatrixStatus.OK) {
	     
	     distToSchool = response.rows[0].elements[0].distance.value;
	     time2School = response.rows[0].elements[0].duration.value;
	     
	     // ajax call to create new student
	     
	         //window.alert($schoolName);
	         var schName = "<?php echo $schoolName; ?>";
	         // make ajax call for school update
	         var data = {
	            func : 'saveNewStudent',
	               schoolName: schName ,
	 	       studentName: saveButton.parent().parent().find('[name=studentName]').val(),
	 	       studentAddress: saveButton.parent().parent().find('[name=studentAddress]').val(),
	 	       studentGrade: saveButton.parent().parent().find('[name=studentGrade]').val(),
	 	       studentSpecialNeeds: saveButton.parent().parent().find('[name=studentSpecialNeeds]').val(),
	 	       lat: studentLatLng.lat(),
	 	       lng: studentLatLng.lng(),
	 	       timeToSchool: time2School,
	 	       distToSchool: distToSchool
	            };
	           $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "ajax.php", //Relative or absolute path to response.php file
		      data: data,
		      success: function(data) {
	        		location.reload();
	              }
	      
	            }); // ajax call    	     
		     
	   }
	   else {
	      // wait for call back to be OK
	      // sleep (200);
	   }
	}
         
        // wait for call back to be executed
//         setTimeout(function(){  
         

//         }, 300);    
      });
      });  // saveNewStudent
      
      $('#resetQuad').click( function() {
      window.alert("entering reset quad");
          var schName = "<?php echo $schoolName; ?>";
	         var data = {
	            func : 'resetQuadrant',
	            schoolName: schName 
                 };
	           $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "ajax.php", //Relative or absolute path to response.php file
		      data: data,
		      success: function(data) {
	        		location.reload();
	              }
	      
	            }); // ajax call            
      });   //     resetQuad
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAw5gl1LJqMre1o3JztvMM7jK_qDbB5pBk" async defer></script> 
</body>
</html>