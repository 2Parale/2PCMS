<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user


if(isset($_GET["action"])){
    if($_GET["action"]=="add"){
        $keys = $_POST["keys"];
        $arKeys = explode("\n",$keys);
        foreach($arKeys as $ki){
            $ki = trim($ki,"\r");
            $db->query("INSERT INTO shop_searches (sterm, scount, active) VALUES ('$ki',1,1) ON DUPLICATE KEY UPDATE active=1");
        }
        redirTo("u_seokeys.php");
    }
}


$page_name = "u_seokeys";
$page_title = "SEO Keywords";
include "include/header.php";
?>


<div style="width: 800px;">
    
    
    <div style="width: 300px; margin-right: 10px; float: left;">
        <h3>Top 10 cautari</h3>
        
        <?
        $tcs = $db->get_results("Select * from shop_searches order by scount desc limit 10");
        if($tcs!=null){
            $i=0;
            foreach($tcs as $tc){
                $i++;
                ?>
                <div style="margin-bottom: 5px; padding: 5px 0px; background-color: #f2f2f2;">                    
                    <div style="float: left; width: 200px;"><?=$i?>. <a href="<?=$_CONFIG["urlpath"]?>cauta/<?=urlencode($tc->sterm)?>/" target="_blank"><?=$tc->sterm?><img src="images/external-link.png" width="9" height="9" alt="link extern" style="margin-left: 5px;"/></a></div>
                    <div style="float: left; width: 100px;"><?=$tc->scount?> cautari</div>
                    <div class="clear"></div>
                </div>
                <?
            }
        }
        ?>        
        
        <div class="spacer10"></div>
        
        <h3>Top 10 vizualizari</h3>
        
        <?
        $tcs = $db->get_results("Select * from shop_searches order by vcount desc limit 10");
        if($tcs!=null){
            $i=0;
            foreach($tcs as $tc){
                $i++;
                ?>
                <div style="margin-bottom: 5px; padding: 5px 0px; background-color: #f2f2f2;">                    
                    <div style="float: left; width: 200px;"><?=$i?>. <a href="<?=$_CONFIG["urlpath"]?>cauta/<?=urlencode($tc->sterm)?>/" target="_blank"><?=$tc->sterm?><img src="images/external-link.png" width="9" height="9" alt="link extern" style="margin-left: 5px;"/></a></div>
                    <div style="float: left; width: 100px;"><?=$tc->vcount?> views</div>
                    <div class="clear"></div>
                </div>
                <?
            }
        }
        ?>         
        
    </div>
    
    <div style="width: 490px; float: left;">
        <h3>Adauga cuvinte cheie</h3>
        <i>cate o expresie pe rand</i>
        <div class="spacer5"></div>
        <form name="addk" id="addk" method="post" action="u_seokeys.php?action=add">
            <textarea name="keys" style="width: 450px;" rows="6"></textarea>
            <br />
            <a href="javascript:void(0)" onclick="$('#addk').submit();" class="button"><span>Adauga</span></a>
            <div class="clear"></div>
        </form>
        
        <div class="spacer10"></div>
        <hr/>
        <div class="spacer10"></div>
        
        <a href="javascript:void(0)" onclick="getWaitKeywords('wait-list')">Vezi cautari neaprobate &raquo;</a>
        <div id="wait-list" style="display: none;">
        </div>
        <div class="spacer10"></div>        
        
    </div>
    
    <div class="clear"></div>

</div>

<?php
include "include/footer.php";
?>
