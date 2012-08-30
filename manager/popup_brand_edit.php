<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user 

if(isset($_GET["action"])){
    
    if($_GET["action"]=="update"){
        $id = (int)$_POST["id"];
        $what = $_POST["what"];
        
        $brand_url = $_POST["brand_url"];
        $details = mysql_real_escape_string($_POST["details"]);
        
        $db->query("Update shop_brands set brand_url='$brand_url', details='$details' where id=$id");
        
        ?>
        <script type="text/javascript">
            window.close();
        </script>
        <?
    }
    
}

$id = (int)$_GET["id"];
$what = $_GET["what"];

$brand = $db->get_row("Select * from shop_brands where id=$id");
?>
<html>
<head>
<title>Editare brand</title>
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
    <b>Editare brand:</b> <?=$brand->brand?>
    <div class="clear"></div>
</div>

<form name="update-brand" id="update-brand" method="post" action="popup_brand_edit.php?action=update">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="what" value="<?=$what?>">

<div style="padding: 5px;">
    
    <div class="form_item">
        <label>Titlu</label>
        <div style="float: left; padding: 2px;"><?=$brand->brand?></div>
        <div class="clear"></div>
    </div>        
    
    <div class="form_item">
        <label for="brand_url">URL</label>
        <input type="text" name="brand_url" id="brand_url" value="<?=$brand->brand_url?>" class="txt">
        <div class="clear"></div>
    </div>        

    <div class="form_item">
        <label for="details">Descriere</label>
        <div class="clear"></div>
        <textarea name="details" id="details" style="width: 470px" rows="10"><?=$brand->details?></textarea>
        <div class="clear"></div>
    </div> 

    
    <div style="margin-top: 20px; padding-top: 10px; border-top: 2px solid #f2f2f2;">
        <a href="javascript:void(0)" onclick="document.getElementById('update-brand').submit()" class="button"><span>Salveaza modificarile</span></a>        
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