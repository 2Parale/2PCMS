<?php
session_start();


//DEFINITIONS
//db configs
$_CONFIG["user"] = "root";
$_CONFIG["pass"] = '';
$_CONFIG["db"] = "2pcms";
$_CONFIG["host"] = "localhost";

//DB INCLUDE
include ("class.sql.php");

//Version Include
include ("version.php");

//path configs
$_CONFIG["urlpath"] = "http://localhost/2pcms-clean/";

//listing description
$listing_description = "";

//settings
$setts = $db->get_results("Select * from settings");
if($setts!=null){
    foreach($setts as $sett){
        $_CONFIG["settings"][$sett->vlabel] = $sett->vvalue;
    }
}

//records per page for front end listing
$_CONFIG["rec_per_page"] = $_CONFIG["settings"]["record_per_page"];

//meta stuff
$_CONFIG["base_meta_key"] = $_CONFIG["settings"]["meta_keywords"];
$_CONFIG["base_meta_desc"] = $_CONFIG["settings"]["meta_description"];

//other settings
$_CONFIG["site_name"] = $_CONFIG["settings"]["website_title"];

//mail config
$_CONFIG["email_from"] = $_CONFIG["settings"]["website_email"];

//template
$_CONFIG["template"] = $_CONFIG["settings"]["website_template"];

//final include
include ("global.functions.php");
?>