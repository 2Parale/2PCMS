<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        $db->query("Delete from landing_pages where id=$id");
        
        $_SESSION["response_msg"] = "Landing Page a fost sters!";       
        redirTo("con_landingpages.php");        
    }
    
    if($_GET["action"]=="add"){
        $product_id = (int)$_POST["product_id"];
        $new_title = addslashes($_POST["new_title"]);
        $lp_url = text_to_url($_POST["lp_url"]);
        $meta_keys = addslashes($_POST["meta_keys"]);
        $meta_desc = addslashes($_POST["meta_desc"]);
        $extra_products_count = (int)$_POST["extra_products_count"];
        $new_description = addslashes($_POST["new_description"]);        
        
        $db->query("Insert into landing_pages (product_id, new_title, new_description, lp_url, meta_keys, meta_desc, extra_products_count, shows, pubdate) 
        values 
        ($product_id, '$new_title', '$new_description', '$lp_url', '$meta_keys', '$meta_desc', $extra_products_count, 0, NOW())");
        
        $_SESSION["response_msg"] = "Un nou Landing Page a fost adaugat!";       
        redirTo("con_landingpages.php");
    }

    
}



//jHtmlArea js and css include
addSuplementalIncludeFile("js/jHtmlArea-0.7.0.min.js","js");
addSuplementalIncludeFile("css/jHtmlArea.css","css");


$page_name = "con_landingpages";
$page_title = "Landing pages";
include "include/header.php";
?>

<a href="javascript:void(0)" onclick="$('#new-lp-container').fadeIn('fast')">Defineste un nou landing page &raquo;</a>

<div class="spacer10"></div>

<div id="new-lp-container" class="form-container" style="display: none;">
    <div class="form-title">Adauga un nou landing page in 2 pasi</div>
    
    <div style="margin-bottom: 5px; border: 2px solid #f2f2f2; padding: 3px;">
        <div style="background-color: #F0CC2D; padding: 5px; font-weight: bold;"><span style="font-size: 16px;">1.</span> Alege un produs</div>
        <div id="new-lp1" style="padding: 5px;" class="form_item">
            <label for="search-prod">Cauta un produs </label>
            <input type="text" class="txt" id="search-prod" value="" style="margin-right: 10px;">
            <a href="javascript:void(0)" onclick="lpSearchProducts('search-prod', 'prod-results', 0)" class="button"><span>Cauta</span></a>
            <div class="clear"></div>
            <div id="prod-results" style="display: none;"></div>
        </div>
    </div>

    <div style="margin-bottom: 5px; border: 2px solid #f2f2f2; padding: 3px;">
        <div style="background-color: #F0CC2D; padding: 5px; font-weight: bold;"><span style="font-size: 16px;">2.</span> Editeaza detalii</div>
        <div id="new-lp2" style="display: none;">
        
        <form name="addLP" id="addLP" method="post" action="con_landingpages.php?action=add" onsubmit="if(!validateForm(arFields, 'error-holder')){return false}">
            <input type="hidden" name="product_id" id="product_id" value="">
            
            <div class="form_item">
                <label for="new_title">Titlu pagina</label>
                <input type="text" name="new_title" id="new_title" class="txt" value="">
                <div class="clear"></div>
            </div>

            <div class="form_item">
                <label for="lp_url">URL pagina</label>
                <input type="text" name="lp_url" id="lp_url" class="txt" value="">
                <div class="clear"></div>
            </div>
            
            <div class="form_item">
                <label for="meta_keys">Meta Keys</label>
                <input type="text" name="meta_keys" id="meta_keys" class="txt" value="">
                <div class="clear"></div>
            </div>
            
            <div class="form_item">
                <label for="meta_desc">Meta Desc</label>
                <input type="text" name="meta_desc" id="meta_desc" class="txt" value="">
                <div class="clear"></div>
            </div>                        

            <div class="form_item">
                <label for="extra_products_count">Numar produse extra</label>
                <input type="text" name="extra_products_count" id="extra_products_count" class="txt" value="8" style="width: 50px;">
                <div class="clear"></div>
            </div>
            
            <div class="form_item">
                <label for="new_description">Descriere</label>
                <div class="clear"></div>
                <textarea name="new_description" id="new_description" rows="15" style="width: 600px;"></textarea>
                <div class="clear"></div>
            </div>            
            
            <div class="form_item">        
                <div class="spacer10"></div>
                
                <script language="javascript">
                    //validation rules
                    var arFields = new Array();
                    var arField = new Array("Titlu", "new_title", "required"); arFields[1] = arField;            
                    var arField = new Array("URL", "lp_url", "required"); arFields[2] = arField;            
                    var arField = new Array("Meta Desc", "meta_desc", "required"); arFields[3] = arField;            
                    var arField = new Array("Numar produse extra", "extra_products_count", "required"); arFields[4] = arField;            
                    var arField = new Array("Descriere", "new_description", "required"); arFields[5] = arField;            
                </script>
                
                <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addLP').submit();}" class="button"><span>Adauga</span></a>
                <a href="javascript:void(0)" onclick="lpResetForm()" class="button"><span>Alege alt produs</span></a>
                <input type="submit" name="cmdSubmit" value="" style="width: 0px; height: 0px; border: 0px; margin: 0px; padding: 0px;">
                <div class="clear"></div>
            </div>
            
            <div class="form_item">        
                <label>&nbsp;</label>
                <div id="error-holder" style="display: none;"></div>
                <div class="clear"></div>
            </div>            
            
        </form>
        
        </div>
    </div>
    
    
</div>


<div class="spacer10"></div>
<hr/>

<h3>Landing pages definite</h3>

<?
$lps = $db->get_results("Select a.*, b.title, b.title_url, b.price, DATE_FORMAT(a.pubdate, '%d %M %Y') as pubdate_nice from landing_pages a 
left join shop_products b on a.product_id=b.id 
order by a.new_title");

if($lps==null){
    ?>
    <i>Nu exista langing pages definite</i>
    <?
}else{
    foreach ($lps as $lp){
        ?>
        <div style="border: 2px solid #f2f2f2; padding: 5px; margin-bottom: 10px;">
            <div style="background-color: #f2f2f2; padding: 5px; font-weight: bold;"><?=$lp->new_title?> - <a href="<?=$_CONFIG["urlpath"]?>lp/<?=$lp->lp_url?>-<?=$lp->id?>.html" target="_blank">vezi pagina</a></div>
            <div style="padding: 4px;"><b>URL:</b> 
                <input type="text" value="<?=$_CONFIG["urlpath"]?>lp/<?=$lp->lp_url?>-<?=$lp->id?>.html" style="padding: 2px; width: 500px;">
            </div>
            <div style="padding: 4px;"><b>Produs:</b> <a href="<?=$_CONFIG["urlpath"]?><?=$lp->title_url?>-<?=$lp->id?>.html" target="_blank"><?=$lp->title?> - <?=$lp->price?></a></div>
            <div style="padding: 4px;"><b>Afisari:</b> <?=$lp->shows?> | <b>Data publicarii:</b> <?=$lp->pubdate_nice?></div>
            <div style="padding: 4px;"><a href="javascript:void(0)" onclick="ConfirmDelete2Link('con_landingpages.php?action=delete&id=<?=$lp->id?>')" class="button"><span>sterge</span></a><div class="clear"></div></div>
        </div>
        <?
    }
}
?>
    




<?php
include "include/footer.php";
?>
