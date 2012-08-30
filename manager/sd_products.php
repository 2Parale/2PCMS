<?php
include "../common/common.php";
set_time_limit(3600);

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="process_import"){
        $pid = $_GET["pid"];
        
        $db->query("OPTIMIZE TABLE aff_categories, aff_feed_source, shop_products");

        $db->query("Update shop_products set marked=1 where partner_id=$pid");//se marcheaza toate ca fiind de sters

        //actual insert / update
        $db->query("
        INSERT INTO shop_products 
            (partner_id, original_id, original_id_int, category_id, title, title_url, brand_id, description, price, price_int, old_price, old_price_int, aff_url, img_url, create_date, active, marked)
        SELECT 
                a.partner_id, a.product_id, a.product_id_int, ac.shop_category_id,  a.title, a.title_url, a.brand_id, a.description, a.price, a.price_int, a.price, a.price_int, 
                a.aff_url, a.img_urls, NOW(), 1, 0 
            from aff_feed_source a 
            left join aff_categories ac on a.aff_category_id=ac.id
            where 
            a.partner_id=$pid 
            and a.product_active=1 
            and ac.shop_category_id>0
            
        ON DUPLICATE KEY UPDATE 
            old_price=shop_products.price, old_price_int=shop_products.price_int, price=a.price, price_int=a.price_int, active=a.product_active, marked=0
            
        ");

        $db->query("Update shop_products set active=0, marked=0 where partner_id=$pid and marked=1");//inactivam produsele care au ramas de sters

        $db->query("OPTIMIZE TABLE aff_categories, aff_feed_source, shop_products");

        //recalculare count produse pe categorii
        $db->query("update shop_categories a set a.pa_count=(Select count(id) from shop_products b where b.category_id=a.id and active=1), pi_count=(Select count(id) from shop_products b where b.category_id=a.id and active=0)");

        //recalculare count produse active pe branduri
        $db->query("Update shop_brands a set a.prod_count=(select count(*) from shop_products b where b.brand_id=a.id and active=1)");

        $db->query("OPTIMIZE TABLE shop_categories, shop_brands, shop_products");
                
        $_SESSION["response_msg"] = "Importul a fost procesat!";
        
        redirTo("sd_products.php?pid=$pid");
    }
    
}


$pid = 0; // partner id
if(isset($_GET["pid"])){$pid = (int)$_GET["pid"];}


$page_name = "sd_products";
$page_title = "Produse din feed-uri";
include "include/header.php";
?>

<form name="filter_feeds" id="filter_feeds" method="get" action="sd_products.php">  
<span style="float: left; padding: 5px;">Selecteaza partenerul:</span>
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

<i>la alegerea unui partener se va rula o analiza a feed-ului pentru a face o estimare a produselor ce pot fi importate</i>

</form>


<?php
if($pid>0){
    $y = 0;
    $pif = (int)$db->get_var("Select count(*) from aff_feed_source where partner_id=$pid");
    $pif_wc = (int)$db->get_var("Select count(*) from aff_feed_source a left join aff_categories b on a.aff_category_id=b.id where a.partner_id=$pid and b.shop_category_id>0 and a.product_active=1");
    $pis = (int)$db->get_var("Select count(*) from shop_products where partner_id=$pid");
    ?>
    
    <div>
        <div style="width: 380px; float: left; margin-right: 20px;">
            <h3>SUMAR</h3>
            <span style="float: left; width: 300px; padding: 5px; margin-right: 10px;">Produse in feed - total:</span>
            <span style="float: left; width: 50px; padding: 5px; background-color: #f2f2f2; font-weight: bold; text-align: center;"><?php echo $pif; ?></span>
            <div class="clear"></div>
            <div class="spacer10"></div>
            
            <span style="float: left; width: 300px; padding: 5px; margin-right: 10px;">Produse in feed active cu categorie asociata:</span>
            <span style="float: left; width: 50px; padding: 5px; background-color: #f2f2f2; font-weight: bold; text-align: center;"><?php echo $pif_wc; ?></span>
            <div class="clear"></div>
            <div class="spacer10"></div>
            
            <span style="float: left; width: 300px; padding: 5px; margin-right: 10px;">Produse in catalog site:</span>
            <span style="float: left; width: 50px; padding: 5px; background-color: #f2f2f2; font-weight: bold; text-align: center;"><?php echo $pis; ?></span>
            <div class="clear"></div>
            <div class="spacer10"></div> 
            
            <div class="spacer10"></div>
            <a href="javascript:void(0)" onclick="window.open('sd_products.php?action=process_import&pid=<?php echo $pid; ?>','_self')" class="button"><span>PROCESEAZA IMPORTUL</span></a>
            <div class="clear"></div>                                   
            
        </div>
        
        <div class="clear"></div>
    </div>

    <?php
}
?>




<?php
include "include/footer.php";
?>
