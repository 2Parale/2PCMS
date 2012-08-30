<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="addnew"){
        $category = $_POST["category"];
        $category_url = text_to_url($category);
        $position = (int)$db->get_var("Select count(id) from article_categories") + 1;
        
        $db->query("Insert into article_categories 
        (category, category_url, article_count, shows, position) 
        values 
        ('$category', '$category_url', 0, 0, $position)");
        
        $_SESSION["response_msg"] = "Categoria a fost adaugata!";
        
        redirTo("con_article_categories.php");
    }

    
    if($_GET["action"]=="update"){
        $count = (int)$_POST["count"];
        
        for($i=1; $i<=$count; $i++){
            $id = $_POST["id-$i"];
            $category = $_POST["category-$i"];
            $category_url = text_to_url($category);
            
            $db->query("Update article_categories set 
            category='$category', 
            category_url='$category_url' 
            where id=$id");
        }
        
        $_SESSION["response_msg"] = "Categoriile au fost modificate!";
        
        redirTo("con_article_categories.php");
    }
    
    
    if($_GET["action"]=="move"){
        $id = (int)$_GET["id"];
        $how = $_GET["how"];
        
        $pos = (int)$db->get_var("Select position from article_categories where id=$id");
        $max = (int)$db->get_var("Select count(id) from article_categories");
        
        if($how=="up"){$new_pos=$pos-1;}
        if($how=="down"){$new_pos=$pos+1;}
        
        if($new_pos>0 and $new_pos<=$max){
            $db->query("Update article_categories set position=$pos where position=$new_pos");
            $db->query("Update article_categories set position=$new_pos where id=$id");
        }
        
        $_SESSION["response_msg"] = "Pozitiile categoriilor au fost modificate!";
        
        redirTo("con_article_categories.php");        
    }
    
    
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        $db->query("Delete from article_categories where id=$id");
        
        $_SESSION["response_msg"] = "Categoria a fost stearsa!";
    
        redirTo("con_article_categories.php");
    }
    
}


$page_name = "con_article_categories";
$page_title = "Categorii articole";
include "include/header.php";
?>


<form name="addcat" id="addcat-form" method="post" action="con_article_categories.php?action=addnew">
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
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>     
    
</form>
<hr/>

<div class="spacer10"></div>

<h3>Categorii existente</h3>




    <?php
    $categories = $db->get_results("Select * from article_categories order by position");
    if($categories==null){
        ?>
        <i>Nu exista categorii de articole definite</i>
        <?
    }else{
        $i=0;
        ?>

<form name="editcat-form" id="editcat-form" method="post" action="con_article_categories.php?action=update">
    <div style="text-align: right;">
        <a href="javascript:void(0)" onclick="$('#editcat-form').submit();" class="button"><span>Salveaza modificarile</span></a>
        <a href="javascript:void(0)" onclick="$('#editcat-form')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
        <div class="clear"></div>    
    </div>
    <div class="spacer10"></div>        
        
        <?
        foreach($categories as $cat){
            $i++;
            ?>
            <div class="form_item" style="border: 2px solid #f2f2f2; padding: 5px;">
                <input type="hidden" name="id-<?php echo $i; ?>" value="<?php echo $cat->id?>">
                
                <div style="background-color: #f2f2f2; padding: 5px; font-weight: bold; margin-bottom: 5px;">
                    <div style="float: right;">Articole: <?=$cat->article_count?> | Afisari: <?=$cat->shows?></div>
                    <?=$cat->category?>
                    <div class="clear"></div>
                </div>
                
                <label for="category-<?php echo $i; ?>" style="width: 100px">Categorie</label>
                <input type="text" name="category-<?php echo $i; ?>" id="category-<?php echo $i; ?>" value="<?=$cat->category?>" class="txt" style="width: 200px;">
                <div style="float: left; width: 20px;">&nbsp;</div>
                
                <label for="category_url-<?php echo $i; ?>" style="width: 100px">URL</label>
                <input type="text" name="category_url-<?php echo $i; ?>" id="category_url-<?php echo $i; ?>" value="<?php echo $cat->category_url?>" class="txt" style="width: 200px;">
                <div style="float: left; width: 20px;">&nbsp;</div>
                
                <div style="float: left;">
                    <a href="con_article_categories.php?action=move&how=up&id=<?=$cat->id?>" class="button"><span>SUS</span></a>        
                    <a href="con_article_categories.php?action=move&how=down&id=<?=$cat->id?>" class="button"><span>JOS</span></a>        
                <?php if((int)$db->get_var("Select count(id) from articles where category_id=".$cat->id)==0){ ?>
                    <div style="float: left; width: 50px;">&nbsp;</div>
                    <a href="javascript:void(0)" onclick="ConfirmDelete2Link('con_article_categories.php?action=delete&id=<?php echo $cat->id; ?>')" class="button"><span>STERGE</span></a>        
                <?php } ?>
                </div>
                
                <div class="clear"></div>
                
            </div>
            <div class="spacer10"></div>
            <?php
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
        
        <?
    }
    ?>    
    




<?php
include "include/footer.php";
?>
