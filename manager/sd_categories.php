<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user


if(isset($_GET["action"])){
    if($_GET["action"]=="save-categories"){
        $pid = (int)$_POST["pid"];
        $rowcount = (int)$_POST["rowcount"];
        
        for($i=1; $i<=$rowcount; $i++){
            $fcid = (int)$_POST["cat-id-".$i];
            $scid = (int)$_POST["shop-category-id-".$i];
            
            if($scid!=-1){
                $db->query("Update aff_categories set shop_category_id=$scid where id=$fcid");
            }elseif($scid==-1){
                $pcat_name = $db->get_var("Select category from aff_categories where id=$fcid");
                $scid = (int)$db->get_var("Select id from shop_categories where category='$pcat_name'");
                if($scid==0){
                    $pcat_name_url = text_to_url($pcat_name);
                    $db->query("Insert into shop_categories (category, category_url, parent_id) values ('$pcat_name', '$pcat_name_url', 0)");
                    $scid = $db->lastId;
                }
                $db->query("Update aff_categories set shop_category_id=$scid where id=$fcid");
            }
        }
        
        $_SESSION["response_msg"] = "Asocierile categoriilor au fost salvate!";
        
        redirTo("sd_categories.php?pid=$pid");
    }
}


$pid = 0; // partner id
if(isset($_GET["pid"])){$pid = (int)$_GET["pid"];}

$page_name = "sd_categories";
$page_title = "Categorii parteneri";
include "include/header.php";
?>

<form name="filter_feeds" id="filter_feeds" method="get" action="sd_categories.php">  
<span style="float: left; padding: 5px;">Selecteaza un partener pentru a vedea categoriile:</span>
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
    
    $catCount = $db->get_var("Select count(*) from aff_categories where partner_id=$pid");
    if($catCount==0){
        ?><b>Nu au fost detectate categorii pentru acest partener</b><?php
    }else{
        $cats = $db->get_results("Select * from aff_categories where partner_id=$pid order by category asc, subcategory asc");
        if($cats!=null){
            ?>
            <b>Urmatorul pas</b>: <a href="sd_products.php?pid=<?php echo $pid; ?>">import produse <?=$partner_name?></a>
            
            <div class="spacer10"></div>
            
            <form name="edit-cats" id="edit-cats" method="post" action="sd_categories.php?action=save-categories">
            <input type="hidden" name="pid" value="<?php echo $pid;?>">
            <div style="margin-bottom: 20px; text-align: right;">                
                <a href="javascript:void(0)" onclick="$('#edit-cats').submit();" class="button"><span>Salveaza modificarile</span></a>
                <a href="javascript:void(0)" onclick="$('#edit-cats')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
                <div class="clear"></div>
            </div>            
            
            <div style="margin-bottom: 10px;">
                <div style="float: left; width: 300px; font-weight: bold; font-size: 14; color: #444;">Categorie <?=$partner_name?>:</div>
                <div style="float: left; width: 300px; font-weight: bold; font-size: 14; color: #444;">Categorie CMS:</div>
                <div class="clear"></div>
            </div>
            
            <?php
            $i=0;
            $arShopCategorie = $db->get_results("select * from shop_categories");
            foreach($cats as $cat){
                $i++;
                ?>
                <div style="margin-bottom: 10px; border: 1px solid #8c8c8c; padding: 4px; <?php if($cat->shop_category_id==0){echo 'border-left: 4px solid darkred;';}else{echo 'border-left: 4px solid green;';}?>">
                    <input type="hidden" name="cat-id-<? echo $i; ?>" value="<? echo $cat->id; ?>">
                    <div style="float: left; width: 300px;">
                        <?php echo $cat->category; ?> / <?php echo $cat->subcategory; ?>
                    </div>
                    <div style="float: left; width: 300px;">
                        <select name="shop-category-id-<? echo $i; ?>" style="width: 200px;">
                            <option <?php if($cat->shop_category_id==0){echo 'selected="selected"';}?> value="0">-- nicio categorie</option>
                            <option value="-1">-- copiaza categoria sursa</option>
                            <?php
                            if($arShopCategorie!=null){
                                foreach($arShopCategorie as $sc){
                                    ?>
                                    <option <?php if($cat->shop_category_id==$sc->id){echo 'selected="selected"';}?> value="<?php echo $sc->id;?>"><?php echo $sc->category;?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>                    
                    <div class="clear"></div>
                </div>
                <?php
            }
            ?>
            <input type="hidden" name="rowcount" value="<?php echo $i;?>">
            <div style="margin-top: 10px; text-align: right;">
                <a href="javascript:void(0)" onclick="$('#edit-cats').submit();" class="button"><span>Salveaza modificarile</span></a>
                <a href="javascript:void(0)" onclick="$('#edit-cats')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
                <div class="clear"></div>
            </div>
            </form>
            <?php
        }
    }
}

?>




<?php
include "include/footer.php";
?>
