<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user



$page_name = "u_stats";
$page_title = "Statistici";
include "include/header.php";
?>



<div style="width: 290px; margin-right: 10px; float: left;">
    <span style="background-color: #f2f2f2; height: 24px; line-height: 24px; font-weight: bold; color: #444; padding: 5px; display: block;">Statistici categorii <i>(top 10)</i></span>
    <?
    $tops = $db->get_results("Select * from shop_categories where vcount>0 order by vcount desc limit 10");
    if($tops!=null){
        $i=0;
        foreach($tops as $top){
            $i++;
            ?>
            <div style="margin-top: 5px; padding: 3px 0px;">
                <span style="float: right;"><?=$top->vcount?> afisari</span>
                <?=$i?>. <?=$top->category?>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>
</div>

<div style="width: 290px; margin-right: 10px; float: left;">
    <span style="background-color: #f2f2f2; height: 24px; line-height: 24px; font-weight: bold; color: #444; padding: 5px; display: block;">Statistici produse - in listare <i>(top 5)</i></span>
    <?
    $tops = $db->get_results("Select * from shop_products where show_inlisting>0 order by show_inlisting desc limit 5");
    if($tops!=null){
        $i=0;
        foreach($tops as $top){
            $i++;
            ?>
            <div style="margin-top: 5px; padding: 3px 0px;">
                <span style="float: right;"><?=$top->show_inlisting?> afisari</span>
                <?=$i?>. <?=$top->title?>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>    
    
    <div class="spacer10"></div>
    <span style="background-color: #f2f2f2; height: 24px; line-height: 24px; font-weight: bold; color: #444; padding: 5px; display: block;">Statistici produse - afisare pagina <i>(top 5)</i></span>
    <?
    $tops = $db->get_results("Select * from shop_products where show_inpage>0 order by show_inpage desc limit 5");
    if($tops!=null){
        $i=0;
        foreach($tops as $top){
            $i++;
            ?>
            <div style="margin-top: 5px; padding: 3px 0px;">
                <span style="float: right;"><?=$top->show_inpage?> afisari</span>
                <?=$i?>. <?=$top->title?>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>    
    
    <div class="spacer10"></div>
    <span style="background-color: #f2f2f2; height: 24px; line-height: 24px; font-weight: bold; color: #444; padding: 5px; display: block;">Statistici produse - click aff-link <i>(top 5)</i></span>
    <?
    $tops = $db->get_results("Select * from shop_products where click>0 order by click desc limit 5");
    if($tops!=null){
        $i=0;
        foreach($tops as $top){
            $i++;
            ?>
            <div style="margin-top: 5px; padding: 3px 0px;">
                <span style="float: right;"><?=$top->click?> clickuri</span>
                <?=$i?>. <?=$top->title?>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>
        
</div>

<div style="width: 290px; margin-right: 10px; float: left;">
    <span style="background-color: #f2f2f2; height: 24px; line-height: 24px; font-weight: bold; color: #444; padding: 5px; display: block;">Statistici cautari <i>(top 10)</i></span>
    <?
    $tops = $db->get_results("Select * from shop_searches where scount>0 order by scount desc limit 10");
    if($tops!=null){
        $i=0;
        foreach($tops as $top){
            $i++;
            ?>
            <div style="margin-top: 5px; padding: 3px 0px;">
                <span style="float: right;"><?=$top->scount?> cautari</span>
                <?=$i?>. <?=$top->sterm?>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>    
</div>



<div class="clear"></div>


<?php
include "include/footer.php";
?>
