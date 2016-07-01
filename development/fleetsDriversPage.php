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
    <title>Drivers Page</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="fleetsPage.css">
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    
    <script>

  $(document).ready(function() {
      
    // hide/show new vehicle form
    $("#newDriverDiv").hide();
    $(function () {
      $('#addDriver').click( function() {
      // window.alert('addStudent clicked');
      var parentObject = $(this).parent().parent().parent().find('[name=newDriverDiv]');
        $(this).parent().parent().parent().parent().find('[name=newDriverDiv]').show();
       
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
    <ul><a href="fleetDriversPage.php"><li>DRIVERS</li></a></ul>
    <ul><a href="fleetsType.php"><li>FLEET TYPE</li></a></ul>
</div>

<div class=fleetSettingsForm>

<div> 
    <h1>DRIVERS</h1>
</div>



<div id="addDriverDiv" class='addDriverDiv' name='addDriverDiv'>
    <h1><button id="addDriver" class='addDriver' type="button" name="addDriver"  input type="submit">+</button></h1>
</div>

<div id="driverUpload">
    <h1><button id='driverUpload' type="button" name="driverUpload" class="driverUpload" input type="submit">Load Driver</button></h1>
</div>

<div id="newDriverDiv" class="newDriverDiv" name="newDriverDiv">  
  <form name='fleetAddForm' method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
    <li>
      <label for="driverID" >&nbsp Driver ID &nbsp </label>
      <input type="text" id="driverID" name="driverID" required pattern="[A-Za-z0-9]{1,}" required >
    </li>
    <li>
      <label for="driverCell" >&nbsp Driver Cell &nbsp </label>
      <input type="text" id="driverCell" name="driverCell" required pattern="[A-Za-z0-9]{1,}" required  >
    </li>

    <table>
        <h1><button id='saveNewDriver' type="button" name="saveNewDriver" class='saveNewDriver' input type="submit">SAVE</button></h1>
    </table>
  </form>
</div>


<div id='driverTableDiv' class='driverTableDiv'>
  <table id='driverTable' class='driverTable'>
    <tr>
      <th class=driverTable>Driver ID</th>
      <th class=driverTable>Driver Cell</th>
    </tr>

  <?php
  
  
  $queryDriver = "SELECT * FROM `drivers`";
  $rowDriver=mysqli_fetch_array($queryDriver,MYSQLI_ASSOC);
  $resultDriver = mysqli_query($db, $queryDriver);
  
  
  while ($rowDriver=mysqli_fetch_array($resultDriver)) {
    $driverID= $rowDriver["driver_id"];
    $driverCell = $rowDriver["driver_cell"];
  ?>	

    <tr>
      <td class=driverTable><?php echo $driverID; ?></td>
      <td class=driverTable><?php echo $driverCell; ?></td>
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
     $('#driverUpload').click( function() {
     var userName = "<?php echo $userName; ?>";
          window.location.replace("selectDriverFile.php?userName=" + userName);
     });  

 
      $('#saveNewDriver').click( function() {
         window.alert(userName);
         var userName = "<?php echo $userName; ?>";
         // make ajax call for fleet update
         var saveButton = $(this); 
         var data = {
            func : 'saveNewDriver',
               userName: userName,
 	       driverID: saveButton.parent().parent().parent().find('[name=driverID]').val(),
 	       driverCell: saveButton.parent().parent().parent().find('[name=driverCell]').val(),
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