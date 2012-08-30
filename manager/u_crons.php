<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user


if(isset($_GET["action"])){
    
    if($_GET["action"]=="update"){
        
        $total = (int)$_POST["total"];
        for($i=1; $i<=$total; $i++){
            $id = (int)$_POST["id-".$i];
            $cron_sync = (int)$_POST["cron_sync-".$i];
            $db->query("Update aff_partners set cron_sync=$cron_sync where id=$id");
        }
        
        
        
        $_SESSION["response_msg"] = "Modificarile au fost salvate!";
        redirTo("u_crons.php");
    }
    
}


$page_name = "u_crons";
$page_title = "Cron-uri";
include "include/header.php";
?>

<form name="updatecron" id="updatecron-form" method="post" action="u_crons.php?action=update">

<div style="width: 500px;">
<h3>Setari cron - import feed-uri</h3>
<div style="margin-bottom: 20px;"><i>alege partenerii ale caror feed care vor fi incluse in sincronizarea prin cron</i></div>

<?
$ps = $db->get_results("Select * from aff_partners order by id");
if($ps!=null){
    $i=0;
    foreach($ps as $p){
        $i++;
        ?>
        <input type="hidden" name="id-<?=$i?>" value="<?=$p->id?>">
        <div style="padding: 5px; margin-bottom: 5px; border-bottom: 1px solid #444;">            
            <select name="cron_sync-<?=$i?>" style="float: right;">
                <option <?if($p->cron_sync==0){echo 'selected="selected"';}?> value="0">NU</option>
                <option <?if($p->cron_sync==1){echo 'selected="selected"';}?> value="1">DA</option>
            </select>
            <span style="float: left;"><b><?=$p->shop?></b></span>
            <div class="clear"></div>
        </div>
        <?
    }
}
?>

<input type="hidden" name="total" value="<?=$i?>">
<a href="javascript:void(0)" onclick="$('#updatecron-form').submit();" class="button"><span>Salveaza modificarile</span></a>

</div>

</form> 

<div class="clear"></div>

<hr/>

<h2>Cele mai recente loguri de activitate generate de cron</h2>


<?
$clogs = $db->get_results("Select * from cron_logs order by id desc limit 50");
if($clogs!=null){
    foreach($clogs as $clog){
        ?>
        <div style="margin-bottom: 10px; border: 1px solid #8c8c8c; padding: 5px;">
            <div style="width: 200px; float: left; background-color: #f2f2f2; padding: 3px;"><?=$clog->date_log?></div>
            <div style="width: 750px; float: left; margin-left: 10px; padding: 3px; background-color: #ffffbe;"><?=$clog->action_details?></div>
            <div class="clear"></div>
        </div>
        <?
    }
}
?>

<?php
include "include/footer.php";
?>
