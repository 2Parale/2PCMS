function markProductClick(product_id){
        $.ajax({
        url: "ajax_proxy.php?action=mark_product_visit&id="+product_id,
        async: false
        });     
        
}

function searchBoxFocus(){
    if($('#keys').val()=="[Cauta ...]"){
        $('#keys').removeClass('search-off');
        $('#keys').addClass('search-on');        
        $('#keys').val('');
    }
}

function searchBoxBlur(){
    if($('#keys').val()==""){
        $('#keys').removeClass('search-on');
        $('#keys').addClass('search-off');        
        $('#keys').val('[Cauta ...]');        
    }
}