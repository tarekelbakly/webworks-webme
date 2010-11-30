$(function(){
	$('div.menu-accordion ul ul').each(function(){
		var $this=$(this);
		$this
			.css('display','none')
			.addClass('is-fg-submenu')
			.prev()
				.addClass('has-submenu')
				.click(function(){
					var $this=$(this);
					$this.next().toggle();
					return false;
				});
	});
	var pid=pagedata.id;
	var $menu=$('.menu-pid-'+pid).closest('ul');
	$menu.prev().trigger('click');
});
