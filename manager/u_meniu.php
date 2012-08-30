<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



$page_name = "u_meniu";
$page_title = "Administrare meniu website";
include "include/header.php";
?>








<?php
include "include/footer.php";
?>
