<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="add"){
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        
        if($name!="" && $email!="" && $password!=""){
            $db->query("Insert into users (name, email, password, last_date, last_ip) values ('$name', '$email', '$password', NOW(), '127.0.0.1')");
        }
        
        $_SESSION["response_msg"] = "Un nou utilizator a fost adaugat!";
        
        redirTo("u_users.php");
    }
    
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        //can not delete self
        if($id!=$_SESSION["admin_id"]){
            $db->query("Delete from users where id=$id");
        }
        
        $_SESSION["response_msg"] = "Utilizatorul a fost sters!";
        
        redirTo("u_users.php");
    }    
    
}


//datagrid include
include ("include/class.datagrid.php");
$myDG = new DataGrid($db);
parseClassDep2IncludeFile ("include/",$myDG->getDependencies());
$curPage = 1;
if(isset($_GET["curPage"])){$curPage = (int)$_GET["curPage"];if($curPage==0){$curPage = 1;}}


$page_name = "u_users";
$page_title = "Utilizatori";
include "include/header.php";
?>

<a href="javascript:void(0)" onclick="$('#addnew').fadeIn('normal')">Adauga un nou utilizator &raquo;</a>
<div class="spacer5"></div>

<div id="addnew" style="display: none;" class="form-container">
<form name="addnew" id="addnew-form" method="post" action="u_users.php?action=add">
    <div class="form_item">
        <label for="name">Nume</label>
        <input type="text" name="name" id="name" value="" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="email">Email (pt login)</label>
        <input type="text" name="email" id="email" value="" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="password">Parola</label>
        <input type="text" name="password" id="password" value="" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">        
        <div class="spacer10"></div>
        <label>&nbsp;</label>
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addnew-form').submit();}" class="button"><span>Adauga</span></a>
        <a href="javascript:void(0)" onclick="$('#addnew').fadeOut('fast'); $('#error-holder').hide();" class="button"><span class="normal">Renunta</span></a>
        <div class="clear"></div>
    </div>
</form>

    <script language="javascript">
        //validation rules
        var arFields = new Array();
        var arField = new Array("Nume", "name", "required"); arFields[1] = arField;            
        var arField = new Array("Email", "email", "required"); arFields[2] = arField;            
        var arField = new Array("Parola", "password", "required"); arFields[3] = arField;                    
    </script> 

    <div class="form_item">        
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>

</div>

<hr/>

<div class="spacer10"></div>


<?php
$myDG->setSqlString ("Select * from users order by name");
$myDG->setSqlCountString ("Select count(*) from users");

$myDG->setIdField("id");
$myDG->setSelfLink("u_users.php?");
$myDG->setCurrentPage($curPage);
$myDG->setRecPerPage(10);

//adding columns
$myDG->addColumn ('#', '', 'counter', '30px');
$myDG->addColumn ('Name', 'name', 'field', '300px', 'left');
$myDG->addColumn ('Email', 'email', 'field', '300px', 'left');
$myDG->addColumn ('Last login - date', 'last_date|# #', 'field', '150px', 'center');
$myDG->addColumn ('Last login - ip', 'last_ip|# #', 'field', '150px', 'center');

$myDG->addColumn ('Optiuni', '', 'options');


//adding options
$myDG->addOption ('sterge', 'images/delete.gif', 'ConfirmDelete2Link(\'u_users.php?action=delete#replace#\');', 'js');


//draw
$myDG->Draw();
?>


<?php
include "include/footer.php";
?>
