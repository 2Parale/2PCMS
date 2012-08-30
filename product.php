<?php
include("common/common.php"); 


//building replace array
$arDescSearch = array();
$arDescReplace = array();
$rep_words = $db->get_results("Select SQL_CACHE * from text_transform");
if ($rep_words!=null){
    foreach($rep_words as $rep_word){
        $arDescSearch[] = $rep_word->source;
        $arDescReplace[] = $rep_word->replace;
    }    
}

//product id
$prod_id = (int)$_GET["id"];
if($prod_id==0){redirTo($_CONFIG["urlpath"]);}
$db->query("Update LOW_PRIORITY shop_products set show_inpage=show_inpage+1 where id=$prod_id");


//product data
$product = $db->get_row("Select a.*, b.category, b.category_url from shop_products a 
left join shop_categories b on a.category_id=b.id 
where a.id=$prod_id");
if($product==null){redirTo($_CONFIG["urlpath"]);}

if($_CONFIG["settings"]["mask_redirect"]=="da"){
    $product->aff_url = $_CONFIG["urlpath"] . "cumpara/" . $product->title_url . "-" . $product->id . ".html";
}

#breadcrumb
addPagePathLink($_CONFIG["urlpath"] . $product->category_url . "/", $product->category);
addPagePathText($product->title);


#page constants
$page_heading = $product->title;
$page_title = $product->title . " - " . $_CONFIG["site_name"];
$page_metakey = $product->title . ", " . $product->category;
$page_metadesc = $product->title . " la pretul de " . $product->price . " din categoria " . $product->category;
if($product->meta_desc!=""){$page_metadesc = $product->meta_desc;}
$canonical_url = $_CONFIG["urlpath"] . $product->title_url . "-" . $product->id . ".html";

$arListedProducts = array();


#process header + footer info
include("include/process-page-info.php");


//PAGE DATA
//product data is already in $product object
$related_prods = $db->get_results("Select * from shop_products where category_id=".$product->category_id." and active=1 and id<>$prod_id order by RAND() limit 8");

//related article data
if($_CONFIG["settings"]["article_product_page_box"]=="da"){
    $related_articles = $db->get_results("Select b.title, b.acontent, b.id from article_x_products a 
    left join articles b on a.article_id=b.id 
    where a.shop_product_id=$prod_id");
}

//partner data
$arPartnerData = array();
$arPartnerData["show"] = false;
if($_CONFIG["settings"]["partner_show"]=="da"){
    $partner = $db->get_row("Select * from aff_partners where id=".$product->partner_id);
    if($partner->shop_content!=""){
        $arPartnerData["show"] = true;
        $arPartnerData["content"] = $partner->shop_content;
        $arPartnerData["logo"] = $partner->shop_logo;
        $arPartnerData["name"] = $partner->shop;
        $arPartnerData["url"] = $partner->shop_url;
    }
}


//include template
include ("templates/".$_CONFIG["template"]."/header.php");
include ("templates/".$_CONFIG["template"]."/product.php");
include ("templates/".$_CONFIG["template"]."/footer.php");


//increase show count
if($arListedProducts!=null){
    $db->query("Update LOW_PRIORITY shop_products set show_inlisting=show_inlisting+1 where id in (".implode(",",$arListedProducts).")");        
}
?>
