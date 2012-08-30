<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="addnew"){
        $label = $_POST["label"];
        $vmin = (int)$_POST["vmin"];
        $vmax = (int)$_POST["vmax"];
        
        
        $db->query("Insert into shop_priceranges (label, vmin, vmax) values ('$label', '$vmin', '$vmax')");
        
        $_SESSION["response_msg"] = "Un nou range de preturi a fost adaugat!";
        
        redirTo("s_pricerange.php");
    }

    if($_GET["action"]=="update"){
        $count = (int)$_POST["count"];
        
        for($i=1; $i<=$count; $i++){
            $id = $_POST["id-$i"];
            $label = $_POST["label-$i"];
            $vmin = $_POST["vmin-$i"];
            $vmax = $_POST["vmax-$i"];
            
            
            $db->query("Update shop_priceranges set 
            label='$label', 
            vmin='$vmin', 
            vmax='$vmax' 
            where id=$id");
        }
        
        $_SESSION["response_msg"] = "Range-urile de preturi au fost modificate!";
        
        redirTo("s_pricerange.php");
    }
        
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        $db->query("Delete from shop_priceranges where id=$id");
        
        $_SESSION["response_msg"] = "Range-ul de preturi a fost sters!";
        
        redirTo("s_pricerange.php");
    }
    
}


$page_name = "s_pricerange";
$page_title = "Price range magazin";
include "include/header.php";
?>


<form name="addprice-form" id="addprice-form" method="post" action="s_pricerange.php?action=addnew">
    <div class="form_item">
        <label for="label">Label price range</label>
        <input type="text" name="label" id="label" value="" class="txt" style="width: 200px; margin-right: 10px;">
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addprice-form').submit();}" class="button"><span>Adauga</span></a>
        <div class="clear"></div>
    </div>    
    <div class="form_item">
        <label for="vmin">Valoare minima</label>
        <input type="text" name="vmin" id="vmin" value="" class="txt" style="width: 50px">        
        <div class="clear"></div>
    </div>    
    <div class="form_item">
        <label for="vmax">Valoare maxima</label>
        <input type="text" name="vmax" id="vmax" value="" class="txt" style="width: 50px">        
        <div class="clear"></div>
    </div>            
    
    <div class="form_item">        
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>        
    
    <script language="javascript">
        //validation rules
        var arFields = new Array();
        var arField = new Array("Label price range", "label", "required"); arFields[1] = arField;            
        var arField = new Array("Valoare minima", "vmin", "required"); arFields[2] = arField;            
        var arField = new Array("Valoare maxima", "vmax", "required"); arFields[3] = arField;            
    </script>      
    
</form>
<hr/>

<div class="spacer10"></div>

<h3>Price range existente</h3>


<form name="editprice-form" id="editprice-form" method="post" action="s_pricerange.php?action=update">
    <div style="text-align: right;">
        <a href="javascript:void(0)" onclick="$('#editprice-form').submit();" class="button"><span>Salveaza modificarile</span></a>
        <a href="javascript:void(0)" onclick="$('#editprice-form')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
        <div class="clear"></div>         
    </div>
    <div class="spacer10"></div>

    <?php
    $pranges = $db->get_results("Select * from shop_priceranges order by vmin asc");
    if($pranges!=null){
        $i=0;
        foreach($pranges as $cat){
            $i++;
            ?>
            <div class="form_item" style="border-bottom: 1px solid #8c8c8c; padding-bottom: 10px;">
                <input type="hidden" name="id-<?php echo $i; ?>" value="<?php echo $cat->id?>">
                
                <label for="label-<?php echo $i; ?>" style="width: 100px">Label</label>
                <input type="text" name="label-<?php echo $i; ?>" id="label-<?php echo $i; ?>" value="<?php echo $cat->label?>" class="txt" style="width: 200px;">
                <div style="float: left; width: 20px;">&nbsp;</div>
                
                <label for="vmin-<?php echo $i; ?>" style="width: 100px">VMin</label>
                <input type="text" name="vmin-<?php echo $i; ?>" id="vmin-<?php echo $i; ?>" value="<?php echo $cat->vmin?>" class="txt" style="width: 50px;">
                <div style="float: left; width: 20px;">&nbsp;</div>

                <label for="vmax-<?php echo $i; ?>" style="width: 100px">VMax</label>
                <input type="text" name="vmax-<?php echo $i; ?>" id="vmax-<?php echo $i; ?>" value="<?php echo $cat->vmax?>" class="txt" style="width: 50px;">
                <div style="float: left; width: 20px;">&nbsp;</div>                
                
                <a href="javascript:void(0)" onclick="ConfirmDelete2Link('s_pricerange.php?action=delete&id=<?php echo $cat->id; ?>')" class="button"><span>Sterge</span></a>
                
                <div class="clear"></div>
            </div>
            <div class="spacer10"></div>
            <?php
        }
    }
    ?>    
    
    <input type="hidden" name="count" value="<?php echo $i;?>">
    
    <div class="spacer10"></div>    
    <div style="text-align: right;">
        <a href="javascript:void(0)" onclick="$('#editprice-form').submit();" class="button"><span>Salveaza modificarile</span></a>
        <a href="javascript:void(0)" onclick="$('#editprice-form')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
        <div class="clear"></div>         
    </div>    
</form>



<?php
include "include/footer.php";
?>
