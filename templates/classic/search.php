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
                <a href="<?=$_CONFIG["urlpath"]?><?=$record->title_url?>-<?=$record->id?>.html" title="<?=$record->title?>"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$record->local_img_small?>" alt="<?=$record->title?>" align="left" border="0" width="90" height="90"></a>                
                <span class="desc"><?=substr($record->description,0,190)?><?if(strlen($record->description)>190){echo "...";}?></span>
                <div class="clear"></div>
            </div>
            <?
        }
    } else {
        ?>
        <div class="search-no-results">
            <h3>Ne pare rau dar nu am gasit niciun produs conform cautarii.</h3>
            <div class="spacer10"></div>
            
            <?
            if($rec_categories!=null){
                ?>
                <div>
                    <h4>Iti recomandam una din aceste categorii: </h4>
                    <?
                    foreach($rec_categories as $rec_c){
                        ?>
                        <a href="<?=$_CONFIG["urlpath"]?><?=$rec_c->category_url?>/" class="rec_link"><?=$rec_c->category?></a> 
                        <?
                    }
                    ?>
                </div>
                <div class="spacer10"></div>
                <div class="spacer10"></div>
                <?
            }
            
            if($rec_products!=null){
                ?>
                <div>
                    <h4>Iti recomandam unul din aceste produse: </h4>
                    <?
                    $i=0;
                    foreach($rec_products as $nprod){
                        $i++;
                        ?>
                        <div class="rec-product-item<? if($i/2==(int)($i/2)){echo "-last";} ?>">
                            <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>"><img src="<?=$_CONFIG["urlpath"]?>product_images/<?=$nprod->local_img_small?>" alt="<?=$nprod->title?>" align="left" border="0" width="90" height="90"/></a>
                            <div class="info-item">
                            <a href="<?=$_CONFIG["urlpath"]?><?=$nprod->title_url?>-<?=$nprod->id?>.html" title="<?=$nprod->title?>" class="title"><?=$nprod->title?></a>                            
                            <span><?=$nprod->price_int?> Lei</span>
                            </div>
                            <div class="clear"></div>
                        </div>                        
                        <?
                    }
                    ?>
                    <div class="clear"></div>
                </div>
                <?
            }
            ?>
        </div>
        <?
    }
    
    
    ?>
    
    <div class="clear"></div>
</div>