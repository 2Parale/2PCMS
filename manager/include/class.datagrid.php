<?php
/*
* DATAGRID CLASS v1.5
* Author: Daniel Buca
* Last change: 18.Dec.2007
* 
* Change log starting 18.Dec.2007
* 18.Dec.2007: 
*   -   adding new method GetDependencies that returns an array with all needed js and css 
*       files and their location, files that have to be included in the file where this class is used 
*/

class DataGrid{	
	
	private $eol = "\n\r";
    
	private $db = null;
	private $sqlString = "";
	private $sqlCountString = "";
	
	private $iColumnCount = 0;
	
	private $sIdField = "";
	
	private $iRecPerPage = 10;
	private $iCurPage = 1;
	
	private $arColumns = null;
	
	private $arRowStyles = null;
	
	private $arImages = null;
	
	private $sSelfLink = ""; //used for creating navigation
	
	private $iOptionCount = 0;
	private $arRowOptions = null;
	
	private $bShowPagination = true;
	private $sPaginationDisplay = "both"; //values accepted: up, bottom, both
	private $bShowSelectNavigation = true;
    private $bShowGotoNavigation = true;
    
	private $arLanguageDictionary = array();
	
	
	
	
	//CLASS CONSTRUCTOR
	public function __construct($db) {
		$this->db = $db;
		
		$this->arRowStyles["container"] = "table_container";
		$this->arRowStyles["paging_row"] = "td_pager";
		$this->arRowStyles["header"] = "td_header";
		$this->arRowStyles["row_first"] = "td_item_even";
		$this->arRowStyles["row_second"] = "td_item_odd";
		$this->arRowStyles["row_mouse_over"] = "td_item_mouseover";
		$this->arRowStyles["nav_link"] = "nav_link";
		$this->arRowStyles["option_link"] = "option_link";
		
		$this->arImages['image_prev'] = "";
		$this->arImages['image_next'] = "";
		
		$this->arLanguageDictionary['Pagination_CountInfo'] = "Avem #total_rows# inregistrari afisate cate #rec_per_page# pe pagina | ";
		$this->arLanguageDictionary['Pagination_NextLink'] = "next&raquo;";
		$this->arLanguageDictionary['Pagination_PrevLink'] = "&laquo;prev";
		$this->arLanguageDictionary['Pagination_CurrentPage'] = "pagina #cur_page# din #total_pages# ";
		$this->arLanguageDictionary['Pagination_SelectLabel'] = "Pagina: ";
		$this->arLanguageDictionary['Pagination_GotoLabel'] = "Mergi la pagina: ";
		$this->arLanguageDictionary['Pagination_GotoButton'] = "&raquo;";
		
	}				
	
