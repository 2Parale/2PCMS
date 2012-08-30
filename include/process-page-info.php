<?php
//header info
if($_CONFIG["settings"]["show_zero_count_categories"]=="da"){$cats = $db->get_results("Select SQL_CACHE * from shop_categories where parent_id=0 order by category asc");}else{$cats = $db->get_results("Select SQL_CACHE * from shop_categories where parent_id=0 and pa_count>0 order by category asc");}
$prices = $db->get_results("Select SQL_CACHE * from shop_priceranges order by vmin asc");
$brands = $db->get_results("Select SQL_CACHE * from shop_brands where prod_count>0 order by shows desc, prod_count desc limit 10");
if($_CONFIG["settings"]["lp_show_sidebar_box"]=="da"){$sidebar_lps = $db->get_results("Select SQL_CACHE id, lp_url, new_title from landing_pages order by pubdate desc limit " . (int)$_CONFIG["settings"]["lp_sidebar_count"]);}
if($_CONFIG["settings"]["article_categories_sidebar_box"]=="da"){$acategories = $db->get_results("Select SQL_CACHE category, category_url from article_categories where article_count>0 order by position asc");}
if($_CONFIG["settings"]["article_sidebar_box"]=="da"){$articles = $db->get_results("Select SQL_CACHE a.title, a.title_url, a.id, b.category_url from articles a left join article_categories b on a.category_id=b.id order by a.pubdate desc limit ".(int)$_CONFIG["settings"]["article_sidebar_count"]);}


//footer info
$fcats = $db->get_results("Select SQL_CACHE * from shop_categories order by vcount desc limit ".$_CONFIG["settings"]["footer_categories"]);
$fprods = $db->get_results("Select SQL_CACHE * from shop_products where active=1 order by click*10+show_inpage*5+show_inlisting desc limit ".$_CONFIG["settings"]["footer_products"]);
$searches = $db->get_results("Select SQL_CACHE * from shop_searches where active=1 order by scount desc limit ".$_CONFIG["settings"]["footer_searches"]);
$les = $db->get_results("Select SQL_CACHE p_link_caption, p_link_title, p_link_href from le_partners where active=1 and last_status=1");

?>