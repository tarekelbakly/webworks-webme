$(document).ready(function(){
	$('.datatable').dataTable();
	$('input.date-human').each(function(key,el){
		var $this=$(el);
		var	id='date-input-'+Math.random().toString().replace(/\./,'');
		var dparts=$this.val().split(/-/);
		$this
			.datepicker({
				dateFormat:'yy-mm-dd',
				modal:true,
				altField:'#'+id,
				altFormat:'DD, d MM, yy'
			});
		var $wrapper=$this.wrap('<div style="position:relative" />');
		var $input=$('<input id="'+id+'" class="date-human-readable" value="'+date_m2h($this.val())+'" />');
		$input.insertAfter($this);
		$this.css({
			'position':'absolute',
			'opacity':0
		});
		$this
			.datepicker(
				'setDate', new Date(dparts[0],dparts[1]-1,dparts[2])
			);
	});
});