	//--------------------------------------------------------------------------------
    // Getting an array of dependency files, both css and js
    //--------------------------------------------------------------------------------
    public function getDependencies () {
        $arTemp = array();
        $arTemp['js_count'] = 0;
        $arTemp['css_count'] = 1;
        $arTemp['css'][1] = "class.datagrid.dep/ajaxgrid.css";
        
        return $arTemp;
    }
    
    
	//--------------------------------------------------------------------------------
	//adding a new column
	//types can be:
	//- field
	//- counter
	//- options
    //- field_eval
	//TODO : ADD NEW FIELD TYPES
	//--------------------------------------------------------------------------------
	public function addColumn($columnLabel, $columnContent, $columnType, $columnWidth="100px", $columnAlign="center"){
		$this->iColumnCount++;
		$this->arColumns[$this->iColumnCount]['columnLabel'] = $columnLabel;
		$this->arColumns[$this->iColumnCount]['columnContent'] = $columnContent;
		$this->arColumns[$this->iColumnCount]['columnType'] = $columnType;
		$this->arColumns[$this->iColumnCount]['columnWidth'] = $columnWidth;
		$this->arColumns[$this->iColumnCount]['columnAlign'] = $columnAlign;
	}
	
	
	//add new option 
	//triggerEvent can be: js, html 
	public function addOption ($displayText, $displayImage, $Link, $triggerEvent){
		$this->iOptionCount++;
		$this->arRowOptions[$this->iOptionCount]['displayText'] = $displayText;
		$this->arRowOptions[$this->iOptionCount]['displayImage'] = $displayImage;
		$this->arRowOptions[$this->iOptionCount]['Link'] = $Link;
		$this->arRowOptions[$this->iOptionCount]['triggerEvent'] = $triggerEvent;
	}
	
	
	//--------------------------------------------------------------------------------	
	//MAIN METHOD - DRAW OF THE DATAGRID
	//--------------------------------------------------------------------------------	
	public function Draw(){
		//1. open container
		echo "<table class=\"" . $this->arRowStyles["container"] . "\" >" .$this->eol;
		
		//2. draw pagination
		if ($this->sPaginationDisplay=="up" or $this->sPaginationDisplay=="both") {
			$sqlLimiterString = $this->Draw_pagination();				
		}
		
		
		//3. draw header
		echo "<tr>" .$this->eol;
		for ($i=1; $i<=$this->iColumnCount; $i++){
			echo "<td width=\"" . $this->arColumns[$i]['columnWidth'] . "\" class=\"" . $this->arRowStyles["header"] . "\" align=\"" . $this->arColumns[$i]['columnAlign'] . "\">" .$this->eol;
			echo $this->arColumns[$i]['columnLabel'] .$this->eol;
			echo "</td>" .$this->eol;
		}
		echo "</tr>" .$this->eol;
		
		
		//4. draw records
		$records = $this->db->get_results ($this->sqlString . $sqlLimiterString, ARRAY_A);
		if ($records == null) {
			echo "<tr>" .$this->eol;
			echo "<td colspan=\"" . $this->iColumnCount . "\" class=\"" . $this->arRowStyles["row_first"] . "\" >" .$this->eol;
			echo "<em>Nu exista inregistrari</em>" .$this->eol;
			echo "</td>" .$this->eol;
			echo "</tr>" .$this->eol;
		} else { 
			$x = 0;
			foreach ($records as $record) {
				$x++;
				$row_class = "row_first";
				if ($x/2==(int)($x/2)) {$row_class = "row_second";}
                //opening row
				echo "<tr id=\"dg_id_$x\">" .$this->eol;
				for ($i=1; $i<=$this->iColumnCount; $i++) {
					
                    //opening cell
					echo "<td width=\"" . $this->arColumns[$i]['columnWidth'] . "\" class=\"" . $this->arRowStyles[$row_class] . "\" align=\"" . $this->arColumns[$i]['columnAlign'] . "\">" .$this->eol;
					
                    //HANDLE FIELD TYPES AND DISPLAY
                    
					//COLUMN TYPE = COUNTER
					if ($this->arColumns[$i]['columnType'] == "counter") {						
						echo (int)($x + ($this->iCurPage-1)*$this->iRecPerPage);						
					}
					
					//COLUMN TYPE = FIELD
					if ($this->arColumns[$i]['columnType'] == "field") {						
						$arFieldDefinition = explode ("|", $this->arColumns[$i]['columnContent']);
						foreach ($arFieldDefinition as $fieldItem) {							
							if (substr($fieldItem,0,1) == "#") {
								$fieldItem = str_replace("#","",$fieldItem);
								echo $fieldItem;
							} else {
								echo nl2br($record[$fieldItem]);
							}
						}
					}
					
                    //COLUMN TYPE = FIELD_EVAIL
                    if ($this->arColumns[$i]['columnType'] == "field_eval") {
                        $arFieldDefinition = explode ("|", $this->arColumns[$i]['columnContent']);
                        $eval_string = "";
                        foreach ($arFieldDefinition as $fieldItem) {                            
                            if (substr($fieldItem,0,1) == "#") {                            
                                $fieldItem = str_replace("#","",$fieldItem);
                                $eval_string = $eval_string . $record[$fieldItem];
                            } else {
                                $eval_string = $eval_string . $fieldItem;
                            }
                        }
                        //echo $eval_string;
                        eval ($eval_string);
                    }                    
                    
					//COLUMN TYPE = OPTIONS
					if ($this->arColumns[$i]['columnType'] == "options") {
						for ($opc=1; $opc<=$this->iOptionCount; $opc++) {
						
							if ($this->bShowPagination ==  true) {
								$opUrl = str_replace ("#replace#","&curPage=" . $this->iCurPage . "&" . $this->sIdField . "=" . $record[$this->sIdField],$this->arRowOptions[$opc]['Link']);
							} else {
								$opUrl = str_replace ("#replace#", "&" . $this->sIdField . "=" . $record[$this->sIdField],$this->arRowOptions[$opc]['Link']);
							}
							
							if ($this->arRowOptions[$opc]['triggerEvent'] == "js") {
								echo "<a href=\"#\" onClick=\"" . $opUrl . "\" class=\"" . $this->arRowStyles["option_link"] . "\" title=\"" . $this->arRowOptions[$opc]['displayText'] . "\">";
							} elseif ($this->arRowOptions[$opc]['triggerEvent'] == "html") {
								echo "<a href=\"" . $opUrl . "\" class=\"" . $this->arRowStyles["option_link"] . "\" target=\"_self\" title=\"" . $this->arRowOptions[$opc]['displayText'] . "\">";
							}
							
							if ($this->arRowOptions[$opc]['displayImage'] == "") { //no display image -> we build text link
								echo $this->arRowOptions[$opc]['displayText'];
							} else {//we build image link
								echo "<img src=\"" . $this->arRowOptions[$opc]['displayImage'] . "\" alt=\"" . $this->arRowOptions[$opc]['displayText'] . "\" border=\"0\">";
							} 
							
							echo "</a>";
							echo "&nbsp;" . $this->eol;
						}						
					}
					//closing cell
					echo "</td>" .$this->eol;
				}
				//closing row
				echo "</tr>" .$this->eol;
			}
		}
		
		//show pagination one more time START
		if ($this->sPaginationDisplay=="bottom" or $this->sPaginationDisplay=="both") {
			$this->Draw_pagination();
		}

		
		
		//5. close container 
		echo "</table>" .$this->eol;
	}
	
	
	//---------------------------------------------------------
	//PAGINATION METHOD
	//---------------------------------------------------------	
	private function Draw_pagination (){
		$sqlLimiterString = "";
		if ($this->bShowPagination ==  true) {
			$recCount = $this->db->get_var ($this->sqlCountString);
			$pagesCount = ceil($recCount/$this->iRecPerPage);
			
			$startLimit = ($this->iCurPage - 1) * $this->iRecPerPage;
			$countLimit = $this->iRecPerPage;
			
			echo "<tr>" . $this->eol;
			echo "<td colspan=\"" . $this->iColumnCount . "\" class=\"" . $this->arRowStyles["paging_row"] . "\">" . $this->eol;
            
            
            //display counting info
			$s_count_info = str_replace('#total_rows#',$recCount,$this->arLanguageDictionary['Pagination_CountInfo']);
			$s_count_info = str_replace('#rec_per_page#',$this->iRecPerPage,$s_count_info);
			echo $s_count_info;
			
			
			//construire paginatie - prev link
            $prev_label = $this->arLanguageDictionary["Pagination_PrevLink"];
			if ($this->iCurPage > 1) {
				$prevLink = $this->sSelfLink . "&curPage=" . (int)($this->iCurPage - 1);
				if ($this->arImages['image_prev'] == "") {
					echo "<a href=\"" . $prevLink . "\" class=\"" . $this->arRowStyles["nav_link"] . "\" title=\"$prev_label\">$prev_label</a>" . $this->eol;
				} else {
					echo "<a href=\"" . $prevLink . "\" title=\"$prev_label\"><img border=\"0\" alt=\"$prev_label\" src=\"" . $this->arImages['image_prev'] . "\"></a>" . $this->eol;
				}
			} else {
				if ($this->arImages['image_prev'] == "") {
					echo "<strong>$prev_label</strong>" . $this->eol;
				} else {
					echo "<img border=\"0\" alt=\"$prev_label\" src=\"" . $this->arImages['image_prev'] . "\">" . $this->eol;
				}
			}
			
			//pagina curenta
            $s_cur_page = str_replace('#cur_page#',$this->iCurPage,$this->arLanguageDictionary["Pagination_CurrentPage"]);
            $s_cur_page = str_replace('#total_pages#',$pagesCount,$s_cur_page);
            echo $s_cur_page;			
			
            
			//construire paginatie - next link
            $next_label = $this->arLanguageDictionary["Pagination_NextLink"];
			if ($this->iCurPage < $pagesCount) {
				$nextLink = $this->sSelfLink . "&curPage=" . (int)($this->iCurPage + 1);
				if ($this->arImages['image_next'] == "") {
					echo "<a href=\"" . $nextLink . "\" class=\"" . $this->arRowStyles["nav_link"] . "\" title=\"$next_label\">$next_label</a>" . $this->eol;
				} else {
					echo "<a href=\"" . $nextLink . "\" title=\"$next_label\"><img border=\"0\" alt=\"$next_label\" src=\"" . $this->arImages['image_next'] . "\"></a>" . $this->eol;
				}
			} else {
				if ($this->arImages['image_next'] == "") {
					echo "<strong>$next_label</strong>" . $this->eol;
				} else {
					echo "<img border=\"0\" alt=\"$next_label\" src=\"" . $this->arImages['image_next'] . "\">" . $this->eol;
				}
			}				
            
            //SUPLEMENTARY NAVIGATION METHODS
            echo '<div style="padding-top: 5px;">' . $this->eol;
            $uniqueString = md5(time() + rand(0,50000));
            //-GOTO BOX TYPE NAVIGATION
            if ($this->bShowGotoNavigation) {
                $jsButtonAction = 'if (isNaN(document.getElementById(\'JumpPage_' . $uniqueString . '\').value)==false) {';
                $jsButtonAction = $jsButtonAction . 'window.open (\'' . $this->sSelfLink . 'curPage=\' + document.getElementById(\'JumpPage_' . $uniqueString . '\').value,\'_self\');';
                $jsButtonAction = $jsButtonAction . '}';
                echo $this->arLanguageDictionary["Pagination_GotoLabel"] . $this->eol;
                echo '<input type="text" style="text-align: center;" id="JumpPage_' . $uniqueString . '" size="2" value="' . $this->iCurPage . '">' . $this->eol;
                echo '<input type="button" name="PagingGoButton" value="' . $this->arLanguageDictionary["Pagination_GotoButton"] . '" onClick="' . $jsButtonAction . '">' . $this->eol;
                echo "&nbsp;&nbsp;";  
            }
            //-SELECT BOX TYPE NAVIGATION
            if ($this->bShowSelectNavigation) {                
                $jsPageJump = 'window.open (\'' . $this->sSelfLink . 'curPage=\' + document.getElementById(\'PagingSelect_' . $uniqueString . '\').value,\'_self\');';                
                echo $this->arLanguageDictionary["Pagination_SelectLabel"] . $this->eol;
                echo '<select id="PagingSelect_' . $uniqueString . '" style="width: 40px;" onChange="'.$jsPageJump.'">' . $this->eol;
                for ($pc=1; $pc<=$pagesCount; $pc++) {
                    echo "<option ";
                    if ($this->iCurPage==$pc) {echo 'selected="selected"';}
                    echo " value=\"$pc\">$pc</option>" . $this->eol;
                }
                echo '</select>' . $this->eol;
            }
            echo '</div>' . $this->eol;            
            
			echo "</td>" . $this->eol;
			echo "</tr>" . $this->eol;
			
			$sqlLimiterString = " limit $startLimit, $countLimit";
		}
		
		return $sqlLimiterString;	
	}
	
	
	//---------------------------------------------------------
	//Setters and getters section
	//---------------------------------------------------------

