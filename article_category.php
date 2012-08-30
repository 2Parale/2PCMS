<?php
include("common/common.php");

$slug = "";
if(isset($_GET["slug"])){$slug = mysql_real_escape_string($_GET["slug"]);}
if($slug==""){redirTo($_CONFIG["urlpath"]);}

//page content
$pacategory = $db->get_row("Select * from article_categories where category_url='$slug' limit 1");
if($pacategory==null){redirTo($_CONFIG["urlpath"]);}
$particles = $db->get_results("Select * from articles where category_id=".$pacategory->id);
$db->query("Update LOW_PRIORITY article_categories set shows=shows+1 where id=".$pacategory->id);

//breadcrumb
addPagePathText("Articole din categoria " . $pacategory->category);


#page constants
$page_heading = $pacategory->category;
$page_title = $pacategory->category . " - " . $_CONFIG["site_name"];
$page_metakey = "";
$page_metadesc = "Articole din categoria " . $pacategory->category;
$canonical_url = $_CONFIG["urlpath"] . "articole/" . $pacategory->category . "/";


$arListedProducts = array();

#process header + footer info
include("include/process-page-info.php");




//include template
include ("templates/".$_CONFIG["template"]."/header.php");
include ("templates/".$_CONFIG["template"]."/article_category.php");
include ("templates/".$_CONFIG["template"]."/footer.php");


//increase show count
if($arListedProducts!=null){
    $db->query("Update LOW_PRIORITY shop_products set show_inlisting=show_inlisting+1 where id in (".implode(",",$arListedProducts).")");        
}
?>