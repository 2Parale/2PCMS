<?php
include "../common/common.php";
//error_reporting(7);
set_time_limit(3600);


if(!userIsOk()){redirTo("login.php");}//checking user


//Handle requests
if(isset($_GET["action"])){
    
    //download feed
    if($_GET["action"]=="download"){
        $pid = $_GET["pid"];
        $feed_id = $_GET["feed_id"];
        
        $feed = $db->get_row("Select * from aff_feeds where id=$feed_id");
        
        file_put_contents('../feeds/feed-'.$feed->partner_id."-".$feed->id.".xml", file_get_contents($feed->feed_url));        
        $db->query("Update aff_feeds set feed_filename='feed-".$feed->partner_id."-".$feed->id.".xml', last_date=NOW() where id=$feed_id");
        
        $_SESSION["response_msg"] = "Feed-ul a fost downloadat!";
        
        redirTo("id_import.php?pid=$pid");
    }
    
    //import feed
    if($_GET["action"]=="import"){
        $pid = $_GET["pid"];
        $feed_id = $_GET["feed_id"];
        
        //clearing the feed source table
        $db->query("Delete from aff_feed_source where partner_id=$pid and feed_id=$feed_id");
        
        $feed = $db->get_row("Select * from aff_feeds where id=$feed_id");
        $objFeed = simplexml_load_file("../feeds/".$feed->feed_filename);
        foreach($objFeed->item as $objItem){
            //insert category data
            $catCheck = (int)$db->get_var("Select count(*) from aff_categories where partner_id=$pid and category='".$objItem->category."' and subcategory='".$objItem->subcategory."'");
            if($catCheck==0){
                $db->query("Insert into aff_categories 
                (partner_id, category, subcategory, shop_category_id) 
                values 
                ($pid, '".$objItem->category."', '".$objItem->subcategory."', 0)");
                $catId = $db->lastId;
            } else {
                $catId = (int)$db->get_var("Select id from aff_categories where partner_id=$pid and category='".$objItem->category."' and subcategory='".$objItem->subcategory."'");
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
            ($pid, $feed_id, NOW(), '".mysql_real_escape_string($objItem->campaign_name)."', '".mysql_real_escape_string($objItem->widget_name)."', '".mysql_real_escape_string($objItem->title)."', '".text_to_url(mysql_real_escape_string($objItem->title))."', '".mysql_real_escape_string($objItem->description)."', '".mysql_real_escape_string($objItem->short_message)."', '".$objItem->price."', '".$price_int."', '".mysql_real_escape_string($objItem->category)."', '".mysql_real_escape_string($objItem->subcategory)."', '".$objItem->url."', '".$arImages[0]."', '".mysql_real_escape_string($objItem->other_data)."', '".$objItem->aff_code."', '".$objItem->created_at."', '".$active."', '".mysql_real_escape_string($objItem->brand)."', '".trim($objItem->product_id)."', $catId, $brandId, ".crc32($objItem->product_id).") ON DUPLICATE KEY UPDATE partner_id=$pid");                                    
            
        }
        
        $_SESSION["response_msg"] = "Feed-ul a fost importat!";
                
        redirTo("id_import.php?pid=$pid");
    }    
}



$pid = 0; // partner id
if(isset($_GET["pid"])){$pid = (int)$_GET["pid"];}

$page_name = "id_import";
$page_title = "Import feed-uri";
include "include/header.php";
?>

<form name="filter_feeds" id="filter_feeds" method="get" action="id_import.php">  
<span style="float: left; padding: 5px;">Selecteaza feed-urile pentru import dupa partener:</span>
<select name="pid" style="float: left;">
    <option value="0">--alege un partener</option>
    <?php
    $partners = $db->get_results("Select * from aff_partners order by shop asc");
    if($partners!=null){
        foreach($partners as $partner){
            ?>
            <option <?php if($pid==$partner->id){echo 'selected="selected"';} ?> value="<?php echo $partner->id; ?>"><?php echo $partner->shop; ?></option>
            <?php
        }
    }
    ?>
</select>
<a href="javascript:void(0)" onclick="$('#filter_feeds').submit();" class="button"><span>&raquo;</span></a>
<div class="clear"></div>
</form>


<?php
if($pid>0){
    $partner_name = $db->get_var("Select shop from aff_partners where id=$pid");
    ?>
    <hr>
    
    <h2>Feed-uri disponibile pentru partenerul <b><?=$partner_name?></b>:</h2>    
    <div>
        <b>Urmatorul pas</b>: 
         <a href="sd_categories.php?pid=<?php echo $pid; ?>">asociere categorii <?=$partner_name?></a>
         &nbsp;|&nbsp;
         <a href="sd_products.php?pid=<?php echo $pid; ?>">import produse <?=$partner_name?></a>
    </div>
    <div class="spacer10"></div>
    
    <?php
    $feeds = $db->get_results("Select *, DATE_FORMAT(last_date, '%d %b %Y - %H:%i') as last_date_nice from aff_feeds where partner_id=$pid");
    if($feeds==null){
        ?>
        <i>Nu exista feed-uri definite pentru acest partener</i>
        <?php
    }else{
        foreach($feeds as $feed){
            $has_file = false;
            ?>
            <div class="feed-item" id="feed-item-id-<?php echo $feed->id ;?>">
                <div style="font-size: 14px; height: 20px; background-color: #f2f2f2; padding: 4px;"><b><?php echo $feed->feed_desc;?></b>: <a href="<?php echo $feed->feed_url ;?>" target="_blank"><?php echo $feed->feed_url ;?></a> <i></i></div>
                <div style="margin-left: 10px; padding-left: 5px; border-left: 2px solid #f2f2f2; margin-top: 10px;">
                    <b>Status download</b>: <?php
                if($feed->feed_filename==""){                    
                    echo "<i>feed-ul nu a fost downloadat niciodata</i>";
                }else{
                    if(!file_exists("../feeds/".$feed->feed_filename)){
                        echo "<i>feed-ul nu mai exista pe server</i>";
                    }else{
                        $has_file = true;
                        echo "feed-ul (<i>".$feed->feed_filename."</i>) a fost downloadat ultima oara la data <u>" . $feed->last_date_nice . "</u>";
                        
                    }
                }
                ?></div>
                
                <div class="spacer5"></div>
                
                <div style="margin-left: 10px; padding-left: 5px; border-left: 2px solid #f2f2f2; margin-top: 10px;">
                    <b>Status import</b>: 
                <?php
                if($has_file){
                    @$dateImported = $db->get_var("Select DATE_FORMAT(import_date, '%d %b %Y - %H:%i') as import_date_nice from aff_feed_source where partner_id=$pid and feed_id=".$feed->id);
                    if($dateImported==null){
                        echo "fisierul nu a fost importat niciodata";
                    }else{
                        echo "fisierul a fost ultima oara importat la data <u>" . $dateImported . "</u>";
                    }
                }
                ?>
                    </div>
                    
                <div class="spacer10"></div>
                    
                <div>
                    <a href="id_import.php?action=download&pid=<?php echo $pid; ?>&feed_id=<?php echo $feed->id; ?>" class="button"><span>Download Feed</span></a>
                    <?php if($has_file){ ?>
                    <a href="id_import.php?action=import&pid=<?php echo $pid; ?>&feed_id=<?php echo $feed->id; ?>" class="button"><span>Import Feed</span></a>
                    <?php } ?>
                    
                    <div class="clear"></div>                    
                </div>
            </div>
            <?php
        }
    }
    ?>
    
    <?php    
}
?>




<?php
include "include/footer.php";
?>
