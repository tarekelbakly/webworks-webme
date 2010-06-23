$(function(){
	$('a.products-lightbox').lightBox();
	$('div.product-images img').click(function(){
		var src=$('a.products-lightbox img').attr('src');
		var id=this.src.replace(/.*kfmget\/([0-9]*)[^0-9].*/,'$1');
		$('a.products-lightbox img').attr('src',src.replace(/kfmget\/([0-9]*)/,'kfmget/'+id));
		$('a.products-lightbox').attr('href','/kfmget/'+id);
	});
});
