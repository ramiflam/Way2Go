<?php
include './generalFunctions.php';

$db=getDbConnection();

if (mysqli_connect_errno()) 
{
	echo 'connection failed';
}

?>

<?php
$cookie_name = "userName";
$cookie_value = $_POST["userName"];
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
$nameErr =$passErr = "";
$userName =$_POST["userName"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <meta http-equiv="Content-Type" content="text/html/php" charset='utf-8' >
    <link rel="stylesheet" type="text/css" href="loginPage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script> 
    
    $(document).ready(function(){
     $("input[type="text"]").focus(function(){
        $(this).css("background-color", "red");
        });
    
    $("#userName").focus(function(){
        $(this).css("background-color", "red");
        });
        
    $("register-form input").focus(function(){
        $(this).css("background-color", "red");
        });
        
     $(":input").focus(function(){
        $(this).css("background-color", "red");
        });    
        
    $(":selected").focus(function(){
        $(this).css("background-color", "red");
        });     
        
    $("ul li:eq(0)").focus(function(){
        $(this).css("background-color", "red");
        });    
        
    });
    
</script>

 
</head>
<body>   
   
<img class='logo'src= "../assets/way2goLogo.png" height=120/>
     

<form class="register-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >     
    <h1>LOGIN</h1>
<ul>
    <li><span class='english'><input type="text" name="userName" placeholder="Name" id="userName" required></span></li><br>
    <li><span class='english'><input type="password" name="userPassword" placeholder="Password" id="userPassword" required></span></li><br>
    <li><button class='submit' type="submit">Submit</button></li>
</ul>
</form>
</body>
</html>

<?php

if($_SERVER['REQUEST_METHOD']=='POST')
{
$userName =$_POST["userName"];
$userPassword =$_POST["userPassword"];
$cookie_name = "userName";
$cookie_value = $_POST["userName"];
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
$loginValid=validateLogin($db, $userName, $userPassword);
    if($loginValid)
    {
    header('location:menuPage.php');
    }
    else
    {
    echo 'Failed';
    }

}

?>