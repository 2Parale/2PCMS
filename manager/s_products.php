<?php
include "../common/common.php";
set_time_limit(7200);

if(!userIsOk()){redirTo("login.php");}//checking user


if(isset($_GET["action"])){
    
    //stergere produse inactive
    if($_GET["action"]=="delete_inactive"){
        
        $prods = $db->get_results("Select * from shop_products where active=0");
        if($prods!=null){
            foreach($prods as $prod){
                if($prod->local_img_small!="default-image.jpg"){
                    @unlink("../product_images/".$prod->local_img_small);
                    @unlink("../product_images/".$prod->local_img_big);                
                }
            }
            
            $db->query("Delete from shop_products where active=0");
            
            $_SESSION["response_msg"] = "Produsele inactive au fost sterse!";
        }
        
        redirTo("s_products.php");
    }
    
    //stergere produs
    if($_GET["action"]=="delete_product"){
        $id = (int)$_GET["id"];
        $curPage = (int)$_GET["curPage"];
        
        if($id>0){
            $prod = $db->get_row("Select * from shop_products where id=$id");
            if($prod->local_img_small!="default-image.jpg"){
                @unlink("../product_images/".$prod->local_img_small);
                @unlink("../product_images/".$prod->local_img_big);
            }
            
            $db->query("Delete from shop_products where id=$id");
            
            $_SESSION["response_msg"] = "Produsul a fost sters!";            
        }
        
        if(isset($_GET["return_action"])){
            redirTo("s_products.php?action=search&curPage=".$curPage."&search_partner=".$_GET["search_partner"]."&search_category=".$_GET["search_category"]."&search_prod=".$_GET["search_prod"]);        
        }else{
            redirTo("s_products.php?curPage=".$curPage);        
        }                
    }    
    
    //stergere produs
    if($_GET["action"]=="delete_all"){
        
        $prods = $db->get_results("Select * from shop_products");
        if($prods!=null){
            foreach($prods as $prod){
                if($prod->local_img_small!="default-image.jpg"){
                    @unlink("../product_images/".$prod->local_img_small);
                    @unlink("../product_images/".$prod->local_img_big);                
                }
            }
            
            $db->query("Delete from shop_products");
            
            $_SESSION["response_msg"] = "Toate produsele au fost sterse!";
        }        
        
        redirTo("s_products.php");        
    }        
    
}


//datagrid include
include ("include/class.datagrid.php");
$myDG = new DataGrid($db);
parseClassDep2IncludeFile ("include/",$myDG->getDependencies());
$curPage = 1;
if(isset($_GET["curPage"])){$curPage = (int)$_GET["curPage"];if($curPage==0){$curPage = 1;}}


$page_name = "s_products";
$page_title = "Produse magazin";
include "include/header.php";
?>


<div class="spacer10"></div>
<div style="border: 1px solid #8c8c8c; padding: 5px;">
    <div style="width: 350px; float: left;">        
    <b>Imagini de procesat</b>: <?php 
    echo (int)$db->get_var("Select count(*) from shop_products where local_img_small=''");
    ?> - <a href="javascript:void(0)" onclick="$('#img_process_holder').fadeIn('normal')">Proceseaza</a>
    <div class="clear"></div>
    </div>
    
    <div style="width: 350px; float: left; margin-left: 10px;">    
    <b>Produse inactive</b>: <?php 
    echo (int)$db->get_var("Select count(*) from shop_products where active=0");
    ?> - <a href="javascript:void(0)" onclick="ConfirmDelete2Link('s_products.php?action=delete_inactive')">Sterge</a>
    <div class="clear"></div>
    </div>    

    <div style="width: 200px; float: left; margin-left: 10px;">    
    <a href="javascript:void(0)" onclick="ConfirmDelete2Link('s_products.php?action=delete_all')">Sterge toate produsele</a>
    <div class="clear"></div>
    </div>    
    
    <div class="clear"></div>
</div>
<div class="spacer10"></div>