    //setter extra pagination type select
    public function setPaginationSelectVisibility ($bPaginationSelectVisible) {
        $this->bShowSelectNavigation = $bPaginationSelectVisible;
    }
    
    //getter extra paginationtype select
    public function getPaginationSelectVisibility() {
        return $this->bShowSelectNavigation;
    }	
    
    //setter extra pagination type goto
    public function setPaginationGotoVisibility ($bPaginationGotoVisible) {
        $this->bShowGotoNavigation = $bPaginationGotoVisible;
    }
    
    //getter extra paginationtype goto
    public function getPaginationGotoVisibility() {
        return $this->bShowGotoNavigation;
    }        
	
	//setter language definition
	public function setLanguageDefinition ($sLanguageLabel, $sLanguageDefinition) {
		$this->arLanguageDictionary[$sLanguageLabel] = $sLanguageDefinition;
	}
	
	//getter language definition
	public function getLanguageDefinition($sLanguageLabel) {
		return $this->arLanguageDictionary[$sLanguageLabel];
	}
	
	//setter sqlString
	public function setPaginationDisplay($sZoneDisplayed){
		$arAllowedZones = array('up','bottom','both');
		if (in_array($sZoneDisplayed,$arAllowedZones)) {
		$this->sPaginationDisplay = $sZoneDisplayed;
		}
	}
	
