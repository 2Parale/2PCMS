<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        $db->query("Delete from articles where id=$id");
        $db->query("Update article_categories a set a.article_count=(Select count(id) from articles b where b.category_id=a.id)");
        
        $_SESSION["response_msg"] = "Articolul a fost sters!";
        redirTo("con_articles.php");
    }
    
}

//datagrid include
include ("include/class.datagrid.php");
$myDG = new DataGrid($db);
parseClassDep2IncludeFile ("include/",$myDG->getDependencies());
$curPage = 1;
if(isset($_GET["curPage"])){$curPage = (int)$_GET["curPage"];if($curPage==0){$curPage = 1;}}


$page_name = "con_articles";
$page_title = "Articole";
include "include/header.php";
?>

<a href="con_articles_edit.php">Adauga un articol &raquo;</a>

<div class="spacer10"></div>

<h3>Articole existente</h3>
    
<?php
//unfiltered listing
$sql = "Select a.*, b.category, DATE_FORMAT(a.pubdate, '%d %M %Y %H:%i:%s') as pubdate_nice from articles a 
left join article_categories b on a.category_id=b.id 
order by b.category asc, a.title asc";
$sql_count = "Select count(*) from articles";    


$myDG->setSqlString ($sql);
$myDG->setSqlCountString ($sql_count);    


$myDG->setIdField("id");
$myDG->setSelfLink("con_articles.php?");
$myDG->setCurrentPage($curPage);
$myDG->setRecPerPage(20);

//adding columns
$myDG->addColumn ('#', '', 'counter', '30px');
$myDG->addColumn ('Categorie', 'category', 'field', '200px', 'left');
$myDG->addColumn ('Titlu', 'title', 'field', '300px', 'left');
$myDG->addColumn ('Afisari', 'shows', 'field', '100px', 'center');
$myDG->addColumn ('Data', 'pubdate_nice', 'field', '100px', 'center');

$myDG->addColumn ('Optiuni', '', 'options');


//adding options
$myDG->addOption ('editeaza', 'images/edit.gif', 'con_articles_edit.php?#replace#', 'html');
$myDG->addOption ('sterge', 'images/delete.gif', 'ConfirmDelete2Link(\'con_articles.php?action=delete#replace#\');', 'js');


//draw
$myDG->Draw();
?>




<?php
include "include/footer.php";
?>
