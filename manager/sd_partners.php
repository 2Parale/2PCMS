<?php
include "../common/common.php";
require_once('include/phpthumb/phpthumb.class.php');
if(!userIsOk()){redirTo("login.php");}//checking user


if(isset($_GET["action"])){
    
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        $recs = $db->get_results("Select * from aff_feed_source where partner_id=$id");
        if($recs!=null){
            foreach($recs as $rec){
                @unlink("../feeds/".$rec->feed_filename);
            }
        }        
        
        $img = $db->get_var("Select shop_logo from aff_partners where id=$id");
        if($img!=""){
            @unlink("../assets/partner-images/".$img);
        }
        
        //deleting images folder
        @unlink('../product_images/'.$id."/");
        
        $db->query("Delete from shop_products where partner_id=$id");
        $db->query("Delete from aff_categories where partner_id=$id");
        $db->query("Delete from aff_feeds where partner_id=$id");
        $db->query("Delete from aff_feed_source where partner_id=$id");
        $db->query("Delete from aff_partners where id=$id");
        
        $_SESSION["response_msg"] = "Partenerul si informatiile asociate au fost sterse!";                
        
        redirTo("sd_partners.php");
    }
    
    if($_GET["action"]=="addnew"){
        $shop = $_POST["shop"];
        $shop_url = $_POST["shop_url"];
        $network = $_POST["network"];
        $shop_desc = $_POST["shop_desc"];
        $shop_content = addslashes($_POST["shop_content"]);
                        
        $db->query("Insert into aff_partners (shop, shop_url, network, shop_desc, shop_content) values ('$shop', '$shop_url', '$network', '$shop_desc', '$shop_content')");
        $id = $db->lastId;
        
        if (!isset($_FILES["logo_img"]) || !is_uploaded_file($_FILES["logo_img"]["tmp_name"]) || $_FILES["logo_img"]["error"] != 0) {
            //nu avem fisier de upload
        } else {                    
            
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
        
        $_SESSION["response_msg"] = "Noul partener a fost adaugat!";
        
        redirTo("sd_partners.php");
    }
}


//datagrid include
include ("include/class.datagrid.php");
$myDG = new DataGrid($db);
parseClassDep2IncludeFile ("include/",$myDG->getDependencies());
$curPage = 1;
if(isset($_GET["curPage"])){$curPage = (int)$_GET["curPage"];if($curPage==0){$curPage = 1;}}


//jHtmlArea js and css include
addSuplementalIncludeFile("js/jHtmlArea-0.7.0.min.js","js");
addSuplementalIncludeFile("css/jHtmlArea.css","css");


$page_name = "sd_partners";
$page_title = "Parteneri Afiliere";
include "include/header.php";
?>

<a href="javascript:void(0)" onclick="$('#addnew').fadeIn('normal')">Adauga un nou partener de afiliere &raquo;</a>
<div class="spacer5"></div>

<div id="addnew" class="form-container">
<div class="form-title">Adauga un partner</div>
<form name="addnew" id="addnew-form" method="post" action="sd_partners.php?action=addnew" enctype="multipart/form-data" onsubmit="if(!validateForm(arFields, 'error-holder')){return false}">
    <div class="form_item">
        <label for="shop">Magazin</label>
        <input type="text" name="shop" id="shop" value="" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="shop_url">URL Magazin</label>
        <input type="text" name="shop_url" id="shop_url" value="" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="network">Retea</label>
        <select name="network" id="network">
            <option value="2Parale-ro">2Parale</option>
        </select>
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="shop_desc">Descriere interna</label>
        <input type="text" name="shop_desc" id="shop_desc" value="" class="txt">
        <div class="clear"></div>
    </div>                
    <div class="form_item">
        <label for="logo_img">Logo partener</label>
        <input type="file" name="logo_img" id="logo_img" value="" class="txt">
        <div class="clear"></div>
    </div>                
    <div class="form_item">
        <label for="shop_content" style="width: 460px;">Descriere publica <br /><span style="font-weight: normal; font-style: italic;">(se afiseaza in pagina produsului, daca este setat astfel in setari)</span></label>
        <div class="clear"></div>
        <textarea name="shop_content" id="shop_content" style="width: 460px; height: 200px;" rows="10"></textarea>
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
        
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addnew-form').submit();}" class="button"><span>Adauga</span></a>
        <a href="javascript:void(0)" onclick="$('#addnew').fadeOut('fast'); $('#error-holder').hide();" class="button"><span class="normal">Renunta</span></a>
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

<script type="text/javascript">
$(function(){
    $("#shop_content").htmlarea({
    toolbar: ["bold", "italic", "underline", "|", "h1", "h2", "h3", "|", "link", "unlink"]
    });
    $('#addnew').hide();
});

</script>


<hr/>

<div class="spacer10"></div>


<?php
$myDG->setSqlString ("Select * from aff_partners order by shop");
$myDG->setSqlCountString ("Select count(*) from aff_partners");

$myDG->setIdField("id");
$myDG->setSelfLink("sd_partners.php?");
$myDG->setCurrentPage($curPage);
$myDG->setRecPerPage(20);

//adding columns
$myDG->addColumn ('#', '', 'counter', '30px');
$myDG->addColumn ('Shop', '#<b>#|shop|#</b> - <a href="sd_feeds.php?pid=#|id|#">vezi feed-uri</a>#', 'field', '200px', 'left');
$myDG->addColumn ('Shop URL', '#<a href="#|shop_url|#" target="_blank">#|shop_url|#</a>#', 'field', '200px', 'left');
$myDG->addColumn ('Network', 'network', 'field', '150px', 'left');
$myDG->addColumn ('Shop Desc', 'shop_desc', 'field', '300px', 'left');

$myDG->addColumn ('Optiuni', '', 'options');


//adding options
#$myDG->addOption ('editeaza', 'images/edit.gif', 'sd_partners.php?action=edit#replace#', 'html');
$myDG->addOption ('editeaza', 'images/edit.gif', 'editPopup2(\'popup_edit.php?what=partner#replace#\');', 'js');
$myDG->addOption ('sterge', 'images/delete.gif', 'ConfirmDelete2Link(\'sd_partners.php?action=delete#replace#\');', 'js');


//draw
$myDG->Draw();
?>



<?php
include "include/footer.php";
?>
