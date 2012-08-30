<?php
include "../common/common.php";

if(!userIsOk()){redirTo("login.php");}//checking user


$page_name = "index";
$page_title = "Dashboard";
include "include/header.php";
?>

<div style="width: 900px;">
    
    <div style="margin-bottom: 10px; padding: 2px; border: 1px solid #f2f2f2;" id="updater-holder">
        <?
        $objCheck = simplexml_load_file($updater_url.$cms_version."&remote-url=".urlencode($_CONFIG["urlpath"]));
        ?>    
        <span style="float: left; padding: 5px; background-color: #f2f2f2; width: 150px; font-weight: bold;">Versiune 2PCMS: </span>
        <div style="float: left; padding: 5px;">
            <div>versiunea ta: <b><?=$cms_version?></b></div>
            <div>versiunea oficiala: <b><?=$objCheck->server_version?></b></div>
            <?
            if($objCheck->server_version!=$cms_version){
                if($objCheck->update_url==""){
                    ?>
                    <div class="clear spacer5"></div>
                    Un update nu poate fi efectuat acum, mesajul de la server este: <i>"<?=$objCheck->info?>"</i>
                    <?
                }else{
                    ?>
                    <script type="text/javascript">
                        $('#updater-holder').css('border','5px solid #C52546');
                    </script>
                    <div class="spacer10"></div>
                    <div style="background-color: #fcffc8; padding: 4px;">
                    Un update este disponibil: <i>"<?=$objCheck->info?>"</i>
                    <div class="clear"></div>
                    <div class="spacer10"></div>
                    <div>
                        <a class="button" href="javascript:void(0)" onclick="$('#cms-updater').slideDown('fast'); window.open('updater.php','cms-updater')"><span>UPDATE 2PCMS</span></a>
                        <div class="clear"></div>
                    </div>
                    </div>
                    <?
                }
            }
            ?>
        </div>
        <div class="clear"></div>
        
        <iframe name="cms-updater" frameborder="0" scrolling="auto" width="880" height="300" style="display: none;" id="cms-updater"></iframe>
        
    </div>
    
    <div style="margin-bottom: 10px; padding: 2px; border: 1px solid #f2f2f2;">
        <span style="float: left; padding: 5px; background-color: #f2f2f2; width: 150px; font-weight: bold;">Sitemap: </span>
        <div style="float: left; padding: 5px;">
        <?
        if(@filemtime("../sitemaps/sitemap_index.xml")===false){
            echo "sitemap-urile nu au fost generate";
        }else{
            $ftime = filemtime("../sitemaps/sitemap_index.xml");
            echo "sitemap-urile au fost generate la data <b><i>" . date("d M Y, H:i:s",$ftime) . "</i></b>";
        }        
        ?>
        <i>(pentru a genera sitemap-ul acum click pe url sitemap)</i>
        <br />
        <div class="spacer5"></div>
        <i>url sitemap: <a href="<?=$_CONFIG["urlpath"]?>sitemap.xml" target="_blank"><?=$_CONFIG["urlpath"]?>sitemap.xml</a></i>
        </div>
        <div class="clear"></div>
    </div>    
    
    <div class="dashboard-item">
        <span>
            <a href="sd_feeds.php" style="float: right; margin-right: 5px;">edit</a>
            Status feed-uri
        </span>
        
        <div class="spacer5"></div>
        
        <div style="float: left; width: 50%; font-weight: bold;">total parteneri: <? echo (int)$db->get_var("Select count(*) from aff_partners"); ?></div>
        <div style="float: left; width: 50%; font-weight: bold;">total feed-uri: <? echo (int)$db->get_var("Select count(*) from aff_feeds"); ?></div>
        
        
        <div class="clear"></div>
        <div class="spacer5"></div>
        
        <div style="font-weight: bold; margin-bottom: 5px; border-top: 1px solid #444; padding-top: 5px;">Ultimele 4 feed-uri importate:</div>
                
        <?
        $feeds = $db->get_results("Select a.*, b.shop from aff_feeds a left join aff_partners b on a.partner_id=b.id order by a.last_date desc limit 4");
        if($feeds!=null){
            foreach($feeds as $feed){
                ?>
                <div style="margin-bottom: 3px;">
                    <?=$feed->shop?> - <?=$feed->feed_filename?> : <?=$feed->last_date?>
                </div>
                <?
            }
        }
        ?>
        
    </div>

    <div class="dashboard-item">
        <span>
            <a href="u_linkex.php" style="float: right; margin-right: 5px;">edit</a>
            Status Link Exchange
        </span>
        
        <div class="spacer5"></div>
        
        <div style="float: left; width: 33%; font-weight: bold;">total: <? echo (int)$db->get_var("Select count(*) from le_partners"); ?></div>
        <div style="float: left; width: 33%; font-weight: bold;">valide: <? echo (int)$db->get_var("Select count(*) from le_partners where last_status=1"); ?></div>
        <div style="float: left; width: 33%; font-weight: bold;">invalide: <? echo (int)$db->get_var("Select count(*) from le_partners where last_status=0"); ?></div>
        <div class="clear"></div>
        
        <div class="spacer5"></div>
        
        <div style="font-weight: bold; margin-bottom: 5px; border-top: 1px solid #444; padding-top: 5px;">Ultimele 4 linkuri verificate:</div>
        <?
        $les = $db->get_results("Select * from le_partners order by last_date desc limit 4");
        if($les!=null){
            $color['unset'] = '#f2f2f2';
            $color['active'] = '#b3cf58';
            $color['inactive'] = '#c44032';            
            foreach($les as $le){
                $lestate = "unset";
                if($le->last_status==0){$lestate = "inactive";}
                if($le->last_status==1){$lestate = "active";}                
                ?>
                <div style="margin-bottom: 3px;">
                    <div style="width: 15px; height: 15; background-color: <?=$color[$lestate]?>; float: left; margin-right: 5px;"></div>
                    <?=$le->p_name?>
                    <a href="<?=$le->p_checkpage?>" target="_blank"><?=$le->p_checkpage?><img src="images/external-link.png" width="9" height="9" alt="link extern" style="margin-left: 5px;"/></a>
                    <div class="clear"></div>
                </div>
                <?
            }
        }
        ?>
    </div>            
    
    <div class="dashboard-item">
        <span>
            <a href="u_stats.php" style="float: right; margin-right: 5px;">edit</a>
            Statistici produse
        </span>
        <div class="spacer5"></div>
        <div style="font-weight: bold; margin-bottom: 5px;">Top 5 produse dupa click-uri:</div>
        <?
        $prods = $db->get_results("Select * from shop_products order by click desc, show_inpage desc, show_inlisting desc limit 5");
        if($prods!=null){
            $i=0;
            foreach($prods as $prod){
                $i++;
                ?>
                <div style="margin-bottom: 3px;">
                    <?=$i?>. <a href="<?=$_CONFIG["urlpath"]?><?=$prod->title_url?>-<?=$prod->id?>.html" target="_blank"><?=$prod->title?></a> - <?=$prod->show_inlisting?>L / <?=$prod->show_inpage?>P / <?=$prod->click?>C
                </div>
                <?
            }
        }
        ?>
        
        <div style="font-style: italic; font-size: 10px; margin-top: 10px;">L - afisari listing | P - afisari pagina produs | C - click link afiliat</div>
    </div>
    
    <div class="dashboard-item">
        <span>
            <a href="u_seokeys.php" style="float: right; margin-right: 5px;">edit</a>
            Statistici cautari
        </span>
        <?
        $searches1 = $db->get_results("Select * from shop_searches order by scount desc limit 5");
        $searches2 = $db->get_results("Select * from shop_searches order by vcount desc limit 5");
        ?>
        <div class="spacer5"></div>
        <div style="float: left; width: 195px; border-right: 1px solid #444; margin-right: 5px; padding-right: 5px;">
            <div style="font-weight: bold; margin-bottom: 5px;">Cautari unice:</div>
            <?
            if($searches1!=null){
                foreach($searches1 as $si){
                    ?>
                    <a href="<?=$_CONFIG["urlpath"]?>cauta/<?=urlencode($si->sterm)?>/" style="display: block" target="_blank"><?=$si->sterm?> (<?=$si->scount?>)<img src="images/external-link.png" width="9" height="9" alt="link extern" style="margin-left: 5px;"/></a>
                    <?
                }
            }
            ?>
        </div>
        <div style="float: left; width: 195px; margin-left: 5px; padding-left: 5px;">
            <div style="font-weight: bold; margin-bottom: 5px; ">Afisari:</div>
            <?
            if($searches2!=null){
                foreach($searches1 as $si){
                    ?>
                    <a href="<?=$_CONFIG["urlpath"]?>cauta/<?=urlencode($si->sterm)?>/" style="display: block" target="_blank"><?=$si->sterm?> (<?=$si->vcount?>)<img src="images/external-link.png" width="9" height="9" alt="link extern" style="margin-left: 5px;"/></a>
                    <?
                }
            }
            ?>            
        </div>
        <div class="clear"></div>
    </div>               
    
    <div class="clear"></div>
    <div class="spacer10"></div>
    <div class="spacer10"></div>
    
    <div style="width: 860px; border: 2px solid #f2f2f2; padding: 4px;">
        <h3 style="background-color: #D4EEE5; padding: 5px;">Ultimele articole de pe blogul 2PCMS</h3>
        <?
        $filemtime = @filemtime("include/_blogcontent.html");
        if ($filemtime and (time() - $filemtime < 86400)){
            readfile("include/_blogcontent.html");
        }else{
            $objBlog = simplexml_load_file("http://2pcms.2parale.ro/blog/feed/");
            $i=0;
            if($objBlog!=null){            
                $blog_content = "";
                foreach($objBlog->channel->item as $objItem){
                    $i++;
                    if($i<=3){
                        $blog_content .= '<div style="margin-bottom: 10px;">';
                        $blog_content .= '<a href="'.$objItem->link.'" target="_blank" style="display: block;"><b>'.$objItem->title.'</b></a>';
                        $blog_content .= str_replace("[...]",'<a href="'.$objItem->link.'" target="_blank">[...]</a>',$objItem->description);
                        $blog_content .= '</div>';
                    }
                }
                
                file_put_contents("include/_blogcontent.html",$blog_content);
                echo $blog_content;
            }            
        }
        ?>
    </div>
    
    
    <? /*
    <div class="dashboard-item" style="width: 860px;">
        <span>Statistici 2Parale</span>
    </div>    
    */ ?>
    
    <div class="clear"></div>
</div>



<?php
include "include/footer.php";
?>