<div id="img_process_holder" style="display: none;">
    <div>
        Proceseaza <input type="text" id="iCount" style="width: 30px;" value="10"> imagini - <a href="javascript:void(0)" onclick="startImgProcessing($('#iCount').val())">GO</a> | 
        Proceseaza toate imaginile - <a href="javascript:void(0)" onclick="startImgProcessing(-1)">GO</a> | 
        <a href="javascript:void(0)" onclick="$('#img_process_holder').hide()">renunta</a> 
    </div>
    
    
    <iframe src="" width="0" height="0" id="img_status" style="display: none;" name="img_status" frameborder="0" scrolling="no"></iframe>
    <div id="img-processing-status" style="margin-top: 10px;"></div>
    <div id="img-processing-progressbar" style="display: none; width: 300px; background-color: #f2f2f2; border: 1px solid #8c8c8c; height: 10px;"><div id="img-processing-progressbar-done" style="height:10px; width: 0px; background-color: #444;"></div></div>
    <script type="text/javascript">var total_images = 0;</script>
    
    
    <hr/>
    <div class="spacer10"></div>
    <div class="spacer10"></div>
</div>

<div class="spacer10"></div>

<form name="search_prods" id="search_prods" method="get" action="s_products.php">
<input type="hidden" name="action" value="search">

<div style="border: 1px solid #8c8c8c; padding: 5px;">
    <?
    if(isset($_GET["action"])){
        ?>
        <a href="s_products.php" style="float: right;">elimina filtrarea</a>
        <?
    }
    ?>
    <span style="font-weight: bold; float: left; margin-right: 10px; line-height: 24px;">Filtreaza produsele:</span> 
    <input type="text" name="search_prod" id="search_prod" value="<? if(isset($_GET["action"])){echo $_GET["search_prod"];} ?>" style="width: 60px; float: left; margin-right: 10px;" onfocus="$('#search_prod').animate({width: '200'}, 150)" onblur="$('#search_prod').animate({width: '60'}, 150)">
    <select name="search_partner" id="search_partner" style="float: left; margin-right: 10px; width: 150px;">
        <option value="0">Toti partenerii</option>
        <?
        $parts = $db->get_results("Select id, shop from aff_partners order by shop asc");
        if($parts!=null){
            foreach($parts as $part){
                ?>
                <option <?if(isset($_GET["action"])){if((int)$_GET["search_partner"]==$part->id){echo 'selected="selected"';}}?> value="<?=$part->id?>"><?=$part->shop?></option>
                <?
            }
        }
        ?>
    </select>
    <select name="search_category" id="search_category" style="float: left; margin-right: 10px; width: 150px;">
        <option value="0">Toate categoriile</option>
        <?
        $cats = $db->get_results("Select id, category from shop_categories order by category asc");
        if($cats!=null){
            foreach($cats as $cat){
                ?>
                <option <?if(isset($_GET["action"])){if((int)$_GET["search_category"]==$cat->id){echo 'selected="selected"';}}?> value="<?=$cat->id?>"><?=$cat->category?></option>
                <?
            }
        }
        ?>        
    </select>    
    <a href="javascript:void(0)" onclick="$('#search_prods').submit()" class="button"><span>Cauta</span></a>
    <div class="clear"></div>             
</div>
<input type="submit" style="width: 0px; height: 0px; border: 0px; padding: 0px;">
</form>

<div class="spacer10"></div>

