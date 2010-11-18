$(function(){
	$('div.menu-accordion ul ul').each(function(){
		var $this=$(this);
		$this.css('display','none');
		$this.prev()
			.addClass('has-submenu')
			.click(function(){
				var $this=$(this);
				$this.next().toggle();
				return false;
			});
	});
});
