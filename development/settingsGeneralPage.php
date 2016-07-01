<?php
include './generalFunctions.php';
include './navigationBar.php';

$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}

$userName=$_COOKIE["userName"];
// echo ("User Name: " . $userName ."\n");

$query = "SELECT * FROM `user_settings` WHERE user_name='$userName';";

$result = mysqli_query($db, $query);
If ($result)
   {
    	$row = mysqli_fetch_assoc($result);
	// $returnMsg = $row["message"];
   }

$userZoning = "";
If    ($row["zoning"] == 'Y') {
	$userZoning= "checked";
}
$loadingTime = $row["loading_time"];
$loadingTimeDisabled = $row["loading_time_disabled"];
$timeLimitPickup = $row["time_limit_pickup"];
$timeLimitRelease = $row["time_limit_release"];
$quadrantTilt = $row["quadrant_tilt"];
$LOB = $row["LOB"];


?>


<!DOCTYPE html>
<html>
<head>
    <title>General Settings</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="settingsPage.css">
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>
<body>   

<script>

 function showVal(newVal,elmName, elmNameVal){
 
	  // window.alert(a);
	  if (elmName == 'loadingTime') {
	  	   document.getElementById(elmName).innerHTML= Math.round(newVal/5)*5 + " (Seconds)";    // round to closest 5 
	  	}
	  	else {
	  	   document.getElementById(elmName).innerHTML= newVal;
	  	}
 }

</script>

<!--   
<img class='logo' src= "../assets/way2goLogo.png" height=120/>
-->
<br><br><br>
<div class="content">
    <ul><a href="settingsGeneralPage.php"><li>GENERAL</li></a></ul>
    <ul><a href="settingsSchoolsPage.php"><li>SCHOOLS</li></a></ul>
    <ul><a href="settingsStudentsPage.php"><li>STUDENTS</li></a></ul>
</div>

<form name='settingsForm' class="generalSettingsForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
    <table>
        <h1>SETTINGS</h1>
        <tr>
            <td><label for="zoning" >&nbsp Include Zoning &nbsp </label></td>
            <td id="checkBox"><input type="checkbox" id="zoning" name="zoning" <?php echo $userZoning; ?> /><label for="zoning"><span></span></label></td>
        </tr>
        <tr>
            <td><label for="loadingTime" >&nbsp Loading Time &nbsp  </td>
            <td><span class="sliderVal" id="loadingTime"><?php echo $loadingTime?></span><br>
            <input type="range" id="loadingTimeslider" value="loadingTime" name="loadingTime" min=20 max=120 
            oninput="showVal(this.value, 'loadingTime', 'loadingTime')"  
            onchange="showVal(this.value, 'loadingTime', 'loadingTime')" /></td>
        </tr>  
        <tr>
            <td><label for="loadingTimeDisabled" > &nbsp Loading Time &nbsp <br> &nbsp (disabled) &nbsp </td>
            <td><span class="sliderVal" id="loadingTimeDisabled"><?php echo $loadingTimeDisabled?></span><br>
            <input type="range" id="loadingTimesliderDisabled" value="loadingTimeDisabled" name="loadingTimeDisabled" min=20 max=120 
            oninput="showVal(this.value, 'loadingTimeDisabled', 'loadingTimeDisabled')"  
            onchange="showVal(this.value,  'loadingTimeDisabled', 'loadingTimeDisabled')" /></td>
        </tr>
        <tr>
            <td><label for="timeLimitPickup" > &nbsp Time Limit for Route &nbsp <br> &nbsp (pick up) &nbsp </td>
            <td><span class="sliderVal" id="timeLimitPickup"><?php echo $timeLimitPickup?></span><br>
            <input type="range" id="timeLimitsliderPickup" value="timeLimitPickup" name="timeLimitPickup" min=20 max=120 
            oninput="showVal(this.value, 'timeLimitPickup', 'timeLimitPickup')"  
            onchange="showVal(this.value,  'timeLimitPickup', 'timeLimitPickup')" /></td>
        </tr>
        <tr>
            <td><label for="timeLimitRelease" > &nbsp Time Limit for Route &nbsp <br> &nbsp (release) &nbsp </td>
            <td><span class="sliderVal" id="timeLimitRelease"><?php echo $timeLimitRelease?></span><br>
            <input type="range" id="timeLimitsliderRelease" value="timeLimitRelease" name="timeLimitRelease" min=20 max=120 
            oninput="showVal(this.value, 'timeLimitRelease', 'timeLimitRelease')"  
            onchange="showVal(this.value,  'timeLimitRelease', 'timeLimitRelease')" /></td>
        </tr>
        <tr>
            <td><label for="quadrantTilt" > &nbsp Quadrant Tilt &nbsp <br> &nbsp </td>
            <td><span class="sliderVal" id="quadrantTilt"><?php echo $quadrantTilt?></span><br>
            <input type="range" id="quadrantTilt" value="quadrantTilt" name="quadrantTilt" min=0 max=89 
            oninput="showVal(this.value, 'quadrantTilt', 'quadrantTilt')"  
            onchange="showVal(this.value,  'quadrantTilt', 'quadrantTilt')" /></td>
        </tr>
        
        <tr>
            <td><label for="lineOfBusiness" > &nbsp Line of Business &nbsp </td>
            <td><select name="lineOfBusiness"><option value="School">School</option></select ></td>
        </tr>
        <tr>
            
        </tr>
    </table>   
    <table>
    <h1><button id='userSettings' type="button" name="save" class='submit' input type="submit">SAVE</button></h1>
    </table>
</form>


<script>
  var flag = 0;
  $(function () {
 //function updateDB()
  $('#userSettings').click( function(){
  // window.alert("button pressed");
  // var a = $(this).parent().find('[name=loadingTime]').val();
   window.alert($(this).parent().parent().parent().find('[name=quadrantTilt]').val());
 	    var data = {
 	    	func : 'updateSettings',
 	    	// userName : $(this).parent().parent().parent().attr('userName'),
 	    	zoning : $(this).parent().parent().parent().find('[name=zoning]')[0].checked,
 	    	loadingTime : $(this).parent().parent().parent().find('[name=loadingTime]').val(),
 	    	loadingTimeDisabled : $(this).parent().parent().parent().find('[name=loadingTimeDisabled]').val(),
 	    	timeLimitPickup : $(this).parent().parent().parent().find('[name=timeLimitPickup]').val(),
 	    	timeLimitRelease : $(this).parent().parent().parent().find('[name=timeLimitRelease]').val(),
 	    	quadrantTilt : $(this).parent().parent().parent().find('[name=quadrantTilt]').val(),
 	    	LOB: $(this).parent().parent().parent().find('[name=lineOfBusiness]').val()
 	    	};
 	    	
            // var data = {"action":"test"};
            // window.alert(data);
            $.ajax({
	      type: "POST",
	      dataType: "json",
	      url: "ajax.php", //Relative or absolute path to response.php file
	      data: data,
	      success: function(data) {
	       // window.alert(data);
        		location.reload();
              }
      
    });
    });
    });
    if (flag == 0){
    /*window.onbeforeunload = function (e) {
  var message = "You did not save your data",
  e = e || window.event;
  // For IE and Firefox
  if (e) {
    e.returnValue = message;
  }

  // For Safari
  return message;
};*/
};
</script>

</body>
</html>