<form name="show_filters" id="show_filters" method="get" action="s_products.php">
	<?php
	// mentine in url parametri care exista deja
	parse_str($_SERVER['QUERY_STRING'], $query_string);
	if( !empty($query_string) ) {
		foreach( $query_string as $keyv => $param ) {
			echo '<input type="hidden" name="'.$keyv.'" value="'.$param.'" />';
		}
	}
	?>
	
	<div style="border: 1px solid #8c8c8c; padding: 5px;">
		<span style="font-weight: bold; float: left; margin-right: 10px; line-height: 24px;">Adauga filtre site:</span> 
		<select name="search_filter_group" id="search_filter_group" style="float: left; margin-right: 10px; width: 130px;">
			<option value="0">Toate grupurile de filtre</option>
			<?
			$gpfs = $db->get_results("Select id, group_name from shop_filter_groups order by group_name asc");
			if($cats!=null){
				foreach($gpfs as $gpf){
					?>
					<option <? if((int)$_GET["search_filter_group"]==$gpf->id){echo 'selected="selected"';} ?> value="<?=$gpf->id?>"><?=$gpf->group_name?></option>
					<?
				}
			}
			?>        
		</select>
		<?php if( isset($_GET['search_filter_group']) && (int) $_GET['search_filter_group'] > 0 ) { ?>
		<select name="search_filter" id="search_filter" style="float: left; margin-right: 10px; width: 130px;">
			<option value="0">Toate filtrele</option>
			<?
			$sel_q = "SELECT id, filter_name FROM shop_filters WHERE filter_group_id = ".(int) $_GET['search_filter_group']." ORDER BY filter_name ASC;";
			$gfs = $db->get_results($sel_q);
			if($cats!=null){
				foreach($gfs as $gf){
					?>
					<option <? if((int)$_GET["search_filter"]==$gf->id){echo 'selected="selected"';} ?> value="<?=$gf->id?>"><?=$gf->filter_name?></option>
					<?
				}
			}
			?>        
		</select>
		<?php } ?>
		
		<a href="javascript:void(0)" onclick="$('#show_filters').submit()" class="button"><span>Adauga</span></a>
		<div class="clear"></div>             
	</div>
	<input type="submit" style="width: 0px; height: 0px; border: 0px; padding: 0px;">
</form>

<div class="spacer10"></div>


<?php
if(isset($_GET["action"]) && $_GET["action"]=="search"){
    //searching
    $sql = "Select distinct a.*, b.category, c.shop, c.shop_url, xp.shop_filter_id from shop_products a 
    left join shop_categories b on a.category_id=b.id 
    left join aff_partners c on a.partner_id=c.id 
	left join shop_filters_x_products xp on a.id = xp.shop_product_id AND xp.shop_filter_id = ".(int) $_GET['search_filter']."
    where a.id>0 
    ";
    $sql_count = "Select count(*) from shop_products a where a.id>0 ";    
    
    $search_prod = mysql_real_escape_string($_GET["search_prod"]);
    $search_partner = (int)$_GET["search_partner"];
    $search_category = (int)$_GET["search_category"];
    
    if($search_category>0){
        $sql .= " and a.category_id=".$search_category;
        $sql_count .= " and a.category_id=".$search_category;
    }

    if($search_partner>0){
        $sql .= " and a.partner_id=".$search_partner;
        $sql_count .= " and a.partner_id=".$search_partner;
    }
    
    if($search_prod!=""){
        $sql .= " and a.title like '%$search_prod%'";
        $sql_count .= " and a.title like '%$search_prod%'";
    }        
    
    $sql .= " order by a.id";
    $self_link = "s_products.php?action=search&search_category=$search_category&search_partner=$search_partner&search_prod=".urlencode($_GET["search_prod"]);
    $delete_link = "s_products.php?action=delete_product&return_action=search&search_category=$search_category&search_partner=$search_partner&search_prod=".urlencode($_GET["search_prod"]);
    $search_extra_url = "search_category=$search_category&search_partner=$search_partner&search_prod=".urlencode($_GET["search_prod"]);
	if( isset($_GET['search_filter_group']) ) {
		$search_extra_url .= "&search_filter_group=".(int) $_GET['search_filter_group'];
		$self_link        .= "&search_filter_group=".(int) $_GET['search_filter_group'];
		$delete_link      .= "&search_filter_group=".(int) $_GET['search_filter_group'];
	}
	if( isset($_GET['search_filter']) ) {
		$search_extra_url .= "&search_filter=".(int) $_GET['search_filter'];
		$self_link        .= "&search_filter=".(int) $_GET['search_filter'];
		$delete_link      .= "&search_filter=".(int) $_GET['search_filter'];
	}
}else{
    //unfiltered listing
    $sql = "Select distinct a.*, b.category, c.shop, c.shop_url, xp.shop_filter_id from shop_products a 
    left join shop_categories b on a.category_id=b.id 
    left join aff_partners c on a.partner_id=c.id 
	left join shop_filters_x_products xp on a.id = xp.shop_product_id AND xp.shop_filter_id = ".(int) $_GET['search_filter']."
    order by a.id";
    $sql_count = "Select count(*) from shop_products";    
    $self_link = "s_products.php?";
    $delete_link = "s_products.php?action=delete_product";
    $search_extra_url = "";
	if( isset($_GET['search_filter_group']) ) {
		$search_extra_url .= "search_filter_group=".(int) $_GET['search_filter_group'];
		$self_link        .= "search_filter_group=".(int) $_GET['search_filter_group'];
		$delete_link      .= "&search_filter_group=".(int) $_GET['search_filter_group'];
	}
	if( isset($_GET['search_filter']) ) {
		$search_extra_url .= ($search_extra_url == "" ? '' : '&') . "search_filter=".(int) $_GET['search_filter'];
		$self_link        .= ($search_extra_url == "s_products.php?" ? '' : '&') . "&search_filter=".(int) $_GET['search_filter'];
		$delete_link      .= "&search_filter=".(int) $_GET['search_filter'];
	}
}

