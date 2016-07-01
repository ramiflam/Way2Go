<?php
include './generalFunctions.php';
include './navigationBar.php';

$db=getDbConnection();
if ($db->connect_error) {
echo '<p>connection failed</p>';
}
$userName=$_COOKIE["userName"];
// echo $userName; 

?>


<!DOCTYPE html>
<html>
<head>
    <title>Fleets Page</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="fleetsPage.css">
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    
    <script>

  $(document).ready(function() {
    
 
    // hide/show new vehicle form
    $("#newFleetDiv").hide();
    $(function () {
      $('#addFleet').click( function() {
      // window.alert('addStudent clicked');
       var parentObject = $(this).parent().parent().parent().find('[name=newFleetDiv]');
        $(this).parent().parent().parent().parent().find('[name=newFleetDiv]').show();
       
      });
    });
  });
  
</script>
    
</head>
<body>   

<!--   
<img class='logo'src= "../assets/way2goLogo.png" height=120/>
-->
<br><br><br>
<div class="content">
    <ul><a href="fleetsPage.php"><li>FLEET</li></a></ul>
    <ul><a href="fleetsDriversPage.php"><li>DRIVERS</li></a></ul>
    <ul><a href="fleetsType.php"><li>FLEET TYPE</li></a></ul>
</div>

<div class=fleetSettingsForm>

<div> 
    <h1>FLEET</h1>
</div>



<div id="addFleetDiv" class='addFleetDiv' name='addFleetDiv'>
    <h1><button id="addFleet" class='addFleet' type="button" name="addFleet"  input type="submit">+</button></h1>
</div>

<div id="fleetUpload">
    <h1><button id='fleetUpload' type="button" name="fleetUpload" class="fleetUpload" input type="submit">Load Fleet</button></h1>
</div>

<div id="newFleetDiv" class="newFleetDiv" name="newFleetDiv">  
  <form name='fleetAddForm' method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
    <li>
      <label for="vehicleID" >&nbsp Vehicle ID &nbsp </label>
      <input type="text" id="vehicleID" name="vehicleID" required pattern="[A-Za-z0-9]{1,}" required >
    </li>
    <li>
      <label for="vehicleName" >&nbsp Vehicle Name &nbsp </label>
      <input type="text" id="vehicleName" name="vehicleName" required pattern="[A-Za-z0-9]{1,}" required  >
    </li>

    <table>
        <h1><button id='saveNewFleet' type="button" name="saveNewFleet" class='saveNewFleet' input type="submit">SAVE</button></h1>
    </table>
  </form>
</div>


<div id='fleetTableDiv' class='fleetTableDiv'>
  <table id='fleetTable' class='fleetTable'>
    <tr>
      <th class=fleetTable>Vehicle ID</th>
      <th class=fleetTable>Vehicle Name</th>
    </tr>

  <?php
  // Loop fleet records and present it
/*  $schoolName = "";
  if ($_POST['schoolNameSelect'])
      $schoolName = $_POST['schoolNameSelect'];
  if ($_GET['schoolNameSelect'])
      $schoolName = $_GET['schoolNameSelect'];
  echo ('school name is: ' . $schoolName);
*/  
  
  $queryFleet = "SELECT * FROM `fleet`";
  $rowFleet=mysqli_fetch_array($queryFleet,MYSQLI_ASSOC);
  $resultFleet = mysqli_query($db, $queryFleet);
  
  
  while ($rowFleet=mysqli_fetch_array($resultFleet)) {
    $vehicleID= $rowFleet["vehicle_id"];
    $vehicleName = $rowFleet["vehicle_name"];
  ?>	

    <tr>
      <td class=fleetTable><?php echo $vehicleID; ?></td>
      <td class=fleetTable><?php echo $vehicleName; ?></td>
    </tr>
  
  <?php
  };
  ?>   
  
  </table>
</div>
</div>

 
<script>
   var flag = 0;
   $(function () {
     $('#fleetUpload').click( function() {
     var userName = "<?php echo $userName; ?>";
          window.location.replace("selectFleetFile.php?userName=" + userName);
     });  

 
      $('#saveNewFleet').click( function() {
         var userName = "<?php echo $userName; ?>";
         window.alert(userName);
         var saveButton = $(this);
         // make ajax call for fleet update
         var data = {
            func : 'saveNewFleet',
               userName: userName,
 	       vehicleName: saveButton.parent().parent().find('[name=vehicleName]').val(),
 	       vehicleID: saveButton.parent().parent().find('[name=vehicleID]').val()
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
      });
</script>


</body>
</html>