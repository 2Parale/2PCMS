<?
if($particles!=null){
    foreach($particles as $particle){
        $acontent = strip_tags(str_replace("</p>","</p><br><br>",$particle->acontent),"<br><b><i>");
        $content_display = substr($acontent,0,400);
        if(strlen($acontent)>strlen($content_display)){$content_display=$content_display." ...";}
        ?>
        
        <div class="article-list-item">
            <h3><a href="<?=$_CONFIG["urlpath"]?>articole/<?=$pacategory->category_url?>/<?=$particle->title_url?>-<?=$particle->id?>.html" title="<?=$particle->title?>"><?=$particle->title?></a></h3>
            <div class="content">
                <?=$content_display?>
                <div class="spacer10"></div>
                <a href="<?=$_CONFIG["urlpath"]?>articole/<?=$pacategory->category_url?>/<?=$particle->title_url?>-<?=$particle->id?>.html" title="<?=$particle->title?>">citeste tot articolul</a>
            </div>            
        </div>
        
        <?
    }
}
?>