$myDG->setSqlString ($sql);
$myDG->setSqlCountString ($sql_count);    


$myDG->setIdField("id");
$myDG->setSelfLink($self_link);
$myDG->setCurrentPage($curPage);
$myDG->setRecPerPage(30);

//adding columns
$myDG->addColumn ('#', '', 'counter', '30px');
$myDG->addColumn ('&nbsp;', 'if("|#local_img_small#|"==""){echo "&nbsp;";}else{echo "<img src=\'../product_images/|#local_img_small#|\' width=\'45\' height=\'45\'>";}', 'field_eval', '100px', 'center');
$myDG->addColumn ('Afiliat', '#<b>#|shop|#</b> <i>(<a href="#|shop_url|#" target="_blank">#|shop_url|#</a>)</i>#', 'field', '200px', 'left');
$myDG->addColumn ('Categorie magazin', 'category', 'field', '200px', 'left');
$myDG->addColumn ('Produs', 'title', 'field', '250px', 'left');
$myDG->addColumn ('Pret', 'price_int|# Lei#', 'field', '100px', 'left');
$myDG->addColumn ('URL Aff', '#<a href="#|aff_url|#" target="_blank">click</a>#', 'field', '50px', 'center');
$myDG->addColumn ('URL Img', '#<a href="#|img_url|#" target="_blank">click</a>#', 'field', '50px', 'center');
if( isset($_GET['search_filter']) && (int) $_GET['search_filter'] > 0 ) {
	
	$myDG->addColumn ('Filtru', '$shop_filter_id = (int) "|#shop_filter_id#|"; if($shop_filter_id != "" && $shop_filter_id == '.(int) $_GET['search_filter'].') { echo "<input type=\'checkbox\' value=\'1\' checked=\'checked\' id=\'filter_product_|#id#|_'.(int) $_GET['search_filter'].'\' />"; } else { echo "<input type=\'checkbox\' value=\'1\' id=\'filter_product_|#id#|_'.(int) $_GET['search_filter'].'\' />"; }', 'field_eval', '50px', 'center');
}

$myDG->addColumn ('Optiuni', '', 'options');


//adding options
$myDG->addOption ('editeaza', 'images/edit.gif', 'editPopup2(\'popup_product_edit.php?'.$search_extra_url.'#replace#\')', 'js');
$myDG->addOption ('sterge', 'images/delete.gif', 'ConfirmDelete2Link(\''.$delete_link.'#replace#\');', 'js');


//draw
$myDG->Draw();
?>


<script type="text/javascript">
$(document).ready(function() {
	$('input[id^="filter_product_"]').click(function() {
		var exp = $(this).attr('id').split('filter_product_');
		var ids = exp[1].split('_');
		//console.log(ids);
		var product_id = ids[0];
		var filter_id  = ids[1];
		var action_type = $(this).is(":checked") ? 'add' : 'delete';

		$.ajax({
			url: "ajax_proxy.php",
			data: {
				action: "add_product_filter",
				product_id: product_id,
				filter_id: filter_id,
				type: action_type
			},
			beforeSend: function() {
				$('input[id^="filter_product_"]').attr("disabled", "disabled");
			},
			success: function(data) {
				$('input[id^="filter_product_"]').removeAttr("disabled");
			}
		});		
	});
});
</script>


<?php
include "include/footer.php";
?>
