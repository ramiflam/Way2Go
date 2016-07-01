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
    <title>Fleet Type</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="fleetsPage.css">
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    
    <script>

  $(document).ready(function() {
      
    // hide/show new vehicle form
    $("#newFleetTypeDiv").hide();
    $(function () {
      $('#addFleetType').click( function() {
      
      var parentObject = $(this).parent().parent().parent().find('[name=newFleetTypeDiv]');
        $(this).parent().parent().parent().parent().find('[name=newFleetTypeDiv]').show();
       
      });
    });
  });
  
</script>
    
</head>
<body>
<br><br><br>
<div class="content">
    <ul><a href="fleetsPage.php"><li>FLEET</li></a></ul>
    <ul><a href="fleetsDriversPage.php"><li>DRIVERS</li></a></ul>
    <ul><a href="fleetsType.php"><li>FLEET TYPE</li></a></ul>
</div>  

<div class=fleetSettingsForm>

<div> 
    <h1>FLEETS TYPE</h1>
</div>


<div id="addFleetTypeDiv" class='addFleetTypeDiv' name='addFleetTypeDiv'>
    <h1><button id="addFleetType" class='addFleetType' type="button" name="addFleetType"  input type="submit">+</button></h1>
</div>

<div id="newFleetTypeDiv" class="newFleetTypeDiv" name="newFleetTypeDiv">  
  <form name='newFleetTypeDiv' method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
    <li>
      <label for="vehicleName" >&nbsp Vehicle Name &nbsp </label>
      <input type="text" id="vehicleName" name="vehicleName" required pattern="[A-Za-z0-9]{1,}" required >
    </li>
    <li>
      <label for="vehicleCapacity" >&nbsp Vehicle Capicity &nbsp </label>
      <input type="text" id="vehicleCapacity" name="vehicleCapacity" required pattern="[A-Za-z0-9]{1,}" required  >
    </li>
        <li>
      <label for="vehicleSpecialNeeds" >&nbsp Special Needs &nbsp </label>
      <input type="text" id="vehicleSpecialNeeds" name="vehicleSpecialNeeds" required pattern="[A-Za-z0-9]{1,}" required  >
    </li>

    <table>
        <h1><button id='saveNewVehicleType' type="button" name="saveNewVehicleType" class='saveNewVehicleType' input type="submit">SAVE</button></h1>
    </table>
  </form>
</div>

<div id='fleetTypeTableDiv' class='fleetTypeTableDiv'>
  <table id='fleetTypeTable' class='fleetTypeTable'>
    <tr>
      <th class=fleetTypeTable>Vehicle Name</th>
      <th class=fleetTypeTable>Capacity</th>
      <th class=fleetTypeTable>Special Needs</th>
    </tr>

  <?php
  
  
  $queryVehicleType = "SELECT * FROM `vehicle_type`";
  $rowVehicleType = mysqli_fetch_array($queryVehicleType, MYSQLI_ASSOC);
  $resultVehicleType = mysqli_query($db, $queryVehicleType);
  
  
  while ($rowVehicleType =mysqli_fetch_array($resultVehicleType)) {
    $vehicleName= $rowVehicleType ["vehicle_name"];
    $vehicleCapacity = $rowVehicleType ["vehicle_capacity"];
    $vehicleSpecialNeeds = $rowVehicleType ["special_needs"];
  ?>	

    <tr>
      <td class= fleetTypeTable><?php echo $vehicleName; ?></td>
      <td class= fleetTypeTable><?php echo $vehicleCapacity ; ?></td>
      <td class= fleetTypeTable><?php echo $vehicleSpecialNeeds ; ?></td>
    </tr>
  
  <?php
  };
  ?>   
  
  </table>
</div>
</div>

<script>
   var flag = 0;
    
      $('#saveNewVehicleType').click( function() {
         var userName = "<?php echo $userName; ?>";
         window.alert(userName);
         var saveButton = $(this);
         // make ajax call for fleet update
         var data = {
            func : 'saveNewVehicleType',
               userName: userName,
 	       vehicleName: saveButton.parent().parent().find('[name=vehicleName]').val(),
 	       vehicleCapacity: saveButton.parent().parent().find('[name=vehicleCapacity]').val(),
 	       vehicleSpecialNeeds: saveButton.parent().parent().find('[name=vehicleSpecialNeeds]').val()
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

</script>


</body>
</html>