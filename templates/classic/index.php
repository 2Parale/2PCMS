<div class="index-products">
    <span class="heading">Cele mai noi produse</span>
    <?                
    if($nprods){
        $i=0;
        foreach($nprods as $nprod){
            $i++;
            $arListedProducts[] = $nprod->id;
            ?>
            <div class="index-product-item<? if($i/2==(int)($i/2)){echo "-last";} ?>">
                <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$nprod->local_img_small?>" alt="<?=$nprod->title?>" align="left" border="0" width="90" height="90"/></a>
                <div class="item-info">
                <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>" class="title"><?=$nprod->title?></a>
                <span><?=$nprod->price_int?> Lei</span>
                </div>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>
    <div class="clear"></div>
</div>



<div class="spacer10"></div>



<div class="index-products">
    <span class="heading">Ce prefera vizitatorii</span>
    <?
    
    if($vprods){
        $i=0;
        foreach($vprods as $nprod){
            $i++;
            $arListedProducts[] = $nprod->id;
            ?>
            <div class="index-product-item<? if($i/2==(int)($i/2)){echo "-last";} ?>">                
                <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$nprod->local_img_small?>" alt="<?=$nprod->title?>" align="left" border="0" width="90" height="90"/></a>
                <div class="item-info">
                <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>" class="title"><?=$nprod->title?></a>
                <span><?=$nprod->price_int?> Lei</span>
                </div>
                <div class="clear"></div>
            </div>
            <?
        }
    }
    ?>
    <div class="clear"></div>
</div> 