<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="addnew"){
        $category = $_POST["category"];
        $category_url = text_to_url($category);
        $details = mysql_real_escape_string($_POST["details"]);
        
        $db->query("Insert into shop_categories (category, category_url, parent_id, details) values ('$category', '$category_url', 0, '$details')");
        
        $_SESSION["response_msg"] = "O noua categorie a fost adaugata!";
        
        redirTo("s_categories.php");
    }
    
    if($_GET["action"]=="update_counts"){
        
        $db->query("update shop_categories a set a.pa_count=(Select count(id) from shop_products b where b.category_id=a.id and active=1), pi_count=(Select count(id) from shop_products b where b.category_id=a.id and active=0)");
        
        $_SESSION["response_msg"] = "Numarul de produse pe categorii a fost recalculat!";
        redirTo("s_categories.php");
    }
    
    if($_GET["action"]=="update"){
        $count = (int)$_POST["count"];
        
        for($i=1; $i<=$count; $i++){
            $id = $_POST["id-$i"];
            $category = $_POST["category-$i"];
            $category_url = text_to_url($category);
            $details = mysql_real_escape_string($_POST["category-details-$i"]);
            
            $db->query("Update shop_categories set 
            category='$category', 
            category_url='$category_url', 
            details='$details' 
            where id=$id");
        }
        
        $_SESSION["response_msg"] = "Categoriile au fost modificate!";
        
        redirTo("s_categories.php");
    }
        
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        $db->query("Delete from shop_categories where id=$id");
        
        $_SESSION["response_msg"] = "Categoria a fost stearsa!";
    
        redirTo("s_categories.php");
    }
    
}


$page_name = "s_categories";
$page_title = "Categorii magazin";
include "include/header.php";
?>


<form name="addcat" id="addcat-form" method="post" action="s_categories.php?action=addnew">
    <div class="form_item">
        <label for="category">Categorie noua</label>
        <input type="text" name="category" id="category" value="" class="txt" style="margin-right: 10px;">        
        
        <script language="javascript">
            //validation rules
            var arFields = new Array();
            var arField = new Array("Categorie", "category", "required"); arFields[1] = arField;                        
        </script>         
        
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addcat-form').submit();}" class="button"><span>Adauga</span></a>
        <div class="clear"></div>
    </div>    
    
    <div class="form_item">
        <label for="details">Descriere</label>
        <textarea name="details" id="details" rows="4" style="width: 300px;" onfocus="$('#details').animate({rows: '10'}, 200)" onblur="$('#details').animate({rows: '4'}, 200)"></textarea>
        <div class="clear"></div>
    </div>
    
    <div class="form_item">        
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>     
    
</form>
<hr/>

<div class="spacer10"></div>

<h3>Categorii existente - <a href="s_categories.php?action=update_counts">recalculeaza numar produse pe categorie</a></h3>


<form name="editcat-form" id="editcat-form" method="post" action="s_categories.php?action=update">
    <div style="text-align: right;">
        <a href="javascript:void(0)" onclick="$('#editcat-form').submit();" class="button"><span>Salveaza modificarile</span></a>
        <a href="javascript:void(0)" onclick="$('#editcat-form')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
        <div class="clear"></div>    
    </div>
    <div class="spacer10"></div>

    <?php
    $categories = $db->get_results("Select * from shop_categories order by category asc");
    if($categories!=null){
        $i=0;
        foreach($categories as $cat){
            $i++;
            ?>
            <div class="form_item" style="border-bottom: 1px solid #8c8c8c; padding-bottom: 10px;">
                <input type="hidden" name="id-<?php echo $i; ?>" value="<?php echo $cat->id?>">
                
                <label for="category-<?php echo $i; ?>" style="width: 100px">Categorie</label>
                <input type="text" name="category-<?php echo $i; ?>" id="category-<?php echo $i; ?>" value="<?php echo $cat->category?>" class="txt" style="width: 200px;">
                <div style="float: left; width: 20px;">&nbsp;</div>
                <label for="category_url-<?php echo $i; ?>" style="width: 100px">URL</label>
                <input type="text" name="category_url-<?php echo $i; ?>" id="category_url-<?php echo $i; ?>" value="<?php echo $cat->category_url?>" class="txt" style="width: 200px;">
                <div style="float: left; width: 20px;">&nbsp;</div>
                <?php if((int)$db->get_var("Select count(*) from shop_products where category_id=".$cat->id)==0){ ?>
                <input type="button" value="Sterge" onclick="ConfirmDelete2Link('s_categories.php?action=delete&id=<?php echo $cat->id; ?>')">
                <?php } ?>
                <div class="clear"></div>
                <label for="category-details-<?php echo $i; ?>" style="width: 100px">Detalii</label>
                <textarea name="category-details-<?php echo $i; ?>" id="category-details-<?php echo $i; ?>" rows="4" style="width: 530px;" onfocus="$('#category-details-<?php echo $i; ?>').animate({rows: '10'}, 200)" onblur="$('#category-details-<?php echo $i; ?>').animate({rows: '4'}, 200)"><?php echo stripslashes($cat->details)?></textarea>
                
                <div style="float: right; width: 340px;">
                    <div style="margin-bottom: 10px;">Produse in categorie</div>
                    <span style="padding: 5px 2px; font-weight: bold; margin-right: 20px; background-color: #f2f2f2;"><?=$cat->pa_count?> ACTIVE</span>
                    <span style="padding: 5px 2px; font-weight: bold; margin-right: 20px; background-color: #f2f2f2;"><?=$cat->pi_count?> INACTIVE</span>
                </div>
                
            </div>
            <div class="spacer10"></div>
            <?php
        }
    }
    ?>    
    
    <input type="hidden" name="count" value="<?php echo $i;?>">
    
    <div class="spacer10"></div>    
    <div style="text-align: right;">
          <a href="javascript:void(0)" onclick="$('#editcat-form').submit();" class="button"><span>Salveaza modificarile</span></a>
        <a href="javascript:void(0)" onclick="$('#editcat-form')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
        <div class="clear"></div>    
    </div>    
</form>



<?php
include "include/footer.php";
?>
