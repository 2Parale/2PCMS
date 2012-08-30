<?php
include("common/common.php");
set_time_limit(3600);
error_reporting(1);
header ("content-type: text/xml");


$filemtime = @filemtime("sitemaps/sitemap_index.xml");
if ($filemtime and (time() - $filemtime < 432000)){
    readfile("sitemaps/sitemap_index.xml");
    die();
}else{
    generate_static_sitemap();
    generate_classification_sitemap();
    $prod_sitemaps = generate_product_sitemaps();
    
    $sitemap_index = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $sitemap_index .= '<sitemap>';
    $sitemap_index .= '<loc>'.$_CONFIG["urlpath"].'sitemaps/sitemap_static.xml</loc>';
    $sitemap_index .= '<lastmod>'.date("c").'</lastmod>';
    $sitemap_index .= '</sitemap>';
    $sitemap_index .= '<sitemap>';
    $sitemap_index .= '<loc>'.$_CONFIG["urlpath"].'sitemaps/sitemap_classification.xml</loc>';
    $sitemap_index .= '<lastmod>'.date("c").'</lastmod>';        
    $sitemap_index .= '</sitemap>';
    for($i=1; $i<=$prod_sitemaps; $i++){
        $sitemap_index .= '<sitemap>';
        $sitemap_index .= '<loc>'.$_CONFIG["urlpath"].'sitemaps/sitemap_products_'.$i.'.xml</loc>';
        $sitemap_index .= '<lastmod>'.date("c").'</lastmod>';        
        $sitemap_index .= '</sitemap>';        
    }
    $sitemap_index .= '</sitemapindex>';
    
    file_put_contents("sitemaps/sitemap_index.xml",$sitemap_index);
    echo $sitemap_index;
    die();
}



function generate_static_sitemap(){
    global $db;
    global $_CONFIG;
    
    $sitemap = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
    $sitemap .= '<url>';
    $sitemap .= '<loc>'.$_CONFIG["urlpath"].'</loc>';
    $sitemap .= '</url>';
    //SEARCHES
    $ss = $db->get_results("Select sterm from shop_searches where active=1 limit 500");
    if($ss!=null){
        foreach($ss as $s){
            $sitemap .= '<url>';
            $sitemap .= '<loc>'.$_CONFIG["urlpath"].'cauta/'.urlencode($s->sterm).'/</loc>';
            $sitemap .= '</url>';            
        }
    }
    //ARTICLE CATEGORIES
    $acs = $db->get_results("Select category_url from article_categories");
    if($acs!=null){
        foreach($acs as $ac){
            $sitemap .= '<url>';
            $sitemap .= '<loc>'.$_CONFIG["urlpath"].'articole/'.$ac->category_url.'/</loc>';
            $sitemap .= '</url>';                        
        }
    }
    //ARTICLES
    $articles = $db->get_results("Select a.id, a.title_url, b.category_url from articles a left join article_categories b on a.category_id=b.id");
    if($articles!=null){
        foreach($articles as $art){
            $sitemap .= '<url>';
            $sitemap .= '<loc>'.$_CONFIG["urlpath"].'articole/'.$art->category_url.'/'.$art->title_url.'-'.$art->id.'.html</loc>';
            $sitemap .= '</url>';            
        }
    }
    //LANDING PAGES
    $lps = $db->get_results("Select lp_url, id from landing_pages");
    if($lps!=null){
        foreach($lps as $lp){
            $sitemap .= '<url>';
            $sitemap .= '<loc>'.$_CONFIG["urlpath"].'lp/'.$lp->lp_url.'-'.$lp->id.'.html</loc>';
            $sitemap .= '</url>';            
            
        }
    }
        
    $sitemap .= '</urlset>';
    
    file_put_contents("sitemaps/sitemap_static.xml",$sitemap);
}

function generate_classification_sitemap(){
    global $db;
    global $_CONFIG;    
    
    $sitemap = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
    //CATEGORIES
    $cats = $db->get_results("Select category_url from shop_categories");
    if($cats!=null){
        foreach($cats as $cat){
            $sitemap .= '<url>';
            $sitemap .= '<loc>'.$_CONFIG["urlpath"].$cat->category_url.'/</loc>';
            $sitemap .= '</url>';        
        }
    }
    //BRANDS
    $brands = $db->get_results("Select brand_url from shop_brands where prod_count>0");
    if($brands!=null){
        foreach($brands as $brand){
            $sitemap .= '<url>';
            $sitemap .= '<loc>'.$_CONFIG["urlpath"].'brand/'.$brand->brand_url.'/</loc>';
            $sitemap .= '</url>';        
        }
    }        
    $sitemap .= '</urlset>';
    
    file_put_contents("sitemaps/sitemap_classification.xml",$sitemap);    
}

function generate_product_sitemaps(){
    global $db;    
    global $_CONFIG;
    global $logg;
    
    $sitemap = "";
    $pcount = 0;
    $scount = 0;
    $limit_start = 0;
    
    $products = $db->get_results("Select a.title_url, a.id, a.local_img_big, a.price, a.title from shop_products a 
    where a.active=1 limit $limit_start, 1000");
    while($products!=null){
        foreach($products as $product){
            $pcount++;
            $sitemap .= '<url>';
            $sitemap .= '<loc>'.$_CONFIG["urlpath"].$product->title_url.'-'.$product->id.'.html</loc>';
            $sitemap .= '<image:image>';
            $sitemap .= '<image:loc>'.$_CONFIG["urlpath"].'product_images/'.$product->local_img_big.'</image:loc>';
            #$sitemap .= '<image:title>'.htmlentities($product->title, ENT_XHTML).' la pretul de '.$product->price.'</image:title>';
            $sitemap .= '</image:image>';
            $sitemap .= '</url>';
            
            if($pcount==10000){
                $pcount = 0;
                $scount++;
                $sitemap = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'.$sitemap.'</urlset>';
                file_put_contents("sitemaps/sitemap_products_".$scount.".xml",$sitemap);    
                $sitemap = "";
            }
        }
        
        $limit_start = $limit_start + 1000;
        
        $products = $db->get_results("Select a.title_url, a.id, a.local_img_big, a.price, a.title from shop_products a 
        where a.active=1 limit $limit_start, 1000");
    }    
    
    if($sitemap!=""){
        $scount++;
        $sitemap = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'.$sitemap.'</urlset>';
        file_put_contents("sitemaps/sitemap_products_".$scount.".xml",$sitemap);            
    }    
    
    return $scount;
}

?>