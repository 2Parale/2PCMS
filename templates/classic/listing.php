<div id="listing-list">
    <?
    if($records!=null){
        $i=0;
        foreach($records as $record){
            $i++;
            $arListedProducts[] = $record->id;
            ?>
            <div class="list-item<?if($i/2==(int)($i/2)){echo " last-item";}?>">                
                <span class="price"><?=$record->price_int?> Lei</span>
                <a href="<?=$_CONFIG["urlpath"]?><?=$record->title_url?>-<?=$record->id?>.html" title="<?=$record->title?>" class="title"><?=$record->title?></a>
                <a href="<?=$_CONFIG["urlpath"]?><?=$record->title_url?>-<?=$record->id?>.html" title="<?=$record->title?>"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$record->local_img_small?>" alt="<?=$record->title?>" align="left" border="0" width="90" height="90"/></a>                
                <span class="desc"><?=substr($record->description,0,190)?><?if(strlen($record->description)>190){echo "...";}?></span>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>
    
    <div class="clear"></div>
</div>