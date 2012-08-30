<?if($product_count>0){?>
<div class="pagination">
    <span class="pages">
    <?      
    echo getPaginationElements();
    ?>    
    </span>
    <span class="count"><?=$product_count?> produse / <?=$page_count?> pagini</span>
    <div class="clear"></div>
</div>
<?}?>