	//getter sqlString
	public function getPaginationDisplay(){
		return $this->sPaginationDisplay;
	}	

	//setter sqlString
	public function setSqlString($sqlString){
		$this->sqlString = $sqlString;
	}
	
	//getter sqlString
	public function getSqlString(){
		return $this->sqlString;
	}	
	
	//setter sqlCountString
	public function setSqlCountString($sqlCountString){
		$this->sqlCountString = $sqlCountString;
	}
	
	//getter sqlCountString
	public function getSqlCountString(){
		return $this->sqlCountString;
	}		
	
	//setter idField
	public function setIdField($IdField){
		$this->sIdField = $IdField;
	}
	
	//getter idField
	public function getIdField(){
		return $this->sIdField;
	}	
	
	//setter records per page
	public function setRecPerPage($RecPerPage){
		$this->iRecPerPage = $RecPerPage;
	}
	
	//getter records per page
	public function getRecPerPage(){
		return $this->iRecPerPage;
	}
	
	//setter current page
	public function setCurrentPage($CurrentPage){
		$this->iCurPage = $CurrentPage;
	}
	
	//getter current page
	public function getCurrentPage(){
		return $this->iCurPage;
	}				
	
	//setter sSelfLink - used for navigation
	public function setSelfLink($SelfLink){
		$this->sSelfLink = $SelfLink;
	}
	
