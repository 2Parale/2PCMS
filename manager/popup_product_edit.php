<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user 

if(isset($_GET["action"])){
    
    if($_GET["action"]=="update"){
        $id = (int)$_POST["id"];
        $curPage = (int)$_POST["curPage"];
        
        $title = $_POST["title"];
        $title_url = $_POST["title_url"];
        $meta_desc = $_POST["meta_desc"];
        $description = mysql_real_escape_string($_POST["description"]);
        
        $db->query("Update shop_products set title='$title', title_url='$title_url', description='$description', meta_desc='$meta_desc' where id=$id");
        
        redirTo("popup_product_edit.php?id=$id&curPage=$curPage");
    }
    
}

$id = (int)$_GET["id"];
$curPage = (int)$_GET["curPage"];

$prod = $db->get_row("Select a.*, b.shop as partner_name, c.category as category_name, d.brand as brand_name from shop_products a 
left join aff_partners b on a.partner_id=b.id 
left join shop_categories c on a.category_id=c.id 
left join shop_brands d on a.brand_id=d.id 
where a.id=$id");
?>
<html>
<head>
<title>Editare produs</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/admin.css">
<script language="javascript" src="js/functions.js"></script>
<script language="javascript" src="../common/jquery-1.3.2.min.js"></script>

<script type="text/javascript" src="js/jHtmlArea-0.7.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jHtmlArea.css">

</head>
<body>

<div style="height: 26px; line-height: 26px; font-size: 18px; padding: 5px; border-bottom: 2px solid #f2f2f2; margin-bottom: 20px;">
    <div style="width: 80px; float: right;">
        <a href="javascript:void(0)" onclick="window.close()" class="button"><span>inchide</span></a>
        <div class="clear"></div>
    </div>
    <b>Editare produs:</b> <?=$prod->title?>
    <div class="clear"></div>
</div>

<form name="update-prod" id="update-prod" method="post" action="popup_product_edit.php?action=update">
<input type="hidden" name="id" value="<?=$prod->id?>">
<input type="hidden" name="curPage" value="<?=$curPage?>">

<div style="padding: 5px;">
    
    <div style="width: 635px; float: left; margin-right: 5px;">
    
        <div class="form_item">
            <label for="title">Titlu</label>
            <input type="text" name="title" id="title" value="<?=$prod->title?>" class="txt">
            <div class="clear"></div>
        </div>        
        
        <div class="form_item">
            <label for="title_url">URL</label>
            <input type="text" name="title_url" id="title_url" value="<?=$prod->title_url?>" class="txt">
            <div class="clear"></div>
        </div>        

        <div class="form_item">
            <label for="meta_desc">Meta description</label>
            <input type="text" name="meta_desc" id="meta_desc" value="<?=$prod->meta_desc?>" class="txt">
            <div class="clear"></div>
        </div>         
        
        <div class="form_item">
            <label for="description">Descriere</label>
            <div class="clear"></div>
            <textarea name="description" id="description" style="width: 635px" rows="20"><?=$prod->description?></textarea>
            <div class="clear"></div>
        </div> 
        
    </div>
    
    <div style="width: 315px; float: left;">
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Partener</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->partner_name?></span>
        <div class="clear"></div>
        <div class="spacer5"></div>
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">ID Original</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->original_id?></span>
        <div class="clear"></div>
        <div class="spacer5"></div>
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Categorie</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->category_name?></span>
        <div class="clear"></div>
        <div class="spacer5"></div>
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Brand</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->brand_name?></span>
        <div class="clear"></div>
        <div class="spacer5"></div>
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Pret</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->price?></span>        
        <div class="clear"></div>
        <div class="spacer5"></div>
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Status</span>
        <span style="width: 190px; float: left; padding: 5px;"><? if($prod->active==1){echo "ACTIV";}else{echo "INACTIV";} ?></span>        
        <div class="clear"></div>
        <div class="spacer5"></div>
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Data adaugare</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->create_date?></span>        
        <div class="clear"></div>
        <div class="spacer5"></div>                
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">URL 2Parale</span>
        <span style="width: 190px; float: left; padding: 5px;"><a href="<?=$prod->aff_url?>" target="_blank">click</a></span>                
        <div class="clear"></div>
        <div class="spacer10"></div>
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Afisari listari</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->show_inlisting?></span>        
        <div class="clear"></div>
        <div class="spacer5"></div>        
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Afisari pagina</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->show_inpage?></span>        
        <div class="clear"></div>
        <div class="spacer5"></div>                
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Click-uri</span>
        <span style="width: 190px; float: left; padding: 5px;"><?=$prod->click?></span>        
        <div class="clear"></div>
        <div class="spacer10"></div>                
        
        <span style="width: 100px; float: left; padding: 5px; background-color: #f2f2f2; margin-right: 5px;">Imagine</span>
        <span style="width: 190px; float: left; padding: 5px;">
            <img src="../product_images/<?=$prod->local_img_small?>" />
        </span>        
        <div class="clear"></div>        
        
    </div>
    
    <div class="clear"></div>
    
    <div style="margin-top: 20px; padding-top: 10px; border-top: 2px solid #f2f2f2;">
        <a href="javascript:void(0)" onclick="document.getElementById('update-prod').submit()" class="button"><span>Salveaza modificarile</span></a>        
        <input type="submit" style="width: 0px; height: 0px; border: 0px; padding: 0px;">
        <div class="clear"></div>
    </div>
    
</div>

</form>

<script type="text/javascript">
$(function(){
    $("textarea").htmlarea({
    toolbar: ["bold", "italic", "underline", "|", "h1", "h2", "h3", "|", "link", "unlink"]
    });
});
</script>

</body>
</html>