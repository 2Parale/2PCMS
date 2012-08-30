    </div>
    <? /** CONTENT ZONE END (starts in header.php)**/?> 
    
    <div class="clear"></div>
</div>
<? /** CONTENT WRAPPER END (starts in header.php)**/?> 


<? /** FOOTER WRAPPER START **/?> 
<div id="footer-wrapper">

    <? /** FOOTER -- general info **/?> 
    <div class="fitem" style="width: 150px;">
        <span>Despre noi</span>
        <a href="<?=$_CONFIG["urlpath"]?>" class="normal" title="<?=$_CONFIG["site_name"]?>"><?=$_CONFIG["site_name"]?></a>
        <div class="spacer10"></div>
        
        
        <? /** SOCIAL MEDIA START **/?>
        
        <?
        //Facebook
        if($_CONFIG["settings"]["facebook_link"]!=""){
            ?>
            <a href="<?=$_CONFIG["settings"]["facebook_link"]?>" title="Facebook" target="_blank"><img src="<?=$_CONFIG["urlpath"]?>images/social-icons/<?=$_CONFIG["settings"]["facebook_icon_size"]?>/<?=$_CONFIG["settings"]["facebook_icon_file"]?>_<?=$_CONFIG["settings"]["facebook_icon_size"]?>.png" border="0" width="<?=$_CONFIG["settings"]["facebook_icon_size"]?>" height="<?=$_CONFIG["settings"]["facebook_icon_size"]?>" alt="Facebook" /></a>
            <?
        }
        
        //Twitter
        if($_CONFIG["settings"]["twitter_link"]!=""){
            ?>
            <a href="<?=$_CONFIG["settings"]["twitter_link"]?>" title="Twitter" target="_blank"><img src="<?=$_CONFIG["urlpath"]?>images/social-icons/<?=$_CONFIG["settings"]["twitter_icon_size"]?>/<?=$_CONFIG["settings"]["twitter_icon_file"]?>_<?=$_CONFIG["settings"]["twitter_icon_size"]?>.png" border="0" width="<?=$_CONFIG["settings"]["twitter_icon_size"]?>" height="<?=$_CONFIG["settings"]["twitter_icon_size"]?>" alt="Facebook" /></a>
            <?
        }            
        ?>
        <? /** SOCIAL MEDIA END **/?>         
        
    </div>
    
    
    <? /** FOOTER -- categories **/?> 
    <div class="fitem">
        <span>Categorii</span>
        <?        
        if($fcats!=null){
            foreach($fcats as $cat){
                ?>
                <a href="<?=$_CONFIG["urlpath"]?><?=$cat->category_url?>/" class="normal"><?=$cat->category?></a>
                <?
            }
        }
        ?>        
    </div>
    
    
    <? /** FOOTER -- products **/?> 
    <div class="fitem" style="width: 340px;">
        <span>Produse</span>
        <?        
        if($fprods!=null){
            foreach($fprods as $fprod){
                ?>
                <a href="<?=$_CONFIG["urlpath"]?><?=$fprod->title_url?>-<?=$fprod->id?>.html" class="normal"><?=$fprod->title?></a>
                <?
            }
        }        
        ?>
    </div>
    
    
    <? /** FOOTER -- searches **/?> 
    <div class="fitem" style="width: 170px;">
         <span>Cautari frecvente</span>
        <?        
        if($searches!=null){
            foreach($searches as $search){
                ?>
                <a href="<?=$_CONFIG["urlpath"]?>cauta/<? echo urlencode($search->sterm);?>/" class="small"><?=$search->sterm?></a>
                <?
            }
        }
        ?>
    </div>    
    
    
    <div class="clear"></div>
    
    <? /** FOOTER -- link exchange **/?> 
    <div class="fitem-vertical">
        <span>Parteneri:</span>
        <?        
        if($les!=null){
            foreach($les as $le){
                ?>
                <a href="<?=$le->p_link_href?>" title="<?=$le->p_link_title?>" class="normal"><?=$le->p_link_caption?></a>
                <?
            }
        }
        ?>
    </div>
    
    <div class="clear"></div>
</div>
<? /** FOOTER WRAPPER END **/?> 



</body>
</html>