	//getter sSelfLink - used for navigation
	public function getSelfLink(){
		return $this->sSelfLink;
	}		
	
	//setter sSelfLink - used for navigation
	public function setPaginationVisibility($bShowPagination){
		$this->bShowPagination = $bShowPagination;
	}
	
	//getter sSelfLink - used for navigation
	public function getPaginationVisibility(){
		return $this->bShowPagination;
	}	
	
	//setter nav images
	public function setNavImages($image_prev, $image_next){
		$this->arImages['image_prev'] = $image_prev;
		$this->arImages['image_next'] = $image_next;
	}
	
	//getter sSelfLink - used for navigation
	public function getNavImages(){
		return $this->arImages;
	}	
	
	//set row styles
	public function setRowStyle($rowType, $rowStyle){
		$this->arRowStyles[$rowType] = $rowStyle;
	}
	
	//getter ColumnCount
	public function getColumnCount(){
		return $this->iColumnCount;
	}	
	
	//---------------------------------------------------------
	//FlushConfig
	//---------------------------------------------------------	
	
	public function FlushConfig(){
		echo "******************************************************************************<br>";
		echo "<strong>Datagrid Class Instance</strong><br>";
		echo "******************************************************************************<br>";
		echo "<strong>1. General settings</strong><br>";
		echo "<strong>\$iRecPerPage</strong> = " . $this->iRecPerPage . "<br>";
		echo "<strong>\$iCurPage</strong> = " . $this->iCurPage . "<br>";
		echo "<strong>\$sSelfLink</strong> = " . $this->sSelfLink . "<br>";
		echo "<br>";	
		
		echo "<strong>2. Display settings</strong><br>";
		echo "<strong>Pagination:</strong><br>";
		echo "Display pagination: " . $this->bShowPagination . "<br>";
		echo "Show pagination where: " . $this->sPaginationDisplay . "<br>";
		echo "<strong>Row styles:</strong><br>";
		echo "(internal id -> class name)<br>";
		echo "<pre>";
		print_r ($this->arRowStyles);
		echo "</pre>";
		echo "<strong>Images:</strong><br>";
		echo "(internal id -> image path)<br>";
		echo "<pre>";
		print_r ($this->arImages);
		echo "</pre>";		
		echo "<br>";		
		
		echo "<strong>3. SQL settings</strong><br>";
		echo "<strong>\$sqlString</strong> = <em>" . $this->sqlString . "</em><br>";
		echo "<strong>\$sqlCountString</strong> = <em>" . $this->sqlCountString . "</em><br>";
		echo "<strong>\$sIdField</strong> = " . $this->sIdField . "<br>";
		echo "<br>";
		
		echo "<strong>4. Colums dictionary</strong><br>";
		echo "<strong>\$iColumnCount</strong> = " . $this->iColumnCount . "<br>";
		echo "<strong>Colums definitions:</strong><br>";
		echo "<pre>";
		print_r ($this->arColumns);
		echo "</pre>";
		echo "<br>";
		
		echo "<strong>5. Options dictionary</strong><br>";
		echo "<strong>\$iOptionCount</strong> = " . $this->iOptionCount . "<br>";
		echo "<strong>Options definitions:</strong><br>";
		echo "<pre>";
		print_r ($this->arRowOptions);
		echo "</pre>";		
		echo "<br>";
		
		echo "<strong>Language definitions</strong><br>";
		echo "<pre>";
		print_r ($this->arLanguageDictionary);
		echo "</pre>";		
		echo "<br>";		
		echo "******************************************************************************<br>";
		echo "<strong>End Flushing Config</strong><br>";
		echo "******************************************************************************<br>";		
	}

}

?>