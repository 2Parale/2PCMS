<?php
include("common/common.php");


#page constants
$page_heading = "Bine ai venit pe " . $_CONFIG["site_name"];
$page_title = $_CONFIG["site_name"];
$page_metakey = "";
$page_metadesc = "";
$canonical_url = $_CONFIG["urlpath"];

$arListedProducts = array();

#process header + footer info
include("include/process-page-info.php");

//page content
$nprods = $db->get_results("Select SQL_CACHE * from shop_products where active=1 order by create_date desc limit " . $_CONFIG["settings"]["produse_index_noi_count"]);
$vprods = $db->get_results("Select SQL_CACHE * from shop_products where active=1 order by click*10+show_inpage*5+show_inlisting desc limit " . $_CONFIG["settings"]["produse_index_visited_count"]);

//include template
include ("templates/".$_CONFIG["template"]."/header.php");
include ("templates/".$_CONFIG["template"]."/index.php");
include ("templates/".$_CONFIG["template"]."/footer.php");


//increase show count
if($arListedProducts!=null){
    $db->query("Update LOW_PRIORITY shop_products set show_inlisting=show_inlisting+1 where id in (".implode(",",$arListedProducts).")");        
}
?>
