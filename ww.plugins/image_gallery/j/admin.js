function image_gallery_add_price(){
	ig_price_count++;
	$('<input class="medium" name="page_vars[image_gallery_pricedescs_'+ig_price_count+']" value="description" /><input class="ig_price small" name="page_vars[image_gallery_prices_'+ig_price_count+']" value="0" /><br />')
		.insertBefore('#ig_prices_more');
}
