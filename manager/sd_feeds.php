<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){
    
    if($_GET["action"]=="delete"){
        $fid = (int)$_GET["id"];
        $pid = (int)$_GET["pid"];
        
        $recs = $db->get_results("Select * from aff_feeds where id=$fid");
        if($recs!=null){
            foreach($recs as $rec){
                @unlink("../feeds/".$rec->feed_filename);
            }
        }
        
        $db->query("Delete from aff_feeds where id=$fid");
        $db->query("Delete from aff_feed_source where feed_id=$fid");                
        $_SESSION["response_msg"] = "Feed-ul si informatiile asociate au fost sterse! Produsele adaugate din feed nu au fost sterse.";                
        
        redirTo("sd_feeds.php?pid=$pid");
    }
    
    if($_GET["action"]=="addnew"){
        $pid = $_GET["pid"];
        $partner_id = $_POST["partner_id"];
        $feed_url = $_POST["feed_url"];
        $feed_desc = $_POST["feed_desc"];
        $price_format = $_POST["price_format"];
        
        $db->query("Insert into aff_feeds (partner_id, feed_url, feed_desc, price_format) values ($partner_id, '$feed_url', '$feed_desc', '$price_format')");
        
        $_SESSION["response_msg"] = "Un nou feed a fost adaugat!";
        
        redirTo("sd_feeds.php?pid=$pid");
    }
    
}


$pid = 0; // partner id
if(isset($_GET["pid"])){$pid = (int)$_GET["pid"];}


//datagrid include
include ("include/class.datagrid.php");
$myDG = new DataGrid($db);
parseClassDep2IncludeFile ("include/",$myDG->getDependencies());
$curPage = 1;
if(isset($_GET["curPage"])){$curPage = (int)$_GET["curPage"];if($curPage==0){$curPage = 1;}}


$page_name = "sd_feeds";
$page_title = "Feed-uri parteneri";
include "include/header.php";
?>
<a href="javascript:void(0)" onclick="$('#addnew').fadeIn('normal')">Adauga un nou feed &raquo;</a>
<div class="spacer5"></div>

<div id="addnew" style="display: none;" class="form-container">
<div class="form-title">Adauga un feed</div>
<form name="addnew" id="addnew-form" method="post" action="sd_feeds.php?action=addnew&pid=<?php echo $pid; ?>" onsubmit="if(!validateForm(arFields, 'error-holder')){return false}">
    <div class="form_item">
        <label for="partner_id">Partener</label>
        <select name="partner_id" id="partner_id">
            <?php
            $ps = $db->get_results("Select * from aff_partners order by shop asc");
            if($ps!=null){
                foreach ($ps as $p){
                    ?>
                    <option value="<?php echo $p->id; ?>"><?php echo $p->shop; ?></option>
                    <?php
                }
            }
            ?>
        </select>        
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="feed_url">URL Feed</label>
        <input type="text" name="feed_url" id="feed_url" value="" class="txt">
        <div class="clear"></div>
    </div>
    <div class="form_item">
        <label for="feed_desc">Descriere</label>
        <input type="text" name="feed_desc" id="feed_desc" value="" class="txt">
        <div class="clear"></div>
    </div>                
    <div class="form_item">
        <label for="price_format">Format preturi</label>
        <select name="price_format">
            <option value="pricedot">punct pentru zecimale si virgula pentru mii</option>
            <option value="pricecomma">virgula pentru zecimale si punct pentru mii</option>
        </select>
        <div class="clear"></div>
    </div>                    
    <div class="form_item">
        <div class="spacer10"></div>
        <label>&nbsp;</label>
        
        <script language="javascript">
            //validation rules
            var arFields = new Array();
            var arField = new Array("URL Feed", "feed_url", "required"); arFields[1] = arField;            
            var arField = new Array("Descriere", "feed_desc", "required"); arFields[2] = arField;            
        </script>        
        
        <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addnew-form').submit();}" class="button"><span>Adauga</span></a>
        <a href="javascript:void(0)" onclick="$('#addnew').fadeOut('fast');$('#error-holder').hide();" class="button"><span class="normal">Renunta</span></a>
        <input type="submit" name="cmdSubmit" value="" style="width: 0px; height: 0px; border: 0px; margin: 0px; padding: 0px;">
        <div class="clear"></div>
        <div class="spacer10"></div>
    </div>    
    <div id="feed-help" style="margin-top: 10px; border-top: 2px solid #f2f2f2; padding: 5px; background-color: #ffc;">
        <img src="images/xml-feed-desc.jpg" alt="detalii feed XML" width="960" height="300"/>
    </div>
    
    <div class="form_item">        
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>    
    
</form>
</div>

<hr/>

<div class="spacer10"></div>

<h2>Listare parneteri</h3>

<div>
<form name="filter_feeds" id="filter_feeds" method="get" action="sd_feeds.php">
<span style="float: left; padding: 5px;">Filtreaza dupa partener </span>
<select name="pid" style="float: left;">
    <option value="0">--toti</option>
    <?php
    $partners = $db->get_results("Select * from aff_partners order by shop asc");
    if($partners!=null){
        foreach($partners as $partner){
            ?>
            <option <?php if($pid==$partner->id){echo 'selected="selected"';} ?> value="<?php echo $partner->id; ?>"><?php echo $partner->shop; ?></option>
            <?php
        }
    }
    ?>
</select>
<a href="javascript:void(0)" onclick="$('#filter_feeds').submit();" class="button"><span>&raquo;</span></a>
</form>

<div class="clear"></div>
</div>

<div class="spacer10"></div>


<?php
$myDG->setSqlString ("Select a.*, b.shop from aff_feeds a left join aff_partners b on a.partner_id=b.id order by a.partner_id");
$myDG->setSqlCountString ("Select count(*) from aff_feeds");

if($pid>0){
    $myDG->setSqlString ("Select a.*, b.shop from aff_feeds a left join aff_partners b on a.partner_id=b.id where a.partner_id=$pid");
    $myDG->setSqlCountString ("Select count(*) from aff_feeds where partner_id=$pid");    
}

$myDG->setIdField("id");
$myDG->setSelfLink("sd_feeds.php?");
$myDG->setCurrentPage($curPage);
$myDG->setRecPerPage(20);

//adding columns
$myDG->addColumn ('#', '', 'counter', '30px');
$myDG->addColumn ('Shop', 'shop', 'field', '150px', 'left');
$myDG->addColumn ('Feed URL', '#<a href="#|feed_url|#" target="_blank">#|feed_url|#</a>#', 'field', '300px', 'left');
$myDG->addColumn ('Feed Filename', 'feed_filename|#<br /><a href="id_import.php?pid=#|partner_id|#">importa feed</a>#', 'field', '150px', 'left');
$myDG->addColumn ('Feed Desc', 'feed_desc', 'field', '200px', 'left');
$myDG->addColumn ('Last Update', 'last_date', 'field', '150px', 'center');

$myDG->addColumn ('Optiuni', '', 'options');


//adding options
#$myDG->addOption ('editeaza', 'images/edit.gif', 'sd_feeds.php?action=edit#replace#', 'html');
$myDG->addOption ('editeaza', 'images/edit.gif', 'editPopup(\'popup_edit.php?what=feed#replace#\');', 'js');
$myDG->addOption ('sterge', 'images/delete.gif', 'ConfirmDelete2Link(\'sd_feeds.php?action=delete&pid='.$pid.'#replace#\');', 'js');


//draw
$myDG->Draw();
?>



<?php
include "include/footer.php";
?>
