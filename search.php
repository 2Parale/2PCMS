<?php
include("common/common.php");

$action = $_GET["action"];

//if it;s prepare we redirect to search url
if($action=="prepare"){    
    $keys = $_GET["keys"];            
    
    if($keys==""){redirTo($_CONFIG["urlpath"]);}
    
    //mark searched word
    $db->query("Insert into shop_searches (sterm, scount, vcount, active) values ('$keys', 1, 1, 0) ON DUPLICATE KEY UPDATE scount=scount+1");

    $keys = urlencode($keys);
    
    redirTo($_CONFIG["urlpath"]."cauta/" . $keys . "/");
}


//Starting actual page
$keys = mysql_real_escape_string($_GET["keys"]);
$page = (int)$_GET["page"];
$start = ($page-1)*$_CONFIG["rec_per_page"];

//+1 view count
$db->query("Insert DELAYED into shop_searches (sterm, scount, vcount, active) values ('$keys', 1, 1, 0) ON DUPLICATE KEY UPDATE vcount=vcount+1");

#content
$arKeys = explode(" ",$keys);
for($i=0; $i<=count($arKeys)-1; $i++){
    $arKeys[$i] = "+" . $arKeys[$i] . "*";
}
$keys_bool = trim(implode(" ",$arKeys)," ");

$product_count = (int)$db->get_var("SELECT count(*) 
FROM shop_products a WHERE MATCH
(title) AGAINST('$keys_bool' IN 
BOOLEAN MODE) and a.active=1");
$page_count = ceil($product_count/20);
$records = $db->get_results("SELECT MATCH(title) AGAINST('$keys') AS relevance, a.* 
FROM shop_products a WHERE MATCH
(title) AGAINST('$keys_bool' IN 
BOOLEAN MODE)  
and a.active=1 
ORDER BY Relevance DESC 
limit $start, ".$_CONFIG["rec_per_page"]);

//in cazul in care nu avem rezultate la cautare atunci facem recomandari de continut
if($records==null){
    $rec_categories = $db->get_results("Select * from shop_categories where pa_count>0 and parent_id=0 order by vcount desc limit 4");
    $rec_products = $db->get_results("Select SQL_CACHE * from shop_products where active=1 order by click*10+show_inpage*5+show_inlisting desc limit 8");
}

#pagination
$pagination_slug = "cauta/" .urlencode($keys);

#breadcrumb
addPagePathLink($_CONFIG["urlpath"]."cauta/".urlencode($keys)."/",'Cauta "' . $keys . '"');
addPagePathText('Pagina ' . $page);


#page constants
$page_heading = 'Cauta <i>' . $keys . '</i> - pagina ' . $page;
$page_title = $keys . " - " . $_CONFIG["site_name"] . " - pagina " . $page;
$page_metakey = $keys;
$page_metadesc = "cauta " . $keys;
$canonical_url = $_CONFIG["urlpath"] . $pagination_slug . "/";

$arListedProducts = array();

#process header + footer info
include("include/process-page-info.php");


//pagination content
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
include ("templates/".$_CONFIG["template"]."/search.php");
include ("templates/".$_CONFIG["template"]."/listing-pagination.php");
include ("templates/".$_CONFIG["template"]."/footer.php");


//increase show count
if($arListedProducts!=null){
    $db->query("Update LOW_PRIORITY shop_products set show_inlisting=show_inlisting+1 where id in (".implode(",",$arListedProducts).")");        
}
?>
