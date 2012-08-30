<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){

    if($_GET["action"]=="update"){
        
        redirTo("s_brands.php");
    }
        
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];        
        
        redirTo("s_brands.php");
    }
    
    if($_GET["action"]=="recount"){
        
        $db->query("Update shop_brands a set a.prod_count=(select count(*) from shop_products b where b.brand_id=a.id and active=1)");
        
        $_SESSION["response_msg"] = "Numarul de produse / brand a fost recalculat!";
        
        redirTo("s_brands.php");
    }    
    
}


$page_name = "s_brands";
$page_title = "Branduri magazin";
include "include/header.php";
?>
<div style="background-color: #f2f2f2; margin-bottom: 20px; padding: 5px;">
Branduri cu produse <b>active</b> asociate - <? echo (int)$db->get_var("Select count(*) from shop_brands where prod_count>0"); ?> | 
Branduri fara produse <b>active</b> asociate - <? echo (int)$db->get_var("Select count(*) from shop_brands where prod_count=0"); ?> | 
<a href="s_brands.php?action=recount">Recalculeaza count de produse pe brand</a>
</div>



<div style="font-size: 16px; font-weight: bold;">
    <span style="float: left; width: 30px; height: 30px; text-align: center; margin-right: 3px;"><a href="javascript:void(0)" onclick="getBrands('0', 'brand-list')" style="display: block;">#</a></span>
<?
for($i=65; $i<=90; $i++){
    ?>
    <span style="float: left; width: 30px; height: 30px; text-align: center; margin-right: 3px;"><a href="javascript:void(0)" onclick="getBrands('<?=chr($i)?>', 'brand-list')" style="display: block;"><?=chr($i)?></a></span>
    <?
}
?>
    <div class="clear"></div>
</div>

<div class="spacer10"></div>

<div id="brand-list"></div>

<?php
include "include/footer.php";
?>
