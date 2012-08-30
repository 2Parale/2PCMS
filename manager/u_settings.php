<?php
include "../common/common.php";
include "include/functions.php";

if(!userIsOk()){redirTo("login.php");}//checking user



if(isset($_GET["action"])){

    if($_GET["action"]=="update2"){
        
        $db->query("TRUNCATE TABLE settings");
        
        $arVariables = $_POST["set-name"];
        $arValues = $_POST["set-value"];
        
        for($i=0; $i<count($arVariables); $i++){
            $db->query("Insert Into settings (vlabel, vvalue) Values ('".$arVariables[$i]."', '".$arValues[$i]."')");            
        }        
        
        $_SESSION["response_msg"] = "Setarile au fost salvate!";
        
        redirTo("u_settings.php");
    }
    
    if($_GET["action"]=="redo-urls"){
        
        //products
        $products = $db->get_results("Select id, title from shop_products");
        if($products!=null){
            foreach($products as $prod){
                $db->query("Update LOW_PRIORITY shop_products set title_url='".text_to_url($prod->title)."' where id=".$prod->id);
            }
        }
                
        //categories
        $categories = $db->get_results("Select id, category from shop_categories");
        if($categories!=null){
            foreach($categories as $cat){
                $db->query("Update LOW_PRIORITY shop_categories set category_url='".text_to_url($cat->category)."' where id=".$cat->id);
            }
        }
                
        //brands
        $brands = $db->get_results("Select id, brand from shop_brands");
        if($brands!=null){
            foreach($brands as $brand){
                $db->query("Update LOW_PRIORITY shop_brands set brand_url='".text_to_url($brand->brand)."' where id=".$brand->id);
            }
        }
        
        $_SESSION["response_msg"] = "URL-urile au fost re-generate!";
        
        redirTo("u_settings.php");        
    }
    
}


$page_name = "u_settings";
$page_title = "Setari CMS";
include "include/header.php";
?>

<form name="edit-settings" id="edit-settings" method="post" action="u_settings.php?action=update2">

<h3>Setari generale website</h3>
<?
$content = scandir("../templates/");
$bad = array(".", "..");
$arTemplates = array_diff($content, $bad);

echo getSettingsFormElement('website_title', '', 'Titlu website', 'text', null, null);
echo getSettingsFormElement('meta_keywords', '', 'Meta keywords', 'text', null, "continut de baza pentru tag-ul meta keywords");
echo getSettingsFormElement('meta_description', '', 'Meta description', 'text', null, "continut de baza pentru tag-ul meta description");
echo getSettingsFormElement('website_email', '', 'Adresa email', 'text', null, "adresa de email folosita pentru comunicarea interna a CMS-ului");
echo getSettingsFormElement('website_template', 'classic', 'Template', 'select', $arTemplates, "template pentru website (se gasesc in folder /templates)");
echo getSettingsFormElement('website_ga_id', '', 'Google Analytics ID', 'text', null, "de forma UA-123456-78, il obtineti din contul Google Analytics");
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Continut parteneri</h3>
<?
$arTrueFalse = array('da', 'nu');
echo getSettingsFormElement('partner_logo_width', '', 'Latime maxima logo partener', 'number', null, null);
echo getSettingsFormElement('partner_show', 'nu', 'Afiseaza detalii partener', 'select', $arTrueFalse, "Selecteaza 'da' pentru a afisa pe pagina produsului descrierea si logo-ul partenerului");
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Procesare imagini produse</h3>
<?
echo getSettingsFormElement('img_small_width', '', 'Dimensiune imagine mica - width', 'number', null, "imaginea mica apare in listari");
echo getSettingsFormElement('img_small_height', '', 'Dimensiune imagine mica - height', 'number', null, null);
echo getSettingsFormElement('img_big_width', '', 'Dimensiune imagine mare - width', 'number', null, "imaginea mare apare pe pagina produsului");
echo getSettingsFormElement('img_big_height', '', 'Dimensiune imagine mare - height', 'number', null, null);
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Setari URL</h3>
<?
$arTrueFalse = array('da', 'nu'); 
$arURLFormer = array('_', '-'); 
echo getSettingsFormElement('mask_redirect', 'nu', 'Link afiliat mascat', 'select', $arTrueFalse, "selecteaza 'nu' pentru a afisa linkul de afiliat<br />selecteaza 'da' pentru a afisa un link de forma http://domeniu/<b>cumpara</b>/produs.html");
echo getSettingsFormElement('slug_price', '', 'URL pret', 'text', null, "URL-ul pentru filtrarile in functie de pret sunt de forma http://domeniu/preturi-<b>[slug]</b>/ iar aici se seteaza acest slug (trebuie sa contina numai caractere a-z)");
echo getSettingsFormElement('url_former', '_', 'Caracter URL', 'select', $arURLFormer, "Caracterul folosit pentru transformarea textelor in url-uri (ex: din 'titlul meu' in 'titlul-meu') <a href='u_settings.php?action=redo-urls'><b>re-genereaza url-uri</b></a>");
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Afisare produse</h3>
<?
echo getSettingsFormElement('produse_index_noi_count', '', 'Numar produse noi - index', 'number', null, "numarul de produse <b>noi</b> care se afiseaza pe prima pagina");
echo getSettingsFormElement('produse_index_visited_count', '', 'Numar produse populare - index', 'number', null, "numarul de produse <b>populare</b> care se afiseaza pe prima pagina");
echo getSettingsFormElement('record_per_page', '', 'Numar produse pe pagina', 'number', null, "numarul de produse care apare pe paginile de listare");
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Afisare categorii</h3> 
<?
$arTrueFalse = array('da', 'nu');
echo getSettingsFormElement('show_zero_count_categories', 'nu', 'Afisare categorii cu 0 produse', 'select', $arTrueFalse, "selecteaza 'da' pentru a afisa categoriile in meniu chiar daca nu au produse active asociate");
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Setari continut</h3> 
<?
$arTrueFalse = array('da', 'nu');
echo getSettingsFormElement('lp_show_sidebar_box', 'nu', 'Afisare landing pages in sidebar', 'select', $arTrueFalse, "selecteaza 'da' pentru a afisa in sidebar un box cu landing page-urile definite");
echo getSettingsFormElement('lp_sidebar_count', '4', 'Numar LP in sidebar', 'number', null, "numarul de landing page-uri pe care le afisam in sidebar daca optiunea anterioara=da");
echo getSettingsFormElement('article_categories_sidebar_box', 'nu', 'Afisare categorii articole in sidebar', 'select', $arTrueFalse, "selecteaza 'da' pentru a afisa in sidebar un box cu categoriile de articole definite");
echo getSettingsFormElement('article_sidebar_box', 'nu', 'Afisare articole in sidebar', 'select', $arTrueFalse, "selecteaza 'da' pentru a afisa in sidebar un box cu cele mai recente articole adaugate");
echo getSettingsFormElement('article_sidebar_count', '4', 'Numar articole in sidebar', 'number', null, "numarul de articole ce se afiseaza in sidebar");
echo getSettingsFormElement('article_product_page_box', 'nu', 'Afisare articole in pagina produsului', 'select', $arTrueFalse, "selecteaza 'da' pentru a afisa in pagina produsului articolele relationate");

