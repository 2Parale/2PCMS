<?php
/**
* Feeds class
* 28 December 2011
* -- manages operations with feeds
*/

class as_feed_manager{
    
    private $db;    //database object
    
    function __construct($db){
        $this->db = $db;
    }
    
    /********************************************************************************
    * PARTNERS
    * 
    */
    
    //getting partners list
    public function partner_get_list(){
        $rec_count = 0;
        $arResults = array();
        $recs = $this->db->get_results("Select * from aff_partners order by shop");
        
        if($recs!=null){
            foreach($recs as $rec){
                $rec_count++;
                $arResults["items"][$rec_count]["id"] = $rec->id;
                $arResults["items"][$rec_count]["shop"] = $rec->shop;
                $arResults["items"][$rec_count]["shop_url"] = $rec->shop_url;
                $arResults["items"][$rec_count]["network"] = $rec->network;
                $arResults["items"][$rec_count]["shop_desc"] = $rec->shop_desc;
            }
        }
        
        $arResults["count"] = $rec_count;
        return $arResults;
    }
    
    //getting one partner info
    public function partner_get_item($pid){
        $arResults = array();
        $pid = (int)$pid;
        $recs = $this->db->get_row("Select * from aff_partners where id=$pid");
        
        if($recs!=null){            
                $arResults["id"] = $recs->id;
                $arResults["shop"] = $recs->shop;
                $arResults["shop_url"] = $recs->shop_url;
                $arResults["network"] = $recs->network;
                $arResults["shop_desc"] = $recs->shop_desc;
                $arResults["feed_count"] = (int)$this->db->get_var("Select count(*) from aff_feeds where partner_id=$pid");                            
        }
        
        return $arResults;        
    }
        
    //add or edit partener
    public function partner_add_item($arInfo, $pid = 0){
        $ret_val = false;
        
        if($pid==0){
            //add
            if($this->db->query ("Insert into aff_partners (shop, shop_url, network, shop_desc) values ('".$arInfo["shop"]."', '".$arInfo["shop_url"]."', '".$arInfo["network"]."', '".$arInfo["shop_desc"]."')")){
                $ret_val = true;
            }
        } else {
            //edit
            if($this->db->query ("Update aff_partners set shop='".$arInfo["shop"]."', shop_url'".$arInfo["shop_url"]."', network='".$arInfo["network"]."', shop_desc='".$arInfo["shop_desc"]."' where id=$pid")){
                $ret_val = true;
            }            
        }
        
        return $ret_val;
    }
    
    
    //delete partner
    public function partner_delete_item($pid){
        $ret_val = false;
        
        if($this->db->query ("Delete from aff_partners where id=$pid")){
            $ret_val = true;
            
            /** CASCADE DELETE: FEEDS + PRODUCTS + CATEGORIES **/
            
        }
        
        return $ret_val;
    }
    
    
    /********************************************************************************
    * FEEDS
    * 
    */    
    
    //get feed list, options 
    public function feed_get_list($pid = 0, $start = 0, $limit = 0){
        $arResults = array();
        $f_count = 0;
        
        $sql = "Select * from aff_feeds";
        if($pid>0){
            $sql .= " where partner_id=$pid";
        }
        if($limit>0){
            $sql .= " limit $start, $limit";
        }
        
        $recs = $this->db->get_results($sql);
        if($recs!=null){
            foreach($recs as $rec){
                $f_count++;
                $arResults["items"][$f_count]["id"] = $rec->id;
                $arResults["items"][$f_count]["partner_id"] = $rec->partner_id;
                $arResults["items"][$f_count]["feed_filename"] = $rec->feed_filename;
                $arResults["items"][$f_count]["feed_url"] = $rec->feed_url;
                $arResults["items"][$f_count]["feed_desc"] = $rec->feed_desc;
                $arResults["items"][$f_count]["last_downloaded_date"] = $rec->last_date;                
            }
        }
        
        $arResults["count"] = $f_count;
        return $arResults;
    }
    
    
    //get feed by partner, alias function
    public function feed_get_list_by_partner($pid, $start = 0, $limit = 0){
        return $this->feed_get_list($pid, $start, $limit);
    }
    
    
    //get one feed details
    public function feed_get_item($pid, $fid){
        $arResults = array();
        
        $rec = $this->db->get_row("Select * from aff_feeds where partner_id=$pid and id=$fid");
        if($rec!=null){
            $arResults["id"] = $rec->id;
            $arResults["partner_id"] = $rec->partner_id;
            $arResults["feed_filename"] = $rec->feed_filename;
            $arResults["feed_url"] = $rec->feed_url;
            $arResults["feed_desc"] = $rec->feed_desc;
            $arResults["last_downloaded_date"] = $rec->last_date;                            
            
            $arResults["last_imported_date"] = $rec->last_date;                            
            $arResults["product_count"] = $rec->last_date;                                    
        }
        
        return $arResults;
    }
    
    
    //add or edit item
    public function feed_add_item($arInfo, $fid){
        $ret_val = false;
        
        if($fid==0){
            //insert
            $this->db->query("Insert into aff_feeds (partner_id, feed_filename, feed_url, feed_desc) values ()");
        }else{
            //update
            
        }
        
        return $ret_val;
    }
    
    
    public function feed_delete_item($fid){}
    
    
    /********************************************************************************
    * CATEGORIES
    * 
    */    
    
    public function categories_get_list_by_partner($pid){}
    
    public function categories_get_shop_utility_list(){}
    
    public function categories_save_asoc($arInfo){}
    
    
    /********************************************************************************
    * FEED OPERATIONS
    * 
    */    
    
    public function feedop_download($pid, $fid){}
    
    public function feedop_import($pid, $fid){}
    
    
    /********************************************************************************
    * PRODUCT IMPORT
    * 
    */    
    
    public function product_simulate($pid){}
    
    public function product_process_import($pid){}
    
    
}
?>
