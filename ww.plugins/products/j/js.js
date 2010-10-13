$(function(){
	$('a.products-lightbox').lightBox();
	$('div.product-images img').click(function(){
		var src=$('a.products-lightbox img').attr('src');
		var id=this.src.replace(/.*kfmget\/([0-9]*)[^0-9].*/,'$1');
		$('a.products-lightbox img').attr('src',src.replace(/kfmget\/([0-9]*)/,'kfmget/'+id));
		$('a.products-lightbox').attr('href','/kfmget/'+id);
	});
	var cache={},lastXhr;
	$('input[name=products-search]').autocomplete({
		source: function(request, response){
			var term = request.term;
			if ( term in cache ) {
				response( cache[ term ] );
				return;
			}
			lastXhr = $.getJSON( "/ww.plugins/products/frontend/search.php", request, function( data, status, xhr ) {
				cache[ term ] = data;
				if ( xhr === lastXhr ) {
					response( data );
				}
			});
		}
	})
	.focus(function(){
		this.value='';
	})
	.change(function(){
		var $this=$(this);
		var $form=$this.closest('form');
		if(!$form.length){
			$form=$this.wrap('<form style="display:inline" method="post" action="'+(document.location.toString())+'" />');
		}
		setTimeout(function(){
			$this.closest('form').submit();
		},500);
	});
});
