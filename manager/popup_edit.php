<?php
include "../common/common.php";
require_once('include/phpthumb/phpthumb.class.php');

if(!userIsOk()){redirTo("login.php");}//checking user 

if(isset($_GET["action"])){
    
    if($_GET["action"]=="update_partner"){
        $id = (int)$_POST["id"];
        $curPage = (int)$_POST["curPage"];
        
        $shop = $_POST["shop"];
        $shop_url = $_POST["shop_url"];
        $network = $_POST["network"];
        $shop_desc = $_POST["shop_desc"];
        $cron_sync = $_POST["cron_sync"];
        $shop_content = addslashes($_POST["shop_content"]);
        
        $db->query("Update aff_partners set shop='$shop', shop_url='$shop_url', network='$network', shop_desc='$shop_desc', cron_sync='$cron_sync', shop_content='$shop_content' where id=$id");


        if (!isset($_FILES["logo_img"]) || !is_uploaded_file($_FILES["logo_img"]["tmp_name"]) || $_FILES["logo_img"]["error"] != 0) {
            //nu avem fisier de upload
        } else {                    
            
            $old_img = $db->get_var("Select shop_logo from aff_partners where id=$id");
            if($old_img!=""){@unlink("../assets/partner-images/".$old_img);}            
            
            $folder_structure = "../assets/partner-images/";
            
            //figure out file name
            $format = "jpg";
            if ($_FILES["logo_img"]["type"] == "image/jpeg"){$format = "jpg";}    
            if ($_FILES["logo_img"]["type"] == "image/pjpeg"){$format = "jpg";}    
            if ($_FILES["logo_img"]["type"] == "image/gif"){$format = "gif";}                        
            if ($_FILES["logo_img"]["type"] == "image/png"){$format = "png";}                        
            $new_file_name = $id . "." . $format;           

            //upload                        
            move_uploaded_file($_FILES["logo_img"]['tmp_name'],$folder_structure.$new_file_name);                                               
            
            //thumb big
            $uploadedfile = $folder_structure.$new_file_name;
            $uploadedfileThumb = $folder_structure.$new_file_name;
            $size=getimagesize( $uploadedfile );                            
            $phpThumb = new phpThumb();                        
            $phpThumb->config_output_format = $format;
            $phpThumb->src = $uploadedfile;
            $phpThumb->w = $_CONFIG["settings"]["partner_logo_width"];
            $phpThumb->setParameter("bg", "ffffff");            
            $phpThumb->GenerateThumbnail();
            $phpThumb->RenderToFile( $uploadedfileThumb );            
            
            $db->query("update aff_partners set shop_logo='$new_file_name' where id=$id");
        }        

        
        $_SESSION["response_msg"] = "Modificarile au fost salvate!";
        ?>
        <script language="javascript">
            window.opener.document.location = "sd_partners.php?curPage=<?=$curPage?>";
            window.close();
        </script>
        <?
    }

    if($_GET["action"]=="update_feed"){
        $id = (int)$_POST["id"];
        $curPage = (int)$_POST["curPage"];        
        
        $partner_id = (int)$_POST["partner_id"];
        $feed_url = $_POST["feed_url"];
        $feed_desc = $_POST["feed_desc"];
        $price_format = $_POST["price_format"];
        
        //if price_format is changed we process the prices for the whole feed in the feed data and the product data
        $old_price_format = $db->get_var("Select price_format from aff_feeds where id=$id");
        if($price_format!=$old_price_format){
            
            //change feed data
            $db->query("Update aff_feed_source a set 
            price_int=IF((Select price_format from aff_feeds b where b.id=a.feed_id)='pricedot' , CAST(REPLACE(a.price,',','') AS DECIMAL(20,2)), CAST(REPLACE(REPLACE(a.price,'.',''), ',', '.') AS DECIMAL(20,2))) 
            where a.feed_id=$id");
            
            //change product data
            $db->query("Update shop_products a set 
            price_int=(Select price_int from aff_feed_source b where b.partner_id=a.partner_id and a.original_id_int=b.product_id_int) 
            where a.partner_id=$partner_id");
            $db->query("Update shop_products set
            old_price = price, 
            old_price_int = price_int 
            where partner_id=$partner_id");
        }
        
        
        
        $db->query("Update aff_feeds set partner_id=$partner_id, feed_url='$feed_url', feed_desc='$feed_desc', price_format='$price_format' where id=$id");
        
        $_SESSION["response_msg"] = "Modificarile au fost salvate!";
        ?>
        <script language="javascript">
            window.opener.document.location = "sd_feeds.php?curPage=<?=$curPage?>";
            window.close();
        </script>        
        <?
    }
    
}


$what = $_GET["what"];  
$id = (int)$_GET["id"];  
$curPage = (int)$_GET["curPage"];  

$title = "";
$url2open = "";

switch ($what){
    case "partner":
        $title = "Editare partener";
        $url2open = "sd_partners.php?curPage=".$curPage;        
        $row = $db->get_row("Select * from aff_partners where id=$id");
        break;
    case "feed":
        $title = "Editare feed";
        $url2open = "sd_feeds.php?curPage=".$curPage;                
        $row = $db->get_row("Select * from aff_feeds where id=$id");
        break;        
}


?>
<html>
<head>
<title><?=$title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/admin.css">
<script language="javascript" src="../common/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript" src="js/jHtmlArea-0.7.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jHtmlArea.css">
</head>
<body>

<div style="padding: 5px;">

<?
if($what=="partner"){
    ?>
    
    <div style="margin-bottom: 20px; font-size: 18px; font-weight: bold; color: #444; background-color: #ffffcc; padding: 10px 5px;">Editare partner <?=$row->id?>::<?=$row->shop?></div>
   
 <form name="addnew" id="addnew-form" method="post" action="popup_edit.php?action=update_partner" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?=$id?>">
    <input type="hidden" name="curPage" value="<?=$curPage?>"> 
        

            
    <div class="form_item">
        <label for="shop">Magazin</label>
        <input type="text" name="shop" id="shop" value="<?=$row->shop?>" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="shop_url">URL Magazin</label>
        <input type="text" name="shop_url" id="shop_url" value="<?=$row->shop_url?>" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="network">Retea</label>
        <select name="network" id="network">
            <option <?if($row->network=='2Parale-ro'){echo 'selected="selected"';}?> value="2Parale-ro">2Parale</option>
        </select>
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="shop_desc">Descriere</label>
        <input type="text" name="shop_desc" id="shop_desc" value="<?=$row->shop_desc?>" class="txt">
        <div class="clear"></div>
    </div>                
    <div class="form_item">
        <label for="cron_sync">CRON sync</label>
        <select name="cron_sync" id="cron_sync">
            <option <?if($row->cron_sync==0){echo 'selected="selected"';}?> value="0">NU</option>
            <option <?if($row->cron_sync==1){echo 'selected="selected"';}?> value="1">DA</option>
        </select>
        <div class="clear"></div>
    </div>     
    
    <div class="form_item">
        <label for="logo_img">Logo partener</label>
        <input type="file" name="logo_img" id="logo_img" class="txt">
        <?
        if($row->shop_logo!=""){?><div class="clear"></div><img src="../assets/partner-images/<?=$row->shop_logo?>" border="0" alt="" /><?}
        ?>         
        <div class="clear"></div>
    </div>                
    <div class="form_item">
        <label for="shop_content" style="width: 460px;">Descriere publica <br /><span style="font-weight: normal; font-style: italic;">(se afiseaza in pagina produsului, daca este setat astfel in setari)</span></label>
        <div class="clear"></div>
        <textarea name="shop_content" id="shop_content" style="width: 460px; height: 200px;" rows="10"><?=stripslashes($row->shop_content)?></textarea>
        <div class="clear"></div>
    </div>    
    
    <div class="form_item">        
        <div class="spacer10"></div>
        
        <script language="javascript">
            //validation rules
            var arFields = new Array();
            var arField = new Array("Magazin", "shop", "required"); arFields[1] = arField;            
            var arField = new Array("URL Magazin", "shop_url", "required"); arFields[2] = arField;            
        </script>
        
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addnew-form').submit();}" class="button"><span>Salveaza</span></a>
        <a href="javascript:void(0)" onclick="window.close();" class="button"><span class="normal">Renunta</span></a>        
        <div class="clear"></div>
    </div>
    
    <div class="form_item">        
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>
    
</form>

<script type="text/javascript">
$(function(){
    $("textarea#shop_content").htmlarea({
    toolbar: ["bold", "italic", "underline", "|", "h1", "h2", "h3", "|", "link", "unlink"]
    });
});
</script>

    <?
}


if($what=="feed"){
    
    ?>
    
    <div style="margin-bottom: 20px; font-size: 18px; font-weight: bold; color: #444; background-color: #ffffcc; padding: 10px 5px;">Editare feed <?=$row->id?>::<?=$row->feed_desc?></div>
    
<form name="addnew" id="addnew-form" method="post" action="popup_edit.php?action=update_feed">
    <input type="hidden" name="id" value="<?=$id?>">
    <input type="hidden" name="curPage" value="<?=$curPage?>">    
    <div class="form_item">
        <label for="partner_id">Partener</label>
        <select name="partner_id" id="partner_id">
            <?php
            $ps = $db->get_results("Select * from aff_partners order by shop asc");
            if($ps!=null){
                foreach ($ps as $p){
                    ?>
                    <option <?if($row->partner_id==$p->id){echo 'selected="selected"';}?> value="<?php echo $p->id; ?>"><?php echo $p->shop; ?></option>
                    <?php
                }
            }
            ?>
        </select>        
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="feed_url">URL Feed</label>
        <input type="text" name="feed_url" id="feed_url" value="<?=$row->feed_url?>" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="feed_desc">Descriere</label>
        <input type="text" name="feed_desc" id="feed_desc" value="<?=$row->feed_desc?>" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="price_format">Format preturi</label>
        <select name="price_format">
            <option <?if($row->price_format=="pricedot"){echo 'selected="selected"';}?> value="pricedot">punct pentru zecimale si virgula pentru mii</option>
            <option <?if($row->price_format=="pricecomma"){echo 'selected="selected"';}?> value="pricecomma">virgula pentru zecimale si punct pentru mii</option>
        </select>
        <div class="clear"></div>
    </div>                    
    <div class="form_item">
        <div class="spacer10"></div>
        
        <script language="javascript">
            //validation rules
            var arFields = new Array();
            var arField = new Array("URL Feed", "feed_url", "required"); arFields[1] = arField;            
            var arField = new Array("Descriere", "feed_desc", "required"); arFields[2] = arField;            
        </script>        
        
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addnew-form').submit();}" class="button"><span>Salveaza</span></a>
        <a href="javascript:void(0)" onclick="window.close();" class="button"><span class="normal">Renunta</span></a>
        <div class="clear"></div>
    </div>    

    
    <div class="form_item">        
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>    
    
</form>    
    
    <?
    
}
?>


</div>

</body>
</html>