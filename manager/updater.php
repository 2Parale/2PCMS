<?php
include "../common/common.php";



?>
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="css/admin.css">
<script language="javascript" src="js/functions.js"></script>
<script language="javascript" src="../common/jquery-1.3.2.min.js"></script>
</head>
<body>

<div style="padding: 5px;">

    <?
    if(!isset($_GET["action"])){
    
    $objCheck = simplexml_load_file($updater_url.$cms_version);
    $objUpdate = simplexml_load_file($objCheck->update_url);
    ?>
    <div style="border-top: 2px solid #404040; padding-top: 10px;">
        <a href="javascript:void(0)" onclick="window.opener.closeIndexUpdater()" style="float: right;">inchide</a>
        <h3>Descriere update</h3>
        <div class="clear"></div>
    </div>

    <span style="background-color: #dfe790; display: block; padding: 5px; color: #404040;"><?=$objUpdate->info?></span>
    
    <div class="spacer10"></div>
    <div>
        <a href="updater.php?action=update" target="_self" class="button"><span>Proceseaza update</span></a>
        <div class="clear"></div>                                                                
    </div>
    
    <?
    }else{
        
        if($_GET["action"]=="update"){
            $objCheck = simplexml_load_file($updater_url.$cms_version);
            $objUpdate = simplexml_load_file($objCheck->update_url."?remote-url=".urlencode($_CONFIG["urlpath"]));            
            ?>
            <h3>Procesare update <?=$objCheck->remote_version?> la <?=$objCheck->server_version?></h3>
            
            <?
            echo "Creare folder temporar <b><i>/update-temp/</i></b><br />";
            @mkdir("../update-temp/");
            
            echo "<br />";
            
            echo "Download update SQL";
            file_put_contents("../update-temp/sql.zip", file_get_contents($objUpdate->sql_file));
            echo " ... marime ". number_format(filesize("../update-temp/sql.zip")/1024,2) . " Kb<br />";
            
            echo "Download update surse";
            file_put_contents("../update-temp/source.zip", file_get_contents($objUpdate->source_file));
            echo " ... marime ". number_format(filesize("../update-temp/source.zip")/1024,2) . " Kb<br />";            
            
            echo "<br />";
            
            echo "Unzip update SQL<br />";
            $zip = new ZipArchive();
            $res = $zip->open("../update-temp/sql.zip");
            if($res===true){
                $zip->extractTo("../update-temp/");
                $zip->close();
                
                echo "Rulez update SQL<br />";
                $sql = file_get_contents("../update-temp/sql.sql");
                $arSql = explode(";",$sql);
                foreach($arSql as $sql_item){
                    if($sql_item!=""){
                        $db->query($sql_item);
                    }                    
                }
                
                @unlink("../update-temp/sql.zip");
                @unlink("../update-temp/sql.sql");
            } else {
                echo "Unzip update SQL - eroare, update-ul SQL nu a fost procesat<br />";
            }
            
            echo "<br />";
            
            echo "Unzip update surse<br />";
            $zip = new ZipArchive();
            $res = $zip->open("../update-temp/source.zip");            
            if($res===true){
                $zip->extractTo("../");
                $zip->close();                
            }else{
                echo "Unzip update surse - eroare, update-ul de surse nu a fost procesat<br />";
            }
            @unlink("../update-temp/source.zip");
            
            ?>
            <br /><br />
            Update-ul a fost finalizat!
            <div class="spacer10"></div>
    <div>
        <a href="javascript:void(0)" onclick="window.parent.callFromIframe_Refresh()" class="button"><span>Refresh pagina</span></a>
        <div class="clear"></div>                                                                
    </div>            
            
            <?
        }
        
    }
    ?>
    
</div>
</body>
</html>