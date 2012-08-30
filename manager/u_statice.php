<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



$page_name = "u_statice";
$page_title = "Pagini statice";
include "include/header.php";
?>








<?php
include "include/footer.php";
?>
