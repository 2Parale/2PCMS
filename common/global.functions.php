<?php 
error_reporting(0);

//building breadcrumb function
/*--START--*/ 
$pagePathRoot = '<a href="'.$_CONFIG["urlpath"].'" target="_self">'.$_CONFIG["site_name"].'</a>';
$pagePath = '';
$pagePathSeparator = '<strong>&nbsp;&raquo;&nbsp;</strong>';

function addPagePathLink ($sLink, $sDisplay, $bDisplayBefore = false){
    global $pagePath;
    global $pagePathSeparator;
    if ($bDisplayBefore == false) {
        $pagePath = $pagePath . '<a href="' . $sLink . '" target="_self">' . $sDisplay . '</a>' . $pagePathSeparator;
    } else {
        $pagePath = '<a href="' . $sLink . '" target="_self">' . $sDisplay . '</a>' . $pagePathSeparator . $pagePath ;
    }    
}

function addPagePathText ($sDisplay){
    global $pagePath;
    $pagePath = $pagePath . $sDisplay;
}

function displayPagePath(){
    global $pagePath;
    global $pagePathRoot;
    global $pagePathSeparator;
    echo $pagePathRoot . $pagePathSeparator . $pagePath;
}
//building breadcrumb function
/*--END--*/ 


//DEFINE AT RUNTIME WHAT FILES TO BE INCLUDED AS "JS" OR "CSS" IN THE CURRENT FILE
/*--START--*/
$ar_suplemental_js = array();
$ar_suplemental_css = array();

function parseClassDep2IncludeFile($basePath, $arClassDep){
    if ($arClassDep!=null) {
        if ($arClassDep['js_count']!=0) {
            for ($i=1; $i<=$arClassDep['js_count']; $i++) {
                addSuplementalIncludeFile ($basePath.$arClassDep['js'][$i],"js");
            }
        }
        if ($arClassDep['css_count']!=0) {
            for ($i=1; $i<=$arClassDep['css_count']; $i++) {
                addSuplementalIncludeFile ($basePath.$arClassDep['css'][$i],"css");
            }
        }        
    }
}

function addSuplementalIncludeFile ($file_name, $file_type){     //$file_type = css | js
    global $ar_suplemental_js;
    global $ar_suplemental_css;

    if ($file_type=='css') {
        $ar_suplemental_css[count($ar_suplemental_css)+1] = $file_name;
    }    

    if ($file_type=='js') {
        $ar_suplemental_js[count($ar_suplemental_js)+1] = $file_name;
    }    
}  

function includeFiles ($file_type=""){//$file_type = css | js | null for all
    global $ar_suplemental_js;
    global $ar_suplemental_css;
    
    //INCLUDE CSS FILES
    if($file_type=="" or $file_type=="css"){
        if (count($ar_suplemental_css)>0) {
            foreach ($ar_suplemental_css as $supl_css){
                ?> <link rel="stylesheet" type="text/css" href="<?php echo $supl_css; ?>"> <?php
            }
        }
    }
    
    if($file_type=="" or $file_type=="js"){
        if (count($ar_suplemental_js)>0) {
            foreach ($ar_suplemental_js as $supl_js){
                ?> <script type="text/javascript" src="<?php echo $supl_js;?>"></script> <?php
            }
        }    
    }
}
/*--END--*/



//REDIRECT FUNCTION
/*--START--*/
function redirTo($sUrl){
    header ("location: $sUrl");
    exit();
}
/*--END--*/


//PRINT ARRAY FUNCTION
/*--START--*/
function printArray($arObject){
    echo "<pre>";
    print_r ($arObject);
    echo "</pre><br />";
}
/*--END--*/


//EMAIL VALIDATION FUNCTION
/*--START--*/
function isValidEmail($address){
  // check an email address is possibly valid
  $result = TRUE;
  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $address)) {
    $result = FALSE;
  }
  return $result;
}  
/*--END--*/

//DATE FUNCTIONS
/*--START--*/

function dateTransform_MonthToName($iMonthNumber){
    $arMonths = array();
    $arMonths[1] = "Ianuarie";
    $arMonths[2] = "Februarie";
    $arMonths[3] = "Martie";
    $arMonths[4] = "Aprilie";
    $arMonths[5] = "Mai";
    $arMonths[6] = "Iunie";
    $arMonths[7] = "Iulie";
    $arMonths[8] = "August";
    $arMonths[9] = "Septembrie";
    $arMonths[10] = "Octombrie";
    $arMonths[11] = "Noiembrie";
    $arMonths[12] = "Decembrie";
    
    return $arMonths[$iMonthNumber];
}


/* TEXT TO URL*/
function text_to_url($input_text){
    global $_CONFIG;
    return trim(strtolower(preg_replace("/[^0-9a-zA-Z]+/", $_CONFIG["settings"]["url_former"], $input_text)),$_CONFIG["settings"]["url_former"]);
}



/* Pagination helper*/
$arPaginationElements = array();
function addPaginationElement($type, $caption, $link="", $title="", $class=""){
    global $arPaginationElements;
    $arItem = array();
    $arItem["type"] = $type;
    $arItem["caption"] = $caption;
    $arItem["link"] = $link;
    $arItem["title"] = $title;
    $arItem["class"] = $class;
    $arPaginationElements[] = $arItem;
}

function getPaginationElements(){
    global $arPaginationElements;
    $return_string = "";        
    
    foreach($arPaginationElements as $pe){
        if($pe["type"]=="text"){$return_string.=$pe["caption"]." ";}
        if($pe["type"]=="link"){
            $class = "";
            if($pe["class"]!=""){$class = 'class="'.$pe["class"].'"';}
            $return_string.='<a href="'.$pe["link"].'" title="'.$pe["title"].'" '.$class.'>'.$pe["caption"].'</a> ';
        }
    }
    
    return $return_string;
}





//verificare user autentificat
function userIsOk(){
    $retVal = false;
        
    if(isset($_SESSION["admin_id"])){
        if((int)$_SESSION["admin_id"]>0){
            $retVal = true;
        }
    }
    
    return $retVal;
}

function displayServerMsg(){    
    if(isset($_SESSION["response_msg"])){
    if($_SESSION["response_msg"]!=""){
        echo '<div class="alert_container" id="myalert">';
        echo $_SESSION["response_msg"];
        echo '</div>';
        echo '<script type="text/javascript">';                
        echo 'function openMyAlert(){$("#myalert").slideDown("fast")}';
        echo "\n\r";
        echo 'function closeMyAlert(){$("#myalert").slideUp("normal")}';
        echo "\n\r";
        echo 'window.setTimeout("openMyAlert()", 500);';
        echo 'window.setTimeout("closeMyAlert()", 3500);';
        echo '</script>';
        $_SESSION["response_msg"] = "";
    }    
    }
}
?>