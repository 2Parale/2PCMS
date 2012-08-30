<?php
set_time_limit(3600);
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user


function get_url_contents($url){    
    $opts = array( 
        'http' => array( 
            'method'=>"GET", 
            'header'=>"Content-Type: text/html; charset=utf-8" 
        ) 
    ); 

    $context = stream_context_create($opts); 
    $result = @file_get_contents($url,false,$context); 
    return $result;     
    
    /* CURL VERSION
    $crl = curl_init();
    $timeout = 5;
    curl_setopt ($crl, CURLOPT_URL,$url);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $ret = curl_exec($crl);
    curl_close($crl);
    return $ret;
    */
}

if(isset($_GET["action"])){
    
    //adaugare
    if($_GET["action"]=="addnew"){
        $p_name = $_POST["p_name"];
        $p_email = $_POST["p_email"];
        $p_website = $_POST["p_website"];
        $p_obs = $_POST["p_obs"];
        $p_link_caption = $_POST["p_link_caption"];
        $p_link_title = $_POST["p_link_title"];
        $p_link_href = $_POST["p_link_href"];
        $my_link = $_POST["my_link"];
        $p_checkpage = $_POST["p_checkpage"];
        $active = $_POST["active"];
        
        $db->query("Insert into le_partners 
        (p_name, p_email, p_website, p_obs, p_link_caption, p_link_title, p_link_href, my_link, p_checkpage, active, last_status) 
        values 
        ('$p_name', '$p_email', '$p_website', '$p_obs', '$p_link_caption', '$p_link_title', '$p_link_href', '$my_link', '$p_checkpage', '$active', '-1')");
        
        $_SESSION["response_msg"] = "Un nou partener de link exchange a fost adaugat!";
        
        redirTo('u_linkex.php');
    }
    
    //verificare 1
    if($_GET["action"]=="check"){        
        $id = (int)$_GET["id"];
        $le = $db->get_row("Select * from le_partners where id=$id");
        
        $webc = get_url_contents($le->p_checkpage);
        
        if($webc!=""){
            $strfound = strpos($webc, $le->my_link);
            
            if($strfound === false){
                //nu am gasit linkul
                $db->query("Update le_partners set last_date=NOW(), last_status=0 where id=$id");
                $db->query("INSERT into le_logs (le_id, date_log, status_log) values ($id, NOW(), 0)");
            }else{
                //am gasit linkul
                $db->query("Update le_partners set last_date=NOW(), last_status=1 where id=$id");
                $db->query("INSERT into le_logs (le_id, date_log, status_log) values ($id, NOW(), 1)");            
            }
        }
        
        $_SESSION["response_msg"] = "Partenerul de link exchange a fost verificat!";
        
        redirTo('u_linkex.php');
    }
    
    if($_GET["action"]=="delete"){
        $id = (int)$_GET["id"];
        
        @$db->query("Delete from le_partners where id=$id");
        
        $_SESSION["response_msg"] = "Un partener de link exchange a fost sters!";
        
        redirTo('u_linkex.php');
    }    
    
    //verificare all
    if($_GET["action"]=="checkall"){
        
        $les = $db->get_results("Select * from le_partners order by id asc");
        if($les!=null){
            foreach($les as $le){

                $webc = get_url_contents($le->p_checkpage);
                $id = $le->id;
                
                if($webc!=""){
                    $strfound = strpos($webc, $le->my_link);
                    
                    if($strfound === false){
                        //nu am gasit linkul
                        $db->query("Update le_partners set last_date=NOW(), last_status=0 where id=$id");
                        $db->query("INSERT into le_logs (le_id, date_log, status_log) values ($id, NOW(), 0)");
                    }else{
                        //am gasit linkul
                        $db->query("Update le_partners set last_date=NOW(), last_status=1 where id=$id");
                        $db->query("INSERT into le_logs (le_id, date_log, status_log) values ($id, NOW(), 1)");            
                    }
                }
                
            }//end foreach
        }
        
        $_SESSION["response_msg"] = "Toti partenerii de link exchange au fost verificati!";
        
        redirTo('u_linkex.php');
    }        

}


$page_name = "u_linkex";
$page_title = "Link exchange";
include "include/header.php";
?>


<a href="javascript:void(0)" onclick="$('#add-lep').toggle();">Adauga un partener de link exchange &raquo;</a>
<div id="add-lep" style="display: none;" class="form-container">
    <form name="addlep" id="addlep" method="post" action="u_linkex.php?action=addnew">
        <h3>Partener</h3>
        <div class="form_item">
            <label for="p_name">Nume</label>
            <input type="text" name="p_name" id="p_name" value="" class="txt">            
            <div class="clear"></div>
        </div>    
        
        <div class="form_item">
            <label for="p_email">Email</label>
            <input type="text" name="p_email" id="p_email" value="" class="txt">            
            <div class="clear"></div>
        </div>    
        
        <div class="form_item">
            <label for="p_website">Website</label>
            <input type="text" name="p_website" id="p_website" value="" class="txt">            
            <div class="clear"></div>
        </div>                    
        
        <div class="form_item">
            <label for="p_obs">Observatii</label>
            <textarea name="p_obs" id="p_obs" class="txt" rows="3"></textarea>
            <div class="clear"></div>
        </div>            
        
        <div class="spacer10"></div>
        
        <h3>Linkul de afisat</h3>
        <div class="form_item">
            <label for="p_link_caption">Caption</label>
            <input type="text" name="p_link_caption" id="p_link_caption" value="" class="txt">            
            <div class="clear"></div>
        </div>                    
        
        <div class="form_item">
            <label for="p_link_title">Title</label>
            <input type="text" name="p_link_title" id="p_link_title" value="" class="txt">            
            <div class="clear"></div>
        </div>                            
        
        <div class="form_item">
            <label for="p_link_href">HREF</label>
            <input type="text" name="p_link_href" id="p_link_href" value="" class="txt">            
            <div class="clear"></div>
        </div>                            
        
        <div class="spacer10"></div>
        
        <h3>Verificari</h3>
        <div class="form_item">
            <label for="my_link">Link-ul meu</label>
            <input type="text" name="my_link" id="my_link" value="" class="txt">            
            <div class="clear"></div>
        </div>                    
        
        <div class="form_item">
            <label for="p_checkpage">Pagina de verificat</label>
            <input type="text" name="p_checkpage" id="p_checkpage" value="" class="txt">            
            <div class="clear"></div>
        </div>                            
        
        <div class="form_item">
            <label for="active">Activ</label>
            <select name="active">
                <option value="1">da</option>
                <option value="0">nu</option>
            </select>
            <div class="clear"></div>
        </div>                                    
        
        <div class="spacer10"></div>
        
        <div class="form_item">
            <label>&nbsp;</label>
            <a href="javascript:void(0)" onclick="if(validateForm(arFields, 'error-holder')){$('#addlep').submit();}" class="button"><span>Adauga</span></a>
            <a href="javascript:void(0)" onclick="$('#add-lep').toggle(); $('#error-holder').hide();" class="button"><span class="normal">Renunta</span></a>
            <div class="clear"></div>            
        </div>
    </form>    

    <script language="javascript">
        //validation rules
        var arFields = new Array();
        var arField = new Array("Nume", "p_name", "required"); arFields[1] = arField;            
        var arField = new Array("Email", "p_email", "required"); arFields[2] = arField;            
        var arField = new Array("Website", "p_website", "required"); arFields[3] = arField;                    
        var arField = new Array("Caption", "p_link_caption", "required"); arFields[4] = arField;            
        var arField = new Array("Title", "p_link_title", "required"); arFields[5] = arField;            
        var arField = new Array("HREF", "p_link_href", "required"); arFields[6] = arField;            
        var arField = new Array("Link-ul meu", "my_link", "required"); arFields[7] = arField;            
        var arField = new Array("Pagina de verificat", "p_checkpage", "required"); arFields[8] = arField;            
    </script> 
    
    <div class="form_item">        
        <label>&nbsp;</label>
        <div id="error-holder" style="display: none;"></div>
        <div class="clear"></div>
    </div>
    
</div>

<hr>

<div style="width: 750px;">
    <a href="u_linkex.php?action=checkall" style="margin-left: 100px; float: right;" class="button"><span>Verifica toate linkurile</span></a>
    <h3>Lista parteneri link exchange</h3>
    <div class="clear"></div>    
</div>

<div class="spacer10"></div>

<div style="width: 750px;">

    <?
    $color['unset'] = '#f2f2f2';
    $color['active'] = '#b3cf58';
    $color['inactive'] = '#c44032';
    
    $les = $db->get_results("Select *, DATE_FORMAT(last_date, '%d %b %Y - %H:%i') as last_date_nice from le_partners order by p_website asc, id asc");
    if($les!=null){
        $i=0;
        foreach($les as $le){
            $i++;
            $lestate = "unset";
            if($le->last_status==0){$lestate = "inactive";}
            if($le->last_status==1){$lestate = "active";}
            ?>
            <div style="margin-bottom: 10px; border: 5px solid <?=$color[$lestate]?>; padding: 5px;">                                
                <div style="margin-bottom: 5px;">
                    <div style="float: right;">
                    <a href="u_linkex.php?action=check&id=<?=$le->id?>"><b>verifica acum</b></a>
                    &nbsp;|&nbsp;
                    <a href="javascript:void(0)" onclick="ConfirmDelete2Link('u_linkex.php?action=delete&id=<?=$le->id?>')"><b>sterge</b></a>                    
                    </div>
                    
                    <?=$i?>. <?=$le->p_name?> (<a href="mailto:<?=$le->p_email?>"><i><?=$le->p_email?></i></a> / <a href="<?=$le->p_website?>" target="_blank"><i><?=$le->p_website?></i></a>) 
                    - <a href="javascript:void(0)" style="width: 10px;" onclick="$('#moredetails-<?=$le->id?>').toggle()">detalii</a>                    
                    <div style="padding-bottom: 10px; padding-top: 3px; display: none;" id="moredetails-<?=$le->id?>"><?=nl2br($le->p_obs)?></div>                    
                    
                    <div class="clear"></div>
                </div>
                
                <div style="float: left; width: 330px; margin-right: 10px;">
                    <div><b>Link de afisat</b></div>
                    <div style="font-size: 10px; margin-top: 3px;">CAPTION: <?=$le->p_link_caption?></div>
                    <div style="font-size: 10px; margin-top: 3px;">TITLE: <?=$le->p_link_title?></div>
                    <div style="font-size: 10px; margin-top: 3px;">HREF: <?=$le->p_link_href?></div>
                </div>
                <div style="float: left; width: 330px;">
                    <div><b>Link de verificat</b></div>
                    <div style="font-size: 10px; margin-top: 3px;">Link catre: <?=$le->my_link?></div>
                    <div style="font-size: 10px; margin-top: 3px;">Link pe: <?=$le->p_checkpage?></div>
                    <div class="spacer5"></div>
                    <div style="font-size: 10px; margin-top: 3px;">
                        <b>Verificare</b>: <?
                        if($le->last_status==-1){
                            ?>linkul nu a fost verificat<?
                        }elseif($le->last_status==0){
                            ?>linkul este <b>inactiv</b> <br /><i>(ultima verificare la <u><?=$le->last_date_nice?></u>)</i><?
                        }elseif($le->last_status==1){
                            ?>linkul este <b>activ</b> <br /><i>(ultima verificare la <u><?=$le->last_date_nice?></u>)</i><?
                        }
                        ?>
                    </div>
                </div>
                
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>

</div>


<?php
include "include/footer.php";
?>
