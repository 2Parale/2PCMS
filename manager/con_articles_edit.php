<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="save"){
        $id = (int)$_POST["id"];
        $curPage = (int)$_POST["curPage"];
        $category_id = (int)$_POST["category_id"];
        $title = addslashes($_POST["title"]);
        $title_url = $_POST["title_url"];
        $meta_desc = addslashes($_POST["meta_desc"]);
        $acontent = addslashes($_POST["acontent"]);
        
        if($id==0){
            //insert 
            $db->query("Insert into articles (category_id, title, title_url, meta_desc, acontent, pubdate, shows) 
            values ($category_id, '$title', '$title_url', '$meta_desc', '$acontent', NOW(), 0)");
            $id = $db->lastId;
            
            $_SESSION["response_msg"] = "Articolul a fost adaugat!";
        }else{
            //update            
            $db->query("Update articles set 
            category_id=$category_id, 
            title='$title', 
            title_url='$title_url', 
            meta_desc='$meta_desc', 
            acontent='$acontent' 
            where id=$id");
            
            $_SESSION["response_msg"] = "Articolul a fost modificat!";
        }
        
        $db->query("Update article_categories a set a.article_count=(Select count(id) from articles b where b.category_id=a.id)");
        redirTo("con_articles_edit.php?curPage=".$curPage."&id=".$id);
    }
    
}


$curPage = 1;
$id = 0;
if(isset($_GET["curPage"])){$curPage=(int)$_GET["curPage"];}
if(isset($_GET["id"])){$id=(int)$_GET["id"];}


$category_id = 0;
$title = "";
$title_url = "";
$meta_desc = "";
$acontent = "";
$pubdate = "";
$shows = "";

if($id>0){
    $article = $db->get_row("Select *, DATE_FORMAT(pubdate, '%d %M %Y %H:%i:%s') as pubdate_nice from articles where id=$id");
    $category_id = $article->category_id;
    $title = $article->title;
    $title_url = $article->title_url;
    $meta_desc = $article->meta_desc;
    $acontent = $article->acontent;
    $pubdate = $article->pubdate_nice;
    $shows = $article->shows;    
}

//jHtmlArea js and css include
addSuplementalIncludeFile("js/jHtmlArea-0.7.0.min.js","js");
addSuplementalIncludeFile("css/jHtmlArea.css","css");


$page_name = "con_articles";
$page_title = "Editare articole";
include "include/header.php";
?>

<a href="con_articles.php?curPage=<?=$curPage?>">&laquo; Inapoi la listarea de articole</a>

<div class="spacer10"></div>

<?
if($id>0){
    ?>
<div class="form-container">
<div class="form-title">Asociere produse la articol</div>
    
    <div class="form_item">
        <label for="search_asoc">Cauta produse</label>
        <input type="text" name="search_asoc" id="search_asoc" value="" class="txt" style="margin-right: 20px;">
        <a href="javascript:void(0)" onclick="getSearchArticleProducts('search_asoc', 'article-products-search-list', <?=$id?>, 0)" class="button"><span>Cauta</span></a>
        <div class="clear"></div>
        <div id="article-products-search-list" style="padding: 5px; background-color: #f2f2f2; display: none;"></div>
    </div>    
    
    <div class="spacer10"></div>
    
    <div class="form_item">
        <div style="background-color: #A0B745; color: #fff; font-weight: bold; padding: 5px;">Produse asociate:</div>
        <div class="clear"></div>
        <div id="article-products-list" style="padding: 5px;"></div>
    </div>    
    
</div>        

<script type="text/javascript">
    getArticleProducts("article-products-list", "<?=$id?>")
</script>
    <?
}
?>


<div class="spacer10"></div>

<div class="form-container">
<div class="form-title">Editare articol</div>
<form name="edit-article" id="edit-article" method="post" action="con_articles_edit.php?action=save" onsubmit="if(!validateForm(arFields, 'error-holder')){return false}">
    <input type="hidden" name="id" value="<?=$id?>">
    <input type="hidden" name="curPage" value="<?=$curPage?>">
    
<?
if($id>0){
    ?>
    <div class="form_item">
        <label>Afisari</label>
        <?=$shows?>
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label>Data publicare</label>
        <?=$pubdate?>
        <div class="clear"></div>
    </div>        
    <div class="spacer10"></div>
    <?
}
?>

    <div class="form_item">
        <label for="category_id">Categorie</label>
        <select name="category_id" id="category_id">
            <option value="0">--alege o categorie</option>
            <?
            $cats = $db->get_results("Select * from article_categories order by category");
            if($cats!=null){
                foreach($cats as $cat){
                    ?>
                    <option <?if($cat->id==$category_id){echo 'selected="selected"';}?> value="<?=$cat->id?>"><?=$cat->category?></option>
                    <?
                }
            }
            ?>
        </select>
        <div class="clear"></div>
    </div> 

    <div class="form_item">
        <label for="title">Titlu</label>
        <input type="text" name="title" id="title" value="<?=$title?>" class="txt" style="width: 600px; " onblur="getUrlFromText('title', 'title_url')">
        <div class="clear"></div>
    </div>     
    
    <div class="form_item">
        <label for="title_url">URL Titlu</label>
        <input type="text" name="title_url" id="title_url" value="<?=$title_url?>" class="txt" style="width: 600px; " onfocus="getUrlFromText('title', 'title_url')">
        <div class="clear"></div>
    </div> 

    <div class="form_item">
        <label for="meta_desc">Meta Desc</label>
        <input type="text" name="meta_desc" id="meta_desc" value="<?=$meta_desc?>" class="txt" style="width: 600px; ">
        <div class="clear"></div>
    </div> 
    
    <div class="form_item">
        <label for="acontent" style="width: 460px;">Continut</label>
        <div class="clear"></div>
        <textarea name="acontent" id="acontent" style="width: 770px;" rows="30"><?=$acontent?></textarea>
        <div class="clear"></div>
    </div>    

    <div class="form_item">        
        <div class="spacer10"></div>
        
        <script language="javascript">
            //validation rules
            var arFields = new Array();
            var arField = new Array("Categorie", "category_id", "required-list"); arFields[1] = arField;            
            var arField = new Array("Titlu", "title", "required"); arFields[2] = arField;            
            var arField = new Array("URL Titlu", "title_url", "required"); arFields[3] = arField;            
            var arField = new Array("Meta Desc", "meta_desc", "required"); arFields[4] = arField;            
            var arField = new Array("Continut", "acontent", "required"); arFields[5] = arField;            
        </script>
        
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#edit-article').submit();}" class="button"><span>Salveaza</span></a>
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
    $("#acontent").htmlarea({
    toolbar: ["bold", "italic", "underline", "|", "h1", "h2", "h3", "|", "link", "unlink", "|", "image", "|", "html"]
    });
});

</script>


<?php
include "include/footer.php";
?>
