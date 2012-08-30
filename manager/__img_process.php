<?php
include "../common/common.php"; 
require_once('include/phpthumb/phpthumb.class.php');
error_reporting(0);

$prod = $db->get_row("Select * from shop_products where local_img_small='' order by id asc limit 1");

$s_w = $_CONFIG["settings"]["img_small_width"]; 
$s_h = $_CONFIG["settings"]["img_small_height"];
$b_w = $_CONFIG["settings"]["img_big_width"]; 
$b_h = $_CONFIG["settings"]["img_big_height"];

if($prod!=null){
    
    
//Signal what we do
$todo = (int)$_GET["todo"];
$processing_msg = "Procesare :: [" . $prod->id . "] " . $prod->title . "<br />&raquo;&raquo;&raquo; " . $todo . " de procesat";


$arImage = explode(".",$prod->img_url);
$fname = "imagine_" . trim($prod->title_url,"_") . "_" . $prod->id . "." . $arImage[count($arImage)-1];                
file_put_contents("../product_images/_temp/" . $fname, file_get_contents($prod->img_url));

$destination_folder = $prod->partner_id . "/" . $prod->category_id . "/";
if (!file_exists("../product_images/".$prod->partner_id)) {mkdir("../product_images/".$prod->partner_id);chmod("../product_images/".$prod->partner_id."/", 0775);}
if (!file_exists("../product_images/".$prod->partner_id."/".$prod->category_id)) {mkdir("../product_images/".$prod->partner_id."/".$prod->category_id); chmod("../product_images/".$prod->partner_id."/".$prod->category_id."/", 0775);}
$format = "jpg";

//thumb big
$uploadedfile = "../product_images/_temp/".$fname;
$uploadedfileThumb = "../product_images/".$destination_folder.$fname;
$size=getimagesize( $uploadedfile );                            
$phpThumb = new phpThumb();                        
$phpThumb->config_output_format = $format;
$phpThumb->src = $uploadedfile;
$phpThumb->w = $b_w;
$phpThumb->h = $b_h;
if ($size[0]>$size[1]){
    $phpThumb->setParameter("far", "L");
} else {
    $phpThumb->setParameter("far", "C");
}                        
$phpThumb->setParameter("bg", "ffffff");            
$phpThumb->GenerateThumbnail();
$phpThumb->RenderToFile( $uploadedfileThumb );        

//thumb small
$uploadedfile = "../product_images/_temp/".$fname;
$uploadedfileThumb = "../product_images/".$destination_folder."small_".$fname;
$size=getimagesize( $uploadedfile );                            
$phpThumb = new phpThumb();                        
$phpThumb->config_output_format = $format;
$phpThumb->src = $uploadedfile;
$phpThumb->w = $s_w;
$phpThumb->h = $s_h;
if ($size[0]>$size[1]){
    $phpThumb->setParameter("far", "L");
} else {
    $phpThumb->setParameter("far", "C");
}                        
$phpThumb->setParameter("bg", "ffffff");            
$phpThumb->GenerateThumbnail();
$phpThumb->RenderToFile( $uploadedfileThumb );         


//stergem imaginea temporara
@unlink("../product_images/_temp/" . $fname);

if(file_exists("../product_images/".$destination_folder."small_".$fname) && file_exists("../product_images/".$destination_folder.$fname)){
    $db->query("Update shop_products set local_img_small='".$destination_folder."small_".$fname."', local_img_big='".$destination_folder.$fname."' where id=".$prod->id);
} else {
    $db->query("Update shop_products set local_img_small='default-image.jpg', local_img_big='default-image.jpg' where id=".$prod->id);
}



}//end if $prod!=null

$todo = $todo - 1;
if($todo>0){    
    ?>
    <script type="text/javascript">
        window.parent.callFromIframe_Update("<?=$processing_msg?>", <?=$todo?>);
        window.open('__img_process.php?todo='+<?=$todo?>,'img_status');
    </script>
    <?php
} else {
    $_SESSION["response_msg"] = "Imaginile au fost procesate!";
    ?>
    <br />
    <script type="text/javascript">
        window.parent.callFromIframe_Update('Procesare incheiata'); 
        window.parent.callFromIframe_Refresh();
    </script>        
    <?php
}
?>