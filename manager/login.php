<?php
include "../common/common.php";

if(isset($_GET["action"])){
    if($_GET["action"]=="doit"){
        unset($_SESSION["admin_id"]);
        unset($_SESSION["admin_name"]);
        session_destroy();
        session_start();
        
        $user = $_POST["user"];
        $password = $_POST["passwd"];
        
        $user = $db->get_row("Select * from users where email='$user' and password='$password'");
        if($user==null){
            $_SESSION["response_msg"] = "User sau parola gresite!";
            redirTo("login.php");
        }else{
            $_SESSION["admin_id"] = $user->id;
            $_SESSION["admin_name"] = $user->name;
            $db->query("Update users set last_date=NOW(), last_ip='".$_SERVER["REMOTE_ADDR"]."' where id=".$user->id);
            redirTo("index.php");
        }
    }
    
    if($_GET["action"]=="logout"){
        unset($_SESSION["admin_id"]);
        unset($_SESSION["admin_name"]);
        session_destroy();
        session_start();        
        $_SESSION["response_msg"] = "Sesiunea de lucru a fost inchisa";
        redirTo("login.php");
    }
}


?>
<html>
<head>
<title>2P CMS - login</title>
<style type="text/css">
body{
    font-family: Verdana, sans-serif;
    font-size: 14px;
}

#login-form{
    width: 300px;
    margin: 200px auto 0 auto;
    background-color: #54514a;
    border: 10px solid #a8c306;
}

#login-form span{
    background-color: #4aa7ac;
    display: block;
    padding: 5px;
    color: #fff;
    font-weight: bold;
}

.clear {clear: both;}

#login-form label {
    width: 90px;
    float: left;
    padding: 5px;
    color: #fff;
    font-weight: bold;
}
#login-form input {
    width: 200px;
    float: left;
    border: 2px solid #54514a;
}

#login-form div.submit {
    padding-top: 10px;
    padding-bottom: 10px;
    text-align: center;
}

#login-form div.submit input {
    float: none;
    width: 100px;
}


div.alert_container {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 50px;
    line-height: 50px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    background-color: #FFE779;
    color: #54514a;
    border-bottom: 10px solid #D10400;
    display: none;
}


</style>
<script language="javascript" src="../common/jquery-1.3.2.min.js"></script>
</head>
<body>

<? displayServerMsg(); ?>

<form name="login" method="post" action="login.php?action=doit">

<div id="login-form">
    <span>2P CMS - LOGIN</span>
    <label for="user">user</label>
    <input type="text" name="user" id="user">
    <div class="clear"></div> 
    <label for="passwd">parola</label>    
    <input type="password" name="passwd" id="passwd">
    <div class="clear"></div>
    <div class="submit">
        <input type="submit" value="LOGIN" name="cmdSubmit">
        <div class="clear"></div>
    </div>
</div>

</form>

</body>
</html>