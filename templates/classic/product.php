<div id="product_container">

    <div class="img_holder"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$product->local_img_big?>" alt="<?=$product->title?>"/></div>
    
    <div class="desc_holder">
        <?if($product->active==1 and $product->price_int<$product->old_price_int){?>
            <div class="reducere">REDUCERE! <span>pret vechi <?=$product->old_price_int?> Lei</span></div>
        <?}?>
    
        <p class="price">                        
            <span><?=$product->price_int?> Lei</span>
            <?if($product->active==1){?>
                <a href="<?=$product->aff_url?>" rel="nofollow" target="_blank" onclick="markProductClick(<?=$product->id?>)"><img src="<?=$_CONFIG["urlpath"]?>templates/<?=$_CONFIG["template"]?>/images/btn-order.jpg" alt="Comanda produsul" border="0"/></a>
            <?}else{?>
                <span class="inactive-product">Ne pare rau dar produsul nu mai este in oferta noastra.</span>
            <?}?>            
            <div class="clear"></div>
        </p>        
        <p class="description">
            <b>Categorie:</b> <a href="<? echo $_CONFIG["urlpath"] . $product->category_url . "/";?>"><?=$product->category?></a>
            <div class="spacer5"></div>
            <?=str_replace($arDescSearch,$arDescReplace,$product->description)?>
        </p>
    </div>
    
    <div class="clear"></div>
</div>


<?
if($arPartnerData["show"]){
    ?>
    <div class="partner_box">
        <h3>Produsul este oferit de <?=$arPartnerData["name"]?></h3>
        <?
        if($arPartnerData["logo"]!=""){?>
        <a href="<?=$arPartnerData["url"]?>" target="_blank" rel="nofollow" title="<?=$arPartnerData["name"]?>"><img src="<?=$_CONFIG["urlpath"]?>assets/partner-images/<?=$arPartnerData["logo"]?>" alt="<?=$arPartnerData["name"]?>"/></a>
        <?}
        ?>
        <?=$arPartnerData["content"]?>
        <div class="clear"></div>
    </div>
    <?
}
?>


<?
if($_CONFIG["settings"]["article_product_page_box"]=="da"){
    if($related_articles!=null){
        ?>
    <div id="article_box">
        <h3>Articole pentru <i><?=$product->title?></i></h3>
        <?
        foreach($related_articles as $particle){
            $acontent = strip_tags(str_replace("</p>","</p><br><br>",$particle->acontent),"<br><b><i>");
            ?>
            <div class="article_item">
                <span><?=$particle->title?></span>
                <div id="asummary-<?=$particle->id?>">                    
                    <?=substr($acontent,0,250)?> ... <a href="javascript:void(0)" onclick="$('#asummary-<?=$particle->id?>').fadeOut('fast', function(){$('#aall-<?=$particle->id?>').fadeIn('fast');});">tot articolul &raquo;</a>
                </div>
                <div id="aall-<?=$particle->id?>" style="display: none;">                    
                    <?=$acontent?>
                    <a href="javascript:void(0)" onclick="$('#aall-<?=$particle->id?>').fadeOut('fast', function(){$('#asummary-<?=$particle->id?>').fadeIn('fast');});">&laquo; restrange</a>
                </div>                
            </div>
            <?
        }
        ?>
    </div>
        <?
    }
}
?>


<div id="related_products">
    <h3>Alte produse care te-ar putea interesa</h3>
    
    <div class="index-products">
        <?                    
        if($related_prods){
            $i=0;
            foreach($related_prods as $nprod){
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
    
</div>
