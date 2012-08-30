<?php
include("common/common.php"); 


//LP id
$lp_id = (int)$_GET["id"];
if($lp_id==0){redirTo($_CONFIG["urlpath"]);}
$db->query("Update LOW_PRIORITY landing_pages set shows=shows+1 where id=$lp_id");


//product data
$product = $db->get_row("Select a.aff_url, a.price, a.price_int, a.title_url, b.category, b.category_url, a.category_id, a.local_img_big, a.active, lp.* from landing_pages lp 
left join shop_products a on lp.product_id=a.id 
left join shop_categories b on a.category_id=b.id 
where lp.id=$lp_id");

if($_CONFIG["settings"]["mask_redirect"]=="da"){
    $product->aff_url = $_CONFIG["urlpath"] . "cumpara/" . $product->title_url . "-" . $product->product_id . ".html";
}

#breadcrumb
addPagePathLink($_CONFIG["urlpath"] . $product->category_url . "/", $product->category);
addPagePathText($product->new_title);


#page constants
$page_heading = $product->new_title;
$page_title = $product->new_title . " - " . $_CONFIG["site_name"];
$page_metakey = $product->meta_keys;
$page_metadesc = $product->meta_desc;
$canonical_url = $_CONFIG["urlpath"] . 'lp/' . $product->lp_url . "-" . $product->id . ".html";


$arListedProducts = array();


 #process header + footer info
include("include/process-page-info.php");


//page data
//product data is already in $product object
$related_prods = $db->get_results("Select * from shop_products where category_id=".$product->category_id." and active=1 order by RAND() limit " . $product->extra_products_count);

//include template
include ("templates/".$_CONFIG["template"]."/header.php");
include ("templates/".$_CONFIG["template"]."/landing-page.php");
include ("templates/".$_CONFIG["template"]."/footer.php");


//increase show count
if($arListedProducts!=null){
    $db->query("Update LOW_PRIORITY shop_products set show_inlisting=show_inlisting+1 where id in (".implode(",",$arListedProducts).")");        
}
?>
