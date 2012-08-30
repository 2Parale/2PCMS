<?php
include("common/common.php");

$id = 0;
if(isset($_GET["id"])){$id=(int)$_GET["id"];}
if($id==0){redirTo($_CONFIG["urlpath"]);}

//page content
$oarticle = $db->get_row("Select a.*, b.category, b.category_url from articles a 
left join article_categories b on a.category_id=b.id 
where a.id=$id");
if($oarticle==null){redirTo($_CONFIG["urlpath"]);}

$asoc_products = $db->get_results("Select b.* from article_x_products a 
left join shop_products b on a.shop_product_id=b.id 
where a.article_id=".$id);

$db->query("Update LOW_PRIORITY articles set shows=shows+1 where id=$id");

#breadcrumbs
addPagePathLink($_CONFIG["urlpath"]."articole/" . $oarticle->category_url . "/",$oarticle->category);
addPagePathText($oarticle->title);

#page constants
$page_heading = $oarticle->title;
$page_title = $oarticle->title . " - " . $_CONFIG["site_name"];
$page_metakey = "";
$page_metadesc = $oarticle->title . "; articol din categoria " . $oarticle->category;
$canonical_url = $_CONFIG["urlpath"]."articole/" . $oarticle->category_url . "/" . $oarticle->title_url . "-" . $oarticle->id . ".html";

$arListedProducts = array();

#process header + footer info
include("include/process-page-info.php");



//include template
include ("templates/".$_CONFIG["template"]."/header.php");
include ("templates/".$_CONFIG["template"]."/article.php");
include ("templates/".$_CONFIG["template"]."/footer.php");


//increase show count
if($arListedProducts!=null){
    $db->query("Update LOW_PRIORITY shop_products set show_inlisting=show_inlisting+1 where id in (".implode(",",$arListedProducts).")");        
}
?>