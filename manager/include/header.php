<html>
<head>
<title>2PCMS Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/admin.css">
<script language="javascript" src="js/functions.js"></script>
<script language="javascript" src="../common/jquery-1.3.2.min.js"></script>
<?php includeFiles("css") ?>
<?php includeFiles("js") ?>
</head>
<body>


<? displayServerMsg(); ?>

<div id="header">
<a href="<?=$_CONFIG["urlpath"]?>" title="2PCMS" target="_blank">2PCMS (<?=$cms_version?>)</a> / <a href="<?=$_CONFIG["urlpath"]?>manager/" title="2PCMS Admin">Admin</a>
<div class="clear"></div>
</div>

<div id="maincontainer">
<div id="menucontainer">
    <div class="mlinks" style="margin-bottom: 20px; padding: 5px; background-color: #f2f2f2;">
        <a href="index.php" <?php if($page_name=="index"){echo 'class="selected"';} ?>>Dashboard</a>
        <a href="login.php?action=logout" <?php if($page_name=="ww"){echo 'class="selected"';} ?>>Logout</a>       
    </div>

    <div class="mtitle">Sursa date</div>
    <div class="mlinks">
        <a href="sd_partners.php" <?php if($page_name=="sd_partners"){echo 'class="selected"';} ?>>Parteneri</a>
        <a href="sd_feeds.php" <?php if($page_name=="sd_feeds"){echo 'class="selected"';} ?>>Feed-uri</a>
        <a href="id_import.php" <?php if($page_name=="id_import"){echo 'class="selected"';} ?>>Import feed-uri</a>    
        <a href="sd_categories.php" <?php if($page_name=="sd_categories"){echo 'class="selected"';} ?>>Asociere categorii</a>        
        <a href="sd_products.php" <?php if($page_name=="sd_products"){echo 'class="selected"';} ?>>Import produse</a>        
    </div>
    
    <div class="mtitle">CMS - Produse</div>
    <div class="mlinks">
        <a href="s_products.php" <?php if($page_name=="s_products"){echo 'class="selected"';} ?>>Produse</a>    
        <a href="s_categories.php" <?php if($page_name=="s_categories"){echo 'class="selected"';} ?>>Categorii</a>        
        <a href="s_brands.php" <?php if($page_name=="s_brands"){echo 'class="selected"';} ?>>Branduri</a>        
        <a href="s_pricerange.php" <?php if($page_name=="s_pricerange"){echo 'class="selected"';} ?>>Price ranges</a>        
        <a href="s_texttrans.php" <?php if($page_name=="s_texttrans"){echo 'class="selected"';} ?>>Text transformation</a>        
    </div>    
    
    <div class="mtitle">CMS - Continut</div>
    <div class="mlinks">
        <a href="con_article_categories.php" <?php if($page_name=="con_article_categories"){echo 'class="selected"';} ?>>Categorii articole</a>        
        <a href="con_articles.php" <?php if($page_name=="con_articles"){echo 'class="selected"';} ?>>Articole</a>                
        <a href="con_landingpages.php" <?php if($page_name=="con_landingpages"){echo 'class="selected"';} ?>>Landing pages</a>        
    </div>
    
    <div class="mtitle">Utile</div>
    <div class="mlinks">
        <a href="u_settings.php" <?php if($page_name=="u_settings"){echo 'class="selected"';} ?>>Setari</a>
        <a href="u_stats.php" <?php if($page_name=="u_stats"){echo 'class="selected"';} ?>>Statistici</a>        
        <a href="u_seokeys.php" <?php if($page_name=="u_seokeys"){echo 'class="selected"';} ?>>SEO Keywords</a>
        <a href="u_linkex.php" <?php if($page_name=="u_linkex"){echo 'class="selected"';} ?>>Link exchange</a>
        <a href="u_users.php" <?php if($page_name=="u_users"){echo 'class="selected"';} ?>>Utilizatori</a>
        <a href="u_crons.php" <?php if($page_name=="u_crons"){echo 'class="selected"';} ?>>Cron-uri</a>
        
    </div>
    
    <div class="powered-by">powered by <a href="http://www.9a.ro/2pcms/" target="_blank">2PCMS</a> </div>
    
</div>
<div id="activecontainer">
<h1><?php echo $page_title; ?></h1>

