<?php
include './generalFunctions.php';
include './navigationBar.php';
$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}

$userName=$_COOKIE["userName"];
echo ("User Name: " . $userName ."\n");

$query = "SELECT * FROM `user_schools` WHERE `user_name`='$userName';";

$row=mysqli_fetch_array($result,MYSQLI_ASSOC);

?>

<?php
$result = mysqli_query($db, $query);

?>




<!DOCTYPE html>
<html>
<head>
    <title>Schools Settings</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="settingsPage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    
<script>    

   $(document).ready(function() {
      $("#newSchool").hide();	 
   });
   
   $(function () {
      $('#addSchool').click( function() {
      $(this).parent().parent().parent().find('[class=newSchool]').show();
      });
   });
   
   $(function () {
      $('#cancelAddSchool').click( function() {
      $(this).parent().parent().parent().find('[class=newSchool]').hide();
      });
   });   
	
   $(function ()  {
   var moveLeft = 20;
   var moveDown = 10;
      $('#deleteSchool').mouseenter(function() { 
      $('div#tipDeleteSchool').show();
      })
      $('#deleteSchool').mouseleave(function () {
      $('div#tipDeleteSchool').hide();
      })   
      $('#deleteSchool').mousemove(function(e) {
      $('div#tipDeleteSchool').css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
      })
  });
	
   $(function ()  {
   var moveLeft = 20;
   var moveDown = 10;
      $('#addSchool').mouseenter(function() { 
      $('div#tipAddSchool').show();
      })
      $('#addSchool').mouseleave(function () {
      $('div#tipAddSchool').hide();
      }) 
      $('#addSchool').mousemove(function(e) {
      $('div#tipAddSchool').css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
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

<div class="schoolsSettingsForm">
<form name='schoolsSettingsForm' method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate  >
    <h1>SCHOOLS</h1>
      
<div>
      
   <table>
     
<?php
$lineIndex=0;  
$result = mysqli_query($db, $query);

// Loop schools records and present it
 while ($row=mysqli_fetch_array($result)) {

	// set values to be presented
	$schoolName = $row["school_name"];
	$schoolAddress= $row["school_address"];
	
	$firstGrade  = "";
	If($row["first_grade"] == 'Y') {
		$firstGrade  = "checked";
	}
	$secondGrade= "";
	If    ($row["second_grade"] == 'Y') {
		$secondGrade= "checked";
	}
	$thirdGrade = "";
	If    ($row["third_grade"] == 'Y') {
		$thirdGrade = "checked";
	}
	$fourthGrade  = "";
	If($row["fourth_grade"] == 'Y') {
		$fourthGrade  = "checked";
	}
	$fifthGrade  = "";
	If($row["fifth_grade"] == 'Y') {
		$fifthGrade  = "checked";
	}
	$sixthGrade  = "";
	If($row["sixth_grade"] == 'Y') {
		$sixthGrade= "checked";
	}
	$seventhGrade  = "";
	If($row["seventh_grade"] == 'Y') {
		$seventhGrade  = "checked";
	}
	$eighthGrade  = "";
	If($row["eighth_grade"] == 'Y') {
		$eighthGrade  = "checked";
	}
	$ninthGrade  = "";
	If($row["ninth_grade"] == 'Y') {
		$ninthGrade  = "checked";
	}
	$tenthGrade  = "";
	If($row["tenth_grade"] == 'Y') {
		$tenthGrade  = "checked";
	}
	$eleventhGrade  = "";
	If($row["eleventh_grade"] == 'Y') {
		$eleventhGrade  = "checked";
	}
	$twelfthGrade  = "";
	If($row["twelfth_grade"] == 'Y') {
		$twelfthGrade  = "checked";
	}
	
?>

  
    <tr index='<?php echo $lineIndex?>'>
    <td id="del" class="del">    
        <li id="schoolName" name="schoolName"> &nbsp <?php echo $schoolName; ?> &nbsp</li>
        <li id="schoolAddress" name="schoolAddress"><?php echo $schoolAddress; ?></li>
        <br>
        <li><input type="checkbox" id="firstGradeS" name="firstGrade" <?php echo $firstGrade; ?> disabled /> <label for="firstGrade">&nbsp 1st &nbsp <span></span></label></li>
        <li><input type="checkbox" id="secondGradeS" name="secondGrade" <?php echo $secondGrade; ?> disabled  /> <label for="secondGrade">&nbsp 2nd &nbsp <span></span></label></li>
        <li><input type="checkbox" id="thirdGradeS" name="thirdGrade" <?php echo $thirdGrade; ?> disabled /> <label for="thirdGrade">&nbsp 3rd &nbsp <span></span></label></li>
        <li><input type="checkbox" id="fourthGradeS" name="fourthGrade" <?php echo $fourthGrade; ?> disabled /> <label for="fourthGrade">&nbsp 4th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="fifthGradeS" name="fifthGrade" <?php echo $fifthGrade; ?> disabled /> <label for="fifthGrade">&nbsp 5th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="sixthGradeS" name="sixthGrade" <?php echo $sixthGrade; ?> disabled /> <label for="sixthGrade">&nbsp 6th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="seventhGradeS" name="seventhGrade" <?php echo $seventhGrade; ?> disabled /> <label for="seventhGrade">&nbsp 7th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="eighthGradeS" name="eighthGrade" <?php echo $eighthGrade; ?> disabled /> <label for="eighthGrade">&nbsp 8th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="ninthGradeS" name="ninthGrade" <?php echo $ninthGrade; ?> disabled /> <label for="ninthGrade">&nbsp 9th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="tenthGradeS" name="tenthGrade" <?php echo $tenthGrade; ?> disabled /> <label for="tenthGrade">&nbsp 10th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="eleventhGradeS" name="eleventhGrade" <?php echo $eleventhGrade; ?> disabled /> <label for="eleventhGrade">&nbsp 11th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="twelfthGradeS" name="twelfthGrade" <?php echo $twelfthGrade; ?> disabled /> <label for="twelfthGrade">&nbsp 12th &nbsp <span></span></label></li>

        <li> &nbsp <button id="deleteSchool" type="button" name="deleteSchool" class="deleteSchool" input type="submit"></button></     
    </td>
    </tr>


 <?php 
 $lineIndex = $lineIndex+1; }
 ?>
 
   </table>      
   
        <h1><button id='addSchool' type="button" name="addSchool" class='addSchool' input type="submit">+</button></h1>
            
</div>

  

<div id="newSchool" class="newSchool">  
        <li>
            <label for="schoolName" >&nbsp School Name &nbsp </label>
            <input type="text" id="schoolName" name="schoolName" required pattern="[A-Za-z0-9]{1,}" required >
        </li>
        <li>
            <label for="schoolAddress" >&nbsp School Address &nbsp </label>
            <input type="text" id="schoolAddress" name="schoolAddress" required pattern="[A-Za-z0-9]{1,}" required  >
        </li>
         <br>
        <li><input type="checkbox" id="firstGrade" name="firstGrade" /> <label for="firstGrade">&nbsp 1st &nbsp <span></span></label></li>
        <li><input type="checkbox" id="secondGrade" name="secondGrade" /> <label for="secondGrade">&nbsp 2nd &nbsp <span></span></label></li>
        <li><input type="checkbox" id="thirdGrade" name="thirdGrade" /> <label for="thirdGrade">&nbsp 3rd &nbsp <span></span></label></li>
        <li><input type="checkbox" id="fourthGrade" name="fourthGrade" /> <label for="fourthGrade">&nbsp 4th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="fifthGrade" name="fifthGrade" /> <label for="fifthGrade">&nbsp 5th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="sixthGrade" name="sixthGrade" /> <label for="sixthGrade">&nbsp 6th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="seventhGrade" name="seventhGrade" /> <label for="seventhGrade">&nbsp 7th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="eighthGrade" name="eighthGrade" /> <label for="eighthGrade">&nbsp 8th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="ninthGrade" name="ninthGrade" /> <label for="ninthGrade">&nbsp 9th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="tenthGrade" name="tenthGrade" /> <label for="tenthGrade">&nbsp 10th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="eleventhGrade" name="eleventhGrade" /> <label for="eleventhGrade">&nbsp 11th &nbsp <span></span></label></li>
        <li><input type="checkbox" id="twelfthGrade" name="twelfthGrade" /> <label for="twelfthGrade">&nbsp 12th &nbsp <span></span></label></li>
        
    <table>
        <h1><button id='saveNewSchool' type="button" name="saveNewSchool" class='submit' input type="submit">SAVE</button><button id='cancelAddSchool' type="button" name="cancelAddSchool" class='submit' input type="submit">CANCEL</button></h1>
    </table>
    
</div>
</form>
</div>

<div id= tipDeleteSchool> Delete This School </div>
<div id= tipAddSchool> Add a New School </div>
 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAw5gl1LJqMre1o3JztvMM7jK_qDbB5pBk" async defer></script> 
 
<script>
   var flag = 0;
   $(function () {
 
      $('#saveNewSchool').click( function() {
     // get lat and lng from address
       
      var saveButton = $(this); // assign button object for later use when $this will mean other object
      var newSchoolAddress= $(this).parent().parent().find('[name=schoolAddress]').val(); 
      window.alert(schoolAddress);		      
      var schoolLatLng;
      var geocoder = new google.maps.Geocoder();
      // window.alert("Entering loadAddress with address: " + schoolAddress);
      
      geocoder.geocode( { 'address': newSchoolAddress}, function(results, status) {   
         if (status == google.maps.GeocoderStatus.OK) {
            if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
            // window.alert(results);
               schoolLatLng = results[0].geometry.location;
               // window.alert(schoolLatLng);
            }
            else {
            window.alert("no result");
            }
         }
         else {
         window.alert("Status not ok");
         }
         
         
         // make ajax call for school update
         // window.alert(saveButton.parent().parent().find('[name=firstGrade]')[0].checked);
         var data = {
            func : 'saveNewSchool',
 	       schoolName: saveButton.parent().parent().find('[name=schoolName]').val(),
 	       schoolAddress: saveButton.parent().parent().find('[name=schoolAddress]').val(),
 	       firstGrade: saveButton.parent().parent().find('[name=firstGrade]')[0].checked,
 	       secondGrade: saveButton.parent().parent().find('[name=secondGrade]')[0].checked,
 	       thirdGrade: saveButton.parent().parent().find('[name=thirdGrade]')[0].checked,
 	       fourthGrade: saveButton.parent().parent().find('[name=fourthGrade]')[0].checked,
 	       fifthGrade: saveButton.parent().parent().find('[name=fifthGrade]')[0].checked,
 	       sixthGrade: saveButton.parent().parent().find('[name=sixthGrade]')[0].checked,
 	       seventhGrade: saveButton.parent().parent().find('[name=seventhGrade]')[0].checked,
 	       eighthGrade: saveButton.parent().parent().find('[name=eighthGrade]')[0].checked,
 	       ninthGrade: saveButton.parent().parent().find('[name=ninthGrade]')[0].checked,
 	       tenthGrade: saveButton.parent().parent().find('[name=tenthGrade]')[0].checked,
 	       eleventhGrade: saveButton.parent().parent().find('[name=eleventhGrade]')[0].checked,
 	       twelfthGrade: saveButton.parent().parent().find('[name=twelfthGrade]')[0].checked,
 	       lat: schoolLatLng.lat(),
 	       lng: schoolLatLng.lng()
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
      });
      });  // saveNewSchool
       

      $('.deleteSchool').click( function(){
      
      // window.alert("delete school");
      var e = $(this).parent().parent().find('[name=schoolName]');
      // var index = '<?php echo $lineIndex; ?>';
      window.alert(e.text());
      
 	    var data = {
 	    	func : 'deleteSchool',
 	    	schoolDeleteName: e.text().trim(),
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
	    	
      
      }); // deleteSchool
      
 /***     
      $('.del').click ( function() {
         var e = $(this).parent().find('[name=schoolName]');
         window.alert (e.text());
      });
     ***/
     
    });  // function 
    
    
    if (flag == 0){
    };
</script>

</body>
</html>