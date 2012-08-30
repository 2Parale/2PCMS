//delete link confirmation
function ConfirmDelete2Link (sUrl) {
    var answer = confirm ("Sunteti sigur ca doriti sa stergeti?\n\rApasati OK pentru DA");
    if (answer) {window.open (sUrl, '_self');}
}

//procesare imagini
function startImgProcessing(imgCount){
    
    if(imgCount==-1){
        imgc = $.ajax({
        url: "ajax_proxy.php?action=get_img_process_count",
        async: false
        }).responseText;        
    } else {
        imgc = imgCount;
    }
    
    total_images = imgc;
    $('#img-processing-progressbar').fadeIn('fast');
    
    window.open('__img_process.php?todo='+imgc,'img_status');
}


function getWaitKeywords(cid){
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=get_wait_keywords",
    async: false,
    beforeSend: function(){
        $('#'+cid).html('');
        $('#'+cid).hide();
    },    
    success: function(data){
        $('#'+cid).html(data);
        $('#'+cid).fadeIn('normal');
    }
    }).responseText;        
}

function setKeywordStatus(wid, status){
    
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=update_keyword&id=" + wid + "&status=" + status,
    async: false,
    success: function(data){
        $('#welid-'+wid).fadeOut('fast');
    }
    }).responseText;    
    
    
}


function getBrands(typ, container){
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=get_brands&what=" + typ,
    async: false,
    success: function(data){
        $('#'+container).html(data);
    }
    }).responseText;        
}


//Form Validation Function
function validateForm(arFields, objResponse){        
    var retVal = true;
    var vmsg = "";
    $('#'+objResponse).hide(); $('#'+objResponse).html();
        
    for(var i in arFields){
        fLabel = arFields[i][0];
        fId = arFields[i][1];
        fRule = arFields[i][2];
        
        if(fRule=="required"){
            if($('#'+fId).val()==""){
                retVal = false;
                vmsg = vmsg + "Campul <b><i>" + fLabel + "</i></b> trebuie completat" + "<br/>";
            }
        }

        if(fRule=="required-list"){
            if($('#'+fId).val()=="0"){
                retVal = false;
                vmsg = vmsg + "Pentru <b><i>" + fLabel + "</i></b> trebuie aleasa o valoare din lista" + "<br/>";
            }
        }
        
    }
    
    if(vmsg!=""){
        $('#'+objResponse).html(vmsg);
        $('#'+objResponse).slideDown("normal");
    }
    
    return retVal
}



function editPopup(url2open){
    window.open(url2open, '_blank', 'width=500, height=400, status=0, toolbar=0, resizable=0, scrollbars=0');
}

function editPopup2(url2open){
    window.open(url2open, '_blank', 'width=1000, height=600, status=0, toolbar=0, resizable=0, scrollbars=0');
}


function ReloadLocalFeedsList(){
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=get_local_feed_list",
    async: false,
    success: function(data){
        $('#local-feeds-list').html(data);
    }
    }).responseText;     
}

function callFromIframe_Refresh(){
    window.location.reload();
}

function callFromIframe_Update(update_msg, remaining_imgs){
    $('#img-processing-status').html(update_msg);
    percent_done = 100 - Math.ceil(remaining_imgs*100/total_images);
    $('#img-processing-progressbar-done').width(percent_done*3);
}

function closeIndexUpdater(){
    $('#cms-updater').fadeOut('fast');
}

function getUrlFromText(objSource, objDestination){
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=get_url_from_text&text="+$('#'+objSource).val(),
    async: false,
    success: function(data){
        $('#'+objDestination).val(data);
    }
    }).responseText;    
}

function getArticleProducts(objDestination, articleId){
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=get_article_products&id="+articleId,
    async: false,
    success: function(data){
        $('#'+objDestination).html(data);
    }
    }).responseText;        
}

function getSearchArticleProducts(objSource, objDestination, articleId, start){
    if($('#'+objSource).val()!=""){
        $('#'+objDestination).fadeIn('fast');
        kcstr = $.ajax({
        url: "ajax_proxy.php?action=search_article_products&id="+articleId+"&start="+start+"&search="+$('#'+objSource).val(),
        async: false,
        success: function(data){
            $('#'+objDestination).html(data);
        }
        }).responseText;             
    }
}

function clearSearchArticleProducts(objDestination){
    $('#'+objDestination).html("");
    $('#'+objDestination).fadeOut('fast');
}

function asocProductToArticle(articleId, productId, objDestination){
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=add_article_product&article_id="+articleId+"&product_id="+productId,
    async: false,
    success: function(data){
        getArticleProducts(objDestination, articleId);
    }
    }).responseText;         
}

function deleteProductToArticle(articleId, productId, objDestination){
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=delete_article_product&article_id="+articleId+"&product_id="+productId,
    async: false,
    success: function(data){
        getArticleProducts(objDestination, articleId);
    }
    }).responseText;         
}


function lpSearchProducts(objSource, objDestination, start){
    if($('#'+objSource).val()!=""){
        $('#'+objDestination).fadeIn('fast');
        kcstr = $.ajax({
        url: "ajax_proxy.php?action=lp_search_products&start="+start+"&search="+$('#'+objSource).val(),
        async: false,
        success: function(data){
            $('#'+objDestination).html(data);
        }
        }).responseText;             
    }    
}

function clearLPSearchProducts(objDestination){
    $('#'+objDestination).html("");
    $('#'+objDestination).fadeOut('fast');    
}

function lpSelectProduct(id){
    $('#product_id').val(id);
    $('#new-lp1').fadeOut('fast');
    
    kcstr = $.ajax({
    url: "ajax_proxy.php?action=lp_get_product_details&id="+id,
    dataType: 'json',
    async: false,
    success: function(data){
        $('#new_title').val(data.title);
        $('#lp_url').val(data.title_url);
        $('#new-lp2').fadeIn('fast');
        $("#new_description").htmlarea({
        toolbar: ["bold", "italic", "underline", "|", "h1", "h2", "h3", "|", "link", "unlink", "|", "image", "|", "html"]
        });
        $('#new_description').text(data.description).htmlarea('updateHtmlArea');                        
    }
    }).responseText;    
}

function lpResetForm(){
    $('#new-lp2').fadeOut('fast');
    $('#new-lp1').fadeIn('fast');
    clearLPSearchProducts('prod-results');
}