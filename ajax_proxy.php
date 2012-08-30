<?php
include "common/common.php"; 

//handle ajax requests
if(isset($_GET["action"])){
    
    if($_GET["action"]=="mark_product_visit"){
        $id = (int)$_GET["id"];
        if($id>0){
            $db->query("Update shop_products set click=click+1 where id=".$id);            
        }        
    }
}
?>