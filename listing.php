<?php
include("common/common.php"); 

//variables
$type = $_GET["type"];
$page = (int)$_GET["page"];
$start = ($page-1)*$_CONFIG["rec_per_page"];
$listing_description = "";

if($type==""){redirTo($_CONFIG["urlpath"]);}

//by price
if($type=="price"){
    $vmin = (int)$_GET["vmin"];
    $vmax = (int)$_GET["vmax"];
    if((int)$db->get_var("Select count(id) from shop_priceranges where vmin=$vmin and vmax=$vmax")==0){redirTo($_CONFIG["urlpath"]);}
        
    $sql_content = "Select * from shop_products where price_int>=$vmin and price_int<=$vmax and active=1 order by price_int asc limit $start, " . $_CONFIG["rec_per_page"];
    $sql_count = "Select count(*) from shop_products where price_int>=$vmin and price_int<=$vmax and active=1";
    
    $pagination_slug = "preturi-" . $_CONFIG["settings"]["slug_price"] . "/" . $vmin . "-" . $vmax ;
    
    $page_heading = "Produse cu pretul intre $vmin Lei si $vmax Lei";
    $extra_title = "pret $vmin Lei - $vmax Lei" . " - pagina " . $page;
    
    $page_metakey = "";
    $page_metadesc = "Produse cu pretul intre $vmin Lei si $vmax Lei";    
    
    $canonical_url = $_CONFIG["urlpath"] . $pagination_slug . "/";
}

//by category
if($type=="category"){
    $slug = mysql_real_escape_string($_GET["slug"]);
    $category = $db->get_row("Select SQL_CACHE * from shop_categories where category_url='$slug' limit 1");        
    if($category==null){redirTo($_CONFIG["urlpath"]);}
    
    $sql_content = "Select * from shop_products where category_id=".$category->id." and active=1 order by title asc limit $start, " . $_CONFIG["rec_per_page"];
    $sql_count = "Select count(*) from shop_products where category_id=".$category->id." and active=1";    
    
    $pagination_slug = $slug;
    
    $page_heading = $category->category;
    $extra_title = $category->category . " - pagina " . $page;
    
    $listing_description = $category->details;
    
    $page_metakey = $category->category;
    $page_metadesc = "Produse din categoria " . $category->category;    
    
    $canonical_url = $_CONFIG["urlpath"] . $pagination_slug . "/";
    
    $db->query("UPDATE LOW_PRIORITY shop_categories set vcount=vcount+1 where id=".$category->id);
}

//by brand
if($type=="brand"){
    $slug = mysql_real_escape_string($_GET["slug"]);
    $brand = $db->get_row("Select SQL_CACHE * from shop_brands where brand_url='$slug' limit 1");
    if($brand==null){redirTo($_CONFIG["urlpath"]);}
    
    $sql_content = "Select * from shop_products where brand_id=".$brand->id." and active=1 order by title asc limit $start, " . $_CONFIG["rec_per_page"];
    $sql_count = "Select count(*) from shop_products where brand_id=".$brand->id." and active=1";    

    $pagination_slug = "brand/" . $slug;
    
    $page_heading = "Brand: " . $brand->brand;
    $extra_title = $brand->brand . " - pagina " . $page;
    
    $listing_description = $brand->details;
    
    $page_metakey = $brand->brand;
    $page_metadesc = "produse de la " . $brand->brand;    
    
    $canonical_url = $_CONFIG["urlpath"] . $pagination_slug . "/";
    
    $db->query("UPDATE LOW_PRIORITY shop_brands set shows=shows+1 where id=".$brand->id);    
}

#page content
$product_count = (int)$db->get_var($sql_count);
$page_count = ceil($product_count/$_CONFIG["rec_per_page"]);
$records = $db->get_results($sql_content);

#breadcrumb
addPagePathText('Listare produse');


#page constants
#$page_heading = "Listare";
$page_title = $_CONFIG["site_name"] . " - " . $extra_title;

$arListedProducts = array();

#process header + footer info
include("include/process-page-info.php");

//pagination
    //prev link
    if($product_count>0){
        $prev_val = $page-1; if($prev_val<1){$prev_val=1;}
        addPaginationElement('link', '&laquo;', $_CONFIG["urlpath"].$pagination_slug.'/'.$prev_val.".html",'Pagina anterioara');
    }    

    //inner links
    $spacer_show = false;
    for ($i=1; $i<=$page_count; $i++){
        $sel_class = "";        
        if($i<=3 or $i>=$page_count-2 or ($i>=$page-1 and $i<=$page+1)){
            if($i==$page){$sel_class = 'selected';}
            addPaginationElement('link', $i, $_CONFIG["urlpath"].$pagination_slug.'/'.$i.".html",'Pagina '.$i,$sel_class);
            $spacer_show = false;
        } else {
            if($spacer_show==false){
                $spacer_show = true;
                addPaginationElement('text', '...');
            }
        }
    }

    //next link
    if($product_count>0){        
        $next_val = $page + 1;
        if($next_val>$page_count){$next_val=$page_count;}
        if($next_val==0){$next_val=1;}    
        addPaginationElement('link', '&raquo;', $_CONFIG["urlpath"].$pagination_slug.'/'.$next_val.".html",'Pagina urmatoare');
    }



//include template
include ("templates/".$_CONFIG["template"]."/header.php");
include ("templates/".$_CONFIG["template"]."/listing-pagination.php");
include ("templates/".$_CONFIG["template"]."/listing.php");
include ("templates/".$_CONFIG["template"]."/listing-pagination.php");
include ("templates/".$_CONFIG["template"]."/footer.php");



//increase show count
if($arListedProducts!=null){
    $db->query("Update LOW_PRIORITY shop_products set show_inlisting=show_inlisting+1 where id in (".implode(",",$arListedProducts).")");        
}
?>