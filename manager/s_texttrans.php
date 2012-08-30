<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="addnew"){
        $source = $_POST["source"];
        $replace = $_POST["replace"];
        
        $db->query("Insert into text_transform (`source`, `replace`) values ('$source', '$replace')");
        
        $_SESSION["response_msg"] = "O noua regula de transformare a fost adaugata!";
        
        redirTo("s_texttrans.php");
    }
    
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        $db->query("Delete from text_transform where id=$id");
        
        $_SESSION["response_msg"] = "Regula de transformare a fost stearsa!";
        
        redirTo("s_texttrans.php");
    }
    
}


$page_name = "s_texttrans";
$page_title = "Text transformation";
include "include/header.php";
?>

<form name="addcat" id="addtt-form" method="post" action="s_texttrans.php?action=addnew">
<div class="form_item">
    <label for="source">Expresie in descriere</label>
    <input type="text" name="source" id="source" value="" class="txt" style="margin-right: 20px; width: 250px;">
    <label for="replace">Inlocuitor</label>
    <input type="text" name="replace" id="replace" value="" class="txt" style="margin-right: 20px; width: 250px;">
    <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addtt-form').submit();}" class="button"><span>Adauga</span></a>
    <div class="clear"></div>
</div>

<script language="javascript">
    //validation rules
    var arFields = new Array();
    var arField = new Array("Expresie in descriere", "source", "required"); arFields[1] = arField;            
    var arField = new Array("Inlocuitor", "replace", "required"); arFields[2] = arField;            
</script> 

<div class="form_item">        
    <label>&nbsp;</label>
    <div id="error-holder" style="display: none;"></div>
    <div class="clear"></div>
</div>

</form>

<hr/>

<div class="spacer10"></div>


<?
$words = $db->get_results("Select * from text_transform order by source asc");
if($words!=null){    
    ?>
        <div style="margin-bottom: 10px;">
            <div style="float: left; width: 300px; margin-right: 10px; padding: 4px; font-weight: bold; font-size: 14px;">
                Sursa
            </div>
            <div style="float: left; width: 300px; margin-right: 10px; padding: 4px; font-weight: bold; font-size: 14px;">
                Inlocuitor
            </div>
            <div class="clear"></div>
        </div>    
    <?
    foreach($words as $word){
        ?>
        <div style="margin-bottom: 10px;">
            <div style="float: left; width: 300px; margin-right: 10px; padding: 4px; background-color: #f2f2f2;">
                <?=$word->source?>
            </div>
            <div style="float: left; width: 300px; margin-right: 10px; padding: 4px; background-color: #f2f2f2;">
                <?=htmlspecialchars($word->replace)?>
            </div>
            <a href="javascript:void(0)" onclick="ConfirmDelete2Link('s_texttrans.php?action=delete&id=<?=$word->id?>')" class="button"><span>sterge</span></a>
            <div class="clear"></div>
        </div>
        <?
    }    
}
?>


<?php
include "include/footer.php";
?>
