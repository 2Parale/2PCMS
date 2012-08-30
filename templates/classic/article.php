<div class="article-page-content">
    <?=strip_tags(str_replace("</p>","</p><br><br>",$oarticle->acontent),"<br><b><i>")?>
</div>

<div class="article-page-products">
    <?                
    if($asoc_products){
        $i=0;
        foreach($asoc_products as $nprod){
            $i++;
            $arListedProducts[] = $nprod->id;
            ?>
            <div class="item">
                <span>
                    <span class="iprice"><?=$nprod->price_int?> Lei</span>
                    <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>"><?=$nprod->title?></a>
                    <div class="clear"></div>                    
                </span>                
                <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$nprod->local_img_big?>" alt="<?=$nprod->title?>" align="left" border="0" width="250" height="250"/></a>
                <div class="clear"></div>
            </div>
            
            <? if($i/2==(int)($i/2)){echo '<div class="clear"></div>';} ?>
            <?
        }
    }
    ?>
    <div class="clear"></div>    
</div>
