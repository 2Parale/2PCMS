<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=$page_title?></title>
<link rel="stylesheet" href="<?=$_CONFIG["urlpath"]?>templates/<?=$_CONFIG["template"]?>/style.css"/>
<script type="text/javascript" src="<?=$_CONFIG["urlpath"]?>common/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?=$_CONFIG["urlpath"]?>js/functions.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?=$_CONFIG["urlpath"]?>" target="_self" />
<? if(isset($canonical_url) && $canonical_url!=""){?><link rel="canonical" href="<?=$canonical_url?>" /><?} ?>
<meta name="keywords" content="<?=$page_metakey?><?=$_CONFIG["base_meta_key"]?>" />
<meta name="description" content="<?=$page_metadesc?><?=$_CONFIG["base_meta_desc"]?>" />
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?=$_CONFIG["settings"]["website_ga_id"]?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>

<? /** START PAGE CONTENT**/?>


<? /** HEADER START **/?>
<div id="header-wrapper">
    <div id="search_box">
        <form name="search" method="get" action="<?=$_CONFIG["urlpath"]?>search.php" onsubmit="if($('#keys').val()=='[Cauta ...]'){return false;}">
            <input type="hidden" name="action" value="prepare"/>
            <input type="text" class="search search-off" name="keys" id="keys" value="[Cauta ...]" onfocus="searchBoxFocus()" onblur="searchBoxBlur()"/>
            <input type="submit" style="width: 0px; height: 0px; padding: 0px; margin: 0px; border: 0px;" value="">
        </form>
    </div>
    <span><a href="<?=$_CONFIG["urlpath"]?>" title="<?=$_CONFIG["site_name"]?>"><img src="<?=$_CONFIG["urlpath"]?>templates/<?=$_CONFIG["template"]?>/images/felogo.jpg" alt="<?=$_CONFIG["settings"]["website_title"]?>"/></a></span>
    <div class="clear"></div>
</div>
<? /** HEADER END **/?>



<? /** CONTENT WRAPPER START (ends in footer.php) **/?> 
<div id="content-wrapper">

    <? /** LEFT SIDEBAR START **/?> 
    <div id="sidebar-left">
        
        <? /** CATEGORIES START **/?> 
        <div id="menu">
            <h2>Categorii</h2>
            <?            
            if($cats!=null){
                foreach($cats as $cat){
                    ?>
                    <h3><a href="<?=$_CONFIG["urlpath"]?><?=$cat->category_url?>/" title="<?=$cat->category?>"><?=$cat->category?></a></h3>
                    <?
                }
            }
            ?>            
        </div>
        <? /** CATEGORIES END **/?> 
        
        <div class="spacer10"></div>
        
        
        <? /** PRICES START **/?> 
        <div id="prices">
            <h2>Interval de pret</h2>
            <?            
            if($prices!=null){
                foreach($prices as $price){
                    ?>
                    <h4><a href="<?=$_CONFIG["urlpath"]?>preturi-<?=$_CONFIG["settings"]["slug_price"]?>/<?=$price->vmin?>-<?=$price->vmax?>/" title="<?=$price->label?>"><?=$price->label?></a></h4>
                    <?
                }
            }
            ?>
        </div>
        <? /** PRICES END **/?> 
        
                
                
        
        
        
        <? /** BRANDS START **/?> 
        <div id="brands">
            <h2>Alege marca</h2>        
            <?            
            if($brands!=null){
                foreach($brands as $brand){
                    ?>
                    <h4><a href="<?=$_CONFIG["urlpath"]?>brand/<?=$brand->brand_url?>/" title="<?=$brand->brand?>"><?=$brand->brand?></a></h4>
                    <?
                }
            }
            ?>            
        </div>
        <? /** BRANDS END **/?> 
        
        
        <? /** ARTICLE CATEGORIES START **/?> 
        <? if($_CONFIG["settings"]["article_categories_sidebar_box"]=="da"){
            if($acategories!=null){
                ?>
        <div id="acategories">
            <h2>Categorii articole</h2>                        
                <?
                foreach($acategories as $acategory){
                    ?>
                    <h4><a href="<?=$_CONFIG["urlpath"]?>articole/<?=$acategory->category_url?>/" title="<?=$acategory->category?>"><?=$acategory->category?></a></h4>
                    <?
                }
                ?>
        </div>                
                <?
            }
        } ?>
        <? /** ARTICLE CATEGORIES END **/?> 
        
               
        <? /** ARTICLES START **/?> 
        <? if($_CONFIG["settings"]["article_sidebar_box"]=="da"){
            if($articles!=null){
                ?>
        <div id="articles">
            <h2>Articole recente</h2>                        
                <?
                foreach($articles as $article){
                    ?>
                    <h4><a href="<?=$_CONFIG["urlpath"]?>articole/<?=$article->category_url?>/<?=$article->title_url?>-<?=$article->id?>.html" title="<?=$article->title?>"><?=$article->title?></a></h4>
                    <?
                }
                ?>
        </div>                
                <?
            }
        } ?>
        <? /** ARTICLES END **/?>
        
        
        <? /** LANDING PAGES START **/?> 
        <? if($_CONFIG["settings"]["lp_show_sidebar_box"]=="da"){
            if($sidebar_lps!=null){
                ?>
        <div id="articles">
            <div class="spacer10"></div>
            <h2>Pagini speciale de produs</h2>
                <?
                foreach($sidebar_lps as $slp){
                    ?>
                    <h4><a href="<?=$_CONFIG["urlpath"]?>lp/<?=$slp->lp_url?>-<?=$slp->id?>.html" title="<?=$slp->new_title?>"><?=$slp->new_title?></a></h4>
                    <?
                }
                ?>
        </div>                
                <?
            }
        } ?>
        <? /** LANDING PAGES END **/?>          
                
                
    </div>
    
    <? /** CONTENT ZONE START (ends in footer.php)**/?> 
    <div id="content-zone">
    
    
    <? /** BREADCRUMS START **/?> 
    <? if($pagePath!=""){?>
    <div id="breadcrumb"><? displayPagePath(); ?></div>
    <?}?>
    <? /** BREADCRUMBS END **/?> 
    
    
    <? /** PAGE HEADING START **/?> 
    <? if($page_heading!=""){?>
    <h1><?=$page_heading?></h1>    
    <?}?>
    <? /** PAGE HEADING END **/?> 
    <?if($listing_description!=""){?>
    <div class="listing-description">
        <?=stripslashes($listing_description)?>
    </div>
    <?}?>