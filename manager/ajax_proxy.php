<?php
include "../common/common.php"; 

//handle ajax requests
if(isset($_GET["action"])){

    if($_GET["action"]=="get_article_products"){
        $id = (int)$_GET["id"];
        
        $aprods = $db->get_results("Select * from article_x_products a 
        left join shop_products b on a.shop_product_id=b.id 
        where a.article_id=$id");
        
        if($aprods==null){
            echo "<i>Nu exista produse asociate acestui articol.</i>";
        }else{
            foreach($aprods as $aprod){
                echo '<div style="margin-bottom: 5px; padding: 3px; border-bottom: 1px solid #8c8c8c; ">';                
                echo "<span style='display: block; margin-bottom: 8px;'><b>" . $aprod->title . "</b> - " . $aprod->price . "</span>";
                echo '<a href="javascript:void(0)" onclick="deleteProductToArticle('.$id.','.$aprod->id.',\'article-products-list\')" style="margin-right: 10px;">sterge asocierea</a>';
                echo '<a href="'.$_CONFIG["urlpath"].$aprod->title_url.'-'.$aprod->id.'.html" target="_blank" style="margin-right: 10px;">vezi produs</a>';                
                echo '</div>';
                echo '<div class="clear"></div>';                
            }
        }
        
        exit();
    }    

    if($_GET["action"]=="search_article_products"){
        $id = (int)$_GET["id"];
        $start = (int)$_GET["start"];
        $search = $_GET["search"];
        
        echo '<div style="background-color: #F0CC2D; padding: 5px; margin-bottom: 5px;"><a href="javascript:void(0);" style="float: right;" onclick="clearSearchArticleProducts(\'article-products-search-list\')">inchide</a><b>Rezultate cautare:</b><div class="clear"></div></div>';
        
        $search = str_replace(" ","%",$search);
        $sprods = $db->get_results("Select * from shop_products where active=1 and (title like '$search' or title like '%$search' or title like '$search%' or title like '%$search%') limit $start, 10");
        $sprods_count = (int)$db->get_var("Select count(id) from shop_products where active=1 and (title like '$search' or title like '%$search' or title like '$search%')");
        if($sprods==null){
            echo "Nu au fost gasite produse conform cautarii";
        }else{
            foreach($sprods as $sprod){
                echo '<div style="margin-bottom: 5px; padding: 3px; border-bottom: 1px solid #8c8c8c; ">';                
                echo "<span style='display: block; margin-bottom: 8px; font-weight: bold;'>".$sprod->title."</span>";
                echo '<a href="javascript:void(0)" onclick="asocProductToArticle('.$id.','.$sprod->id.',\'article-products-list\')" style="margin-right: 10px;">asociaza</a>';
                echo '<a href="'.$_CONFIG["urlpath"].$sprod->title_url.'-'.$sprod->id.'.html" target="_blank" style="margin-right: 10px;">vezi produs</a>';
                echo '</div>';
            }
            $start = $start + 10;
            if($start<$sprods_count){
                echo '<div><a href="javascript:void(0)" onclick="getSearchArticleProducts(\'search_asoc\', \'article-products-search-list\', '.$id.','.$start.')"><b>mai multe rezultate &raquo;</b></a></div>';
            }
            
        }
        
        exit();
    }    
    
    if($_GET["action"]=="add_article_product"){
        $product_id = (int)$_GET["product_id"];
        $article_id = (int)$_GET["article_id"];
        
        $db->query("Insert into article_x_products (article_id, shop_product_id) values ($article_id, $product_id) on duplicate key update id=id");
        
        exit();
    }
    
    if($_GET["action"]=="delete_article_product"){
        $product_id = (int)$_GET["product_id"];
        $article_id = (int)$_GET["article_id"];
        
        $db->query("Delete from article_x_products where article_id=$article_id and shop_product_id=$product_id");
        
        exit();
    }    
    
    
    if($_GET["action"]=="lp_search_products"){
        $start = (int)$_GET["start"];
        $search = $_GET["search"];
        
        echo '<div style="background-color: #F2F8EC; padding: 5px; margin-bottom: 5px;"><a href="javascript:void(0);" style="float: right;" onclick="clearLPSearchProducts(\'prod-results\')">inchide</a><b>Rezultate cautare:</b><div class="clear"></div></div>';
        
        $search = str_replace(" ","%",$search);
        $sprods = $db->get_results("Select * from shop_products where active=1 and (title like '$search' or title like '%$search' or title like '$search%' or title like '%$search%') limit $start, 10");
        $sprods_count = (int)$db->get_var("Select count(id) from shop_products where active=1 and (title like '$search' or title like '%$search' or title like '$search%')");
        if($sprods==null){
            echo "Nu au fost gasite produse conform cautarii";
        }else{
            foreach($sprods as $sprod){
                echo '<div style="margin-bottom: 5px; padding: 3px; border-bottom: 1px solid #8c8c8c; ">';                
                echo "<span style='display: block; margin-bottom: 8px; font-weight: bold;'>".$sprod->title."</span>";
                echo '<a href="javascript:void(0)" onclick="lpSelectProduct('.$sprod->id.')" style="margin-right: 10px;">alege produsul</a>';
                echo '<a href="'.$_CONFIG["urlpath"].$sprod->title_url.'-'.$sprod->id.'.html" target="_blank" style="margin-right: 10px;">vezi produs</a>';
                echo '</div>';
            }
            $start = $start + 10;
            if($start<$sprods_count){
                echo '<div><a href="javascript:void(0)" onclick="lpSearchProducts(\'search-prod\', \'prod-results\', '.$start.')"><b>mai multe rezultate &raquo;</b></a></div>';
            }
            
        }
        
        exit();
    }
    
    if($_GET["action"]=="lp_get_product_details"){
        $id = (int)$_GET["id"];
        $prod = $db->get_row("Select * from shop_products where id=$id");
        $arProd = array();
        
        $arProd['title'] = $prod->title;
        $arProd['title_url'] = $prod->title_url;
        $arProd['description'] = $prod->description;
        
        echo json_encode($arProd);
        
        exit();
    }    
    
    
    
    
    if($_GET["action"]=="get_url_from_text"){
        $text = $_GET["text"];
        echo text_to_url($text);
        exit();
    }
    
    
    if($_GET["action"]=="get_local_feed_list"){
        
        $handler = opendir("../feedupload/");
        while ($file = readdir($handler)) {
            if ($file != "." && $file != "..") {        
                ?>
                <div style="margin-bottom: 3px;"><a href="javascript:void(0)" onclick="$('#feed_url').val('<?=$_CONFIG["urlpath"]?>feedupload/<?=$file?>')"><b>alege</b></a> <?=$file?> (<? echo number_format(filesize("../feedupload/".$file)/1024/1024,2,",",""); ?> Mb)</div>
                <?
            }    
        }
        closedir($handler);        
        
        exit();
    }    
    
    if($_GET["action"]=="get_img_process_count"){
        echo (int)$db->get_var("Select count(*) from shop_products where local_img_small=''");
        exit();
    }
    
    
    if($_GET["action"]=="get_brands"){
        $what = $_GET["what"];
        
        if($what=="0"){
            $sql = "Select * from shop_brands where MID(brand,1,1)='0' OR MID(brand,1,1)='1'  OR MID(brand,1,1)='2' OR MID(brand,1,1)='3' OR MID(brand,1,1)='4' OR MID(brand,1,1)='5' OR MID(brand,1,1)='6' OR MID(brand,1,1)='7' OR MID(brand,1,1)='8' OR MID(brand,1,1)='9'";
        }else{
            $sql = "Select * from shop_brands where MID(brand,1,1)='".$what."'";
        }
        
        $sql .= " order by prod_count desc, brand asc";
        
        $recs = $db->get_results($sql);
        if($recs!=null){
            $c = 0;
            foreach($recs as $rec){
                $c++;
                if($rec->prod_count==0){
                    echo '<div style="width: 240px; float: left; margin-right: 5px; margin-bottom: 5px; padding: 4px 2px; background-color: #f2f2f2;">'.$rec->brand.' ('.$rec->prod_count.')</div>';
                }elseif($rec->prod_count>0){
                    echo '<div style="width: 240px; float: left; margin-right: 5px; margin-bottom: 5px; padding: 4px 2px; background-color: #f2f2f2;">
                    <a href="javascript:void(0)" onclick="editPopup(\'popup_brand_edit.php?id='.$rec->id.'&what='.$what.'\')" style="float: right;" title="editare"><img src="images/edit.gif" width="16" height="16" alt="editare" /></a>
                    <a href="'.$_CONFIG["urlpath"].'brand/'.$rec->brand_url.'/" target="_blank" title="'.$rec->brand.' - '.$rec->prod_count.' produse">'.$rec->brand.' ('.$rec->prod_count.')</a>
                    <div class="clear"></div>
                    </div>';
                }
                
                if($c/4==(int)($c/4)){echo '<div class="clear"></div>';}
                
            }
        }
        echo '<div class="clear"></div>';
        
        exit();
    }
    
    
    if($_GET["action"]=="get_wait_keywords"){
        echo '<a href="javascript:void()" onclick="getWaitKeywords(\'wait-list\')"><b>refresh</b></a>';
        echo '&nbsp;|&nbsp;';
        echo '<a href="javascript:void()" onclick="$(\'#wait-list\').toggle();"><b>ascunde</b></a>';
        echo '<div class="spacer10"></div>';
        
        $words = $db->get_results("Select * from shop_searches where active=0 limit 15");
        if($words!=null){
            $i=0;
            foreach($words as $word){
                $i++;
                
                echo '<div style="float: left; width: 225px; margin-right: 5px; margin-bottom: 10px; padding: 5px; border: 1px solid #8c8c8c;" id="welid-'.$word->id.'">';
                echo '<div style="float: right; text-align: right;">';
                echo '<a href="javascript:void(0)" onclick="setKeywordStatus('.$word->id.', \'minus\')" title="Sterge" style="margin-right: 5px;"><img src="images/delete.png" alt="Sterge" width="16" height="16"/></a>';
                echo '<a href="javascript:void(0)" onclick="setKeywordStatus('.$word->id.', \'plus\')" title="Aproba"><img src="images/ok.png" alt="Aproba" width="16" height="16"/></a>';
                echo '</div>';
                echo '<a href="'.$_CONFIG["urlpath"].'cauta/'.urlencode($word->sterm).'/" target="_blank">' . $word->sterm . "</a> ". '<img src="images/external-link.png" width="9" height="9" alt="link extern"/>' ."  (" . $word->scount . ")";
                echo '</div>';
                
                if($i/3==(int)($i/3)){
                    #echo '<div class="clear"></div>';
                }
            }
            
            echo '<div class="clear"></div>';
        }
        
        exit();
    }
    
    
    if($_GET["action"]=="update_keyword"){
        $id = (int)$_GET["id"];
        $status = $_GET["status"];
        
        if($status=="minus"){
            $db->query("delete from shop_searches where id=$id");
        }elseif($status=="plus"){
            $db->query("update shop_searches set active=1 where id=$id");
        }
        
        exit();
    }
    
}
?>