<?php


function getSettingsFormElement($var_name, $default_value, $label, $type, $arValues, $description, $jsFunction = "", $jsFunctionEvent = ""){
    $retVal = "";
    $jsStuff = "";
    global $db;
    
    if($jsFunction!="" && $jsFunctionEvent!=""){
        $jsStuff = $jsFunctionEvent . '="' . str_replace("|objid|","set-name-".$var_name,$jsFunction) . '" ';
    }
    
    $retVal = '<div class="form_item" style="background-color: #f2f2f2; padding: 3px 0px; margin-bottom: 5px;" >' . "\n\r";
    $retVal .= '<input type="hidden" name="set-name[]" value="'.$var_name.'">';
    $retVal .= '<label for="set-name-'.$var_name.'" style="width: 300px;">';
    $retVal .= $label;
    $retVal .= '</label>' . "\n\r";
    @$db_value = $db->get_var("Select vvalue from settings where vlabel='$var_name'");        
    
    //type number
    if($type=="number"){
        $retVal .= '<input '.$jsStuff.' type="text" name="set-value[]" id="set-name-'.$var_name.'" style="width: 50px; text-align: right; float: left;" value="';    
        if(isset($db_value) && !is_null($db_value)){
            $retVal .= $db_value;
        }else{
            $retVal .= $default_value;
        }
        $retVal .= '">' . "\n\r";
    }

    //type text
    if($type=="text"){
        $retVal .= '<input '.$jsStuff.' type="text" name="set-value[]" id="set-name-'.$var_name.'" style="width: 200px; float: left;" value="';    
        if(isset($db_value) && !is_null($db_value)){
            $retVal .= $db_value;
        }else{
            $retVal .= $default_value;
        }
        $retVal .= '">' . "\n\r";
    }    
    
    //type bigtext
    if($type=="bigtext"){
        $retVal .= '<textarea '.$jsStuff.' name="set-value[]" id="set-name-'.$var_name.'" style="width: 300px; float: left;" rows="3">';
        if(isset($db_value) && !is_null($db_value)){
            $retVal .= $db_value;
        }else{
            $retVal .= $default_value;
        }        
        $retVal .= '</textarea>';
    }
    
    //type select
    if($type=="select"){
        if(isset($db_value) && !is_null($db_value)){
            $selected_value = $db_value;
        }else{
            $selected_value = $default_value;
        }         
        $retVal .= '<select '.$jsStuff.' name="set-value[]" id="set-name-'.$var_name.'" style="float: left; width: 200px;">';
        //$retVal .= '<option value=""></option>';
        foreach($arValues as $iVal){
            $retVal .= '<option value="'.$iVal.'"';
            if($iVal==$selected_value){$retVal .= 'selected="selected"';}
            $retVal .= '>'.$iVal.'</option>';
        }
        $retVal .= '</select>';
    }    
    
    //description
    if(isset($description) && !is_null($description)){
        $retVal .= '<div style="float: left; width: 350px; font-size: 10px; text-decoration: italic; margin-left: 10px;">'.$description.'</div>';
    }
    
    $retVal .= '<div class="clear"></div>'  . "\n\r";
    $retVal .= "</div>" . "\n\r";
    return $retVal;
}

?>