?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Setari footer</h3> 
<?
echo getSettingsFormElement('footer_categories', '6', 'Numar categorii in footer', 'number', null, "numarul de categorii care se afiseaza in footer");
echo getSettingsFormElement('footer_products', '6', 'Numar produse in footer', 'number', null, "numarul de produse care se afiseaza in footer");
echo getSettingsFormElement('footer_searches', '20', 'Numar keywords in footer', 'number', null, "numarul de keywords care se afiseaza in footer");
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Integrare FB <img src="<?
@$fbimg = $db->get_var("Select vvalue from settings where vlabel='facebook_icon_file'");
if(isset($fbimg) && !is_null($fbimg)){echo "../images/social-icons/24/" . $fbimg . "_24.png";}
?>" id="fbimg" width="24" height="24"></h3>
<?
$arFacebookFiles = array();
$arTwitterFiles = array();
$handler = opendir("../images/social-icons/16/");
while ($file = readdir($handler)) {
    if ($file != "." && $file != "..") {        
        if(strpos($file,"social_facebook")===0){
            $arFacebookFiles[] = str_replace("_16.png","",$file);
        }
        if(strpos($file,"social_twitter")===0){
            $arTwitterFiles[] = str_replace("_16.png","",$file);
        }
        
    }    
}
closedir($handler);

$arIconSizes = array('16','24','32','48','64');
echo getSettingsFormElement('facebook_link', '', 'Link pagina Facebook', 'text', null, null);
echo getSettingsFormElement('facebook_embed', '', 'Widget Facebook', 'bigtext', null, "widgetul va fi afisat pe sidebar");
echo getSettingsFormElement('facebook_icon_file', '', 'Icon Facebook - fisier', 'select', $arFacebookFiles, null, "updateSettingsImage('|objid|','fbimg')", 'onChange');
echo getSettingsFormElement('facebook_icon_size', '24', 'Icon Facebook - marime', 'select', $arIconSizes, null);
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<h3>Integrare Twitter <img src="<?
@$twimg = $db->get_var("Select vvalue from settings where vlabel='twitter_icon_file'");
if(isset($twimg) && !is_null($twimg)){echo "../images/social-icons/24/" . $twimg . "_24.png";}
?>" id="twimg" width="24" height="24"></h3>
<?
echo getSettingsFormElement('twitter_link', '', 'Link pagina Twitter', 'text', null, null);
echo getSettingsFormElement('twitter_icon_file', '', 'Icon Twitter - fisier', 'select', $arTwitterFiles, null, "updateSettingsImage('|objid|','twimg')", 'onChange');
echo getSettingsFormElement('twitter_icon_size', '24', 'Icon Twitter - marime', 'select', $arIconSizes, null);
?>

<div class="spacer10"></div>
<div class="spacer10"></div>

<div style="text-align: right;">
    <a href="javascript:void(0)" onclick="$('#edit-settings').submit();" class="button"><span>Salveaza modificarile</span></a>
    <a href="javascript:void(0)" onclick="$('#edit-settings')[0].reset();" class="button"><span>Anuleaza modificarile</span></a>
    <div class="clear"></div>         
</div>  

<div class="spacer10"></div>
<div class="spacer10"></div>
<div class="spacer10"></div>
<div class="spacer10"></div>

</form>

<script type="text/javascript">
    function updateSettingsImage(objId, objImg){
        $('#'+objImg).attr('src',"../images/social-icons/24/" + $('#'+objId).val() + "_24.png");
    }
</script>

<?php
include "include/footer.php";
?>
