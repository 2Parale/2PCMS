<?php
/**
* 
* CRON DAILY JOB
* 
* INCLUDES:
*   - downloading feeds for the selected partners
*   - importing products for selected partners
*   - downloading product images
*   - checking link exchange partners
* 
* OUTPUT:
*   - stats for every action
*   - outputs to console and to cron_logs table
* 
*/

include "../common/common.php";
require_once('../manager/include/phpthumb/phpthumb.class.php');
error_reporting(1);  
set_time_limit(7200);

$t1 = time();


function setlog($msg, $include_in_db=0){
    global $db;

    if($include_in_db==1){$db->query("Insert into cron_logs (date_log, action_details) values (NOW(), '$msg')");}
    
    echo date("r") . " :: " . $msg . "\n\r";
    flush();    
}


function get_url_contents($url){    
    $opts = array( 
        'http' => array( 
            'method'=>"GET", 
            'header'=>"Content-Type: text/html; charset=utf-8" 
        ) 
    ); 

    $context = stream_context_create($opts); 
    $result = @file_get_contents($url,false,$context); 
    return $result;     
    
    /* CURL VERSION
    $crl = curl_init();
    $timeout = 5;
    curl_setopt ($crl, CURLOPT_URL,$url);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $ret = curl_exec($crl);
    curl_close($crl);
    return $ret;
    */
}


//LOG->
setlog("START - cron job", 1);


//FEEDS
$partners = $db->get_results("Select * from aff_partners where cron_sync=1");
if($partners!=null){
    foreach($partners as $part){
        setlog("START - PARTNER - partner id ".$part->id." / name |".$part->shop."|");
        
        $feeds = $db->get_results("Select * from aff_feeds where partner_id=".$part->id);
        if($feeds!=null){
            foreach($feeds as $feed){
                
                setlog("START - PARTNER - FEED - partner id ".$part->id." / name |".$part->shop."| - feed id " . $feed->id);
                
                //Downloading 
                file_put_contents('../feeds/feed-'.$feed->partner_id."-".$feed->id.".xml", file_get_contents($feed->feed_url));        
                $db->query("Update aff_feeds set feed_filename='feed-".$feed->partner_id."-".$feed->id.".xml', last_date=NOW() where id=".$feed->id);                
                
                setlog("PARTNER - FEED - partner id ".$part->id." / name |".$part->shop."| - feed id " . $feed->id . " :: feed downloaded", 1);
                //Downloading - end
                
                
                
                //Importing
                //clearing the feed source table
                $db->query("Delete from aff_feed_source where partner_id=".$feed->partner_id." and feed_id=".$feed->id);
                
                $objFeed = simplexml_load_file("../feeds/".$feed->feed_filename);
                foreach($objFeed->item as $objItem){
                    //insert category data
                    $catCheck = (int)$db->get_var("Select count(*) from aff_categories where partner_id=".$feed->partner_id." and category='".$objItem->category."' and subcategory='".$objItem->subcategory."'");
                    if($catCheck==0){
                        $db->query("Insert into aff_categories 
                        (partner_id, category, subcategory, shop_category_id) 
                        values 
                        ($pid, '".$objItem->category."', '".$objItem->subcategory."', 0)");
                        $catId = $db->lastId;
                    } else {
                        $catId = (int)$db->get_var("Select id from aff_categories where partner_id=".$feed->partner_id." and category='".$objItem->category."' and subcategory='".$objItem->subcategory."'");
                    }
                    
                    //insert brand data
                    $brandCheck = (int)$db->get_var("Select count(*) from shop_brands where brand='".mysql_real_escape_string($objItem->brand)."'");
                    if($brandCheck==0){
                        $db->query("Insert into shop_brands (brand, brand_url, brand_file, shows) values ('".mysql_real_escape_string($objItem->brand)."', '".text_to_url($objItem->brand)."', '', 0)");
                        $brandId = $db->lastId;
                    }else{
                        $brandId = (int)$db->get_var("Select id from shop_brands where brand='".mysql_real_escape_string($objItem->brand)."'");
                    }
                    
                    //insert feed data
                    $arImages = explode(",",$objItem->image_urls);
                    $active=1; if($objItem->product_active=='false'){$active=0;}
                    $price_int = $objItem->price;
                    if($feed->price_format=='pricedot'){
                        $price_int = floatval(str_replace(",","",$price_int));
                    }elseif($feed->price_format=='pricecomma'){
                        $price_int = floatval(str_replace(",",".",str_replace(".","",$price_int)));
                    }                    
                    $db->query("Insert into aff_feed_source 
                    (partner_id, feed_id, import_date, campaign_name, widget_name, title, title_url, description, short_message, price, price_int, category, subcategory, url, img_urls, other_data, aff_url, create_date, product_active, brand, product_id, aff_category_id, brand_id, product_id_int) 
                    values 
                    (".$feed->partner_id.", ".$feed->id.", NOW(), '".mysql_real_escape_string($objItem->campaign_name)."', '".mysql_real_escape_string($objItem->widget_name)."', '".mysql_real_escape_string($objItem->title)."', '".text_to_url(mysql_real_escape_string($objItem->title))."', '".mysql_real_escape_string($objItem->description)."', '".mysql_real_escape_string($objItem->short_message)."', '".$objItem->price."', '".$price_int."', '".mysql_real_escape_string($objItem->category)."', '".mysql_real_escape_string($objItem->subcategory)."', '".$objItem->url."', '".$arImages[0]."', '".mysql_real_escape_string($objItem->other_data)."', '".$objItem->aff_code."', '".$objItem->created_at."', '".$active."', '".mysql_real_escape_string($objItem->brand)."', '".trim($objItem->product_id)."', $catId, $brandId, ".crc32($objItem->product_id).") ON DUPLICATE KEY UPDATE partner_id=".$feed->partner_id);                                    
                    
                }
                
                setlog("PARTNER - FEED - partner id ".$part->id." / name |".$part->shop."| - feed id " . $feed->id . " :: feed imported", 1);
                //Importing - end
                
                
                
                setlog("END - PARTNER - partner id ".$part->id." / name |".$part->shop."| - FEED - feed id " . $feed->id);
            }
        }
        
        //Importing products
                
        //Import products
        $pid = $part->id;

        $db->query("OPTIMIZE TABLE aff_categories, aff_feed_source, shop_products");

        $db->query("Update shop_products set marked=1 where partner_id=$pid");//se marcheaza toate ca fiind de sters

        //actual insert / update
        $db->query("
        INSERT INTO shop_products 
            (partner_id, original_id, original_id_int, category_id, title, title_url, brand_id, description, price, price_int, aff_url, img_url, create_date, active, marked)
        SELECT 
                a.partner_id, a.product_id, a.product_id_int, ac.shop_category_id,  a.title, a.title_url, a.brand_id, a.description, a.price, a.price_int, 
                a.aff_url, a.img_urls, NOW(), 1, 0 
            from aff_feed_source a 
            left join aff_categories ac on a.aff_category_id=ac.id
            where 
            a.partner_id=$pid 
            and a.product_active=1 
            and ac.shop_category_id>0
            
        ON DUPLICATE KEY UPDATE 
            price=a.price, price_int=a.price_int, active=a.product_active, marked=0
            
        ");

        $db->query("Update shop_products set active=0, marked=0 where partner_id=$pid and marked=1");//inactivam produsele care au ramas de sters

        $db->query("OPTIMIZE TABLE aff_categories, aff_feed_source, shop_products");

        //recalculare count produse pe categorii
        $db->query("update shop_categories a set a.pa_count=(Select count(id) from shop_products b where b.category_id=a.id and active=1), pi_count=(Select count(id) from shop_products b where b.category_id=a.id and active=0)");
        setlog("Calculated product count on categories", 1);

        //recalculare count produse active pe branduri
        $db->query("Update shop_brands a set a.prod_count=(select count(*) from shop_products b where b.brand_id=a.id and active=1)");
        setlog("Calculated active product count on brands", 1);
        
        $db->query("OPTIMIZE TABLE shop_categories, shop_brands, shop_products");        
                
        setlog("PARTNER - partner id ".$part->id." / name |".$part->shop."| :: products imported", 1);
        //Import products - end
        
        //Get product images
        $prods = $db->get_results("Select * from shop_products where local_img_small='' and partner_id=$pid order by id asc limit 1000");
        
        $s_w = $_CONFIG["settings"]["img_small_width"]; 
        $s_h = $_CONFIG["settings"]["img_small_height"];
        $b_w = $_CONFIG["settings"]["img_big_width"]; 
        $b_h = $_CONFIG["settings"]["img_big_height"];

        while($prods!=null){
        
            foreach($prods as $prod){
                $arImage = explode(".",$prod->img_url);
                $fname = "imagine_" . trim($prod->title_url,"_") . "_" . $prod->id . "." . $arImage[count($arImage)-1];                
                file_put_contents("../product_images/_temp/" . $fname, file_get_contents($prod->img_url));

                $destination_folder = $prod->partner_id . "/" . $prod->category_id . "/";
                if (!file_exists("../product_images/".$prod->partner_id)) {mkdir("../product_images/".$prod->partner_id);chmod("../product_images/".$prod->partner_id, 0644);}
                if (!file_exists("../product_images/".$prod->partner_id."/".$prod->category_id)) {mkdir("../product_images/".$prod->partner_id."/".$prod->category_id); chmod("../product_images/".$prod->partner_id."/".$prod->category_id, 0644);}
                $format = "jpg";

                //thumb big
                $uploadedfile = "../product_images/_temp/".$fname;
                $uploadedfileThumb = "../product_images/".$destination_folder.$fname;
                $size=getimagesize( $uploadedfile );                            
                $phpThumb = new phpThumb();                        
                $phpThumb->config_output_format = $format;
                $phpThumb->src = $uploadedfile;
                $phpThumb->w = $b_w;
                $phpThumb->h = $b_h;
                if ($size[0]>$size[1]){
                    $phpThumb->setParameter("far", "L");
                } else {
                    $phpThumb->setParameter("far", "C");
                }                        
                $phpThumb->setParameter("bg", "ffffff");            
                $phpThumb->GenerateThumbnail();
                $phpThumb->RenderToFile( $uploadedfileThumb );        

                //thumb small
                $uploadedfile = "../product_images/_temp/".$fname;
                $uploadedfileThumb = "../product_images/".$destination_folder."small_".$fname;
                $size=getimagesize( $uploadedfile );                            
                $phpThumb = new phpThumb();                        
                $phpThumb->config_output_format = $format;
                $phpThumb->src = $uploadedfile;
                $phpThumb->w = $s_w;
                $phpThumb->h = $s_h;
                if ($size[0]>$size[1]){
                    $phpThumb->setParameter("far", "L");
                } else {
                    $phpThumb->setParameter("far", "C");
                }                        
                $phpThumb->setParameter("bg", "ffffff");            
                $phpThumb->GenerateThumbnail();
                $phpThumb->RenderToFile( $uploadedfileThumb );         


                //stergem imaginea temporara
                @unlink("../product_images/_temp/" . $fname);

                $db->query("Update shop_products set local_img_small='".$destination_folder."small_".$fname."', local_img_big='".$destination_folder.$fname."' where id=".$prod->id);

            }//end foreach
            
            //Get product images
            $prods = $db->get_results("Select * from shop_products where local_img_small='' and partner_id=$pid order by id asc limit 1000");            
            
        }//end while $prods!=null        
        
        setlog("PARTNER - partner id ".$part->id." / name |".$part->shop."| :: product images downloaded", 1);
        //Get product images - end
        
        setlog("END - PARTNER - partner id ".$part->id." / name |".$part->shop."|");
    }
}


//LINK EXCHANGE
$les = $db->get_results("Select * from le_partners order by id asc");
if($les!=null){
    foreach($les as $le){

        $webc = get_url_contents($le->p_checkpage);
        $id = $le->id;
        
        if($webc!=""){
            $strfound = strpos($webc, $le->my_link);
            
            if($strfound === false){
                //nu am gasit linkul
                $db->query("Update le_partners set last_date=NOW(), last_status=0 where id=$id");
                $db->query("INSERT into le_logs (le_id, date_log, status_log) values ($id, NOW(), 0)");
            }else{
                //am gasit linkul
                $db->query("Update le_partners set last_date=NOW(), last_status=1 where id=$id");
                $db->query("INSERT into le_logs (le_id, date_log, status_log) values ($id, NOW(), 1)");            
            }
        }
        
    }//end foreach
}
setlog("Links checked", 1);




$t2 = time();
$td = $t2 - $t1;

//LOG->
setlog("END - cron job ($td seconds)", 1);
?>
