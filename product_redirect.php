<?php
include("common/common.php");

$id = (int)$_GET["id"];

$aff_url = $db->get_var("Select aff_url from shop_products where id=$id");

redirTo($aff_url);
?>