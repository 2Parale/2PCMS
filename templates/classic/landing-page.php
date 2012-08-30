<div id="product_container">

    <div class="img_holder"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$product->local_img_big?>" alt="<?=$product->title?>"/></div>
    
    <div class="desc_holder">
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
            <?=$product->new_description?>
        </p>
    </div>
    
    <div class="clear"></div>
</div>


<div id="related_products">
    <h3>Produse similare</h3>
    
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
                    <span><?=$nprod->price?></span>
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