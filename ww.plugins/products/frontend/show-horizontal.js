jQuery.fn.dataTableExt.oApi.fnSetFilteringDelay = function ( oSettings, iDelay ) {
	var
		_that = this,
		iDelay = (typeof iDelay == 'undefined') ? 250 : iDelay;
	 
	this.each( function ( i ) {
		$.fn.dataTableExt.iApiIndex = i;
		var
			$this = this,
			oTimerId = null,
			sPreviousSearch = null,
			anControl = $( 'input', _that.fnSettings().aanFeatures.f );
		 
			anControl.unbind( 'keyup' ).bind( 'keyup', function() {
			var $$this = $this;
 
			if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
				window.clearTimeout(oTimerId);
				sPreviousSearch = anControl.val(); 
				oTimerId = window.setTimeout(function() {
					$.fn.dataTableExt.iApiIndex = i;
					_that.fnFilter( anControl.val() );
				}, iDelay);
			}
		});
		 
		return this;
	} );
	return this;
}
 
$(function(){
	var cols=[];
	var oTable=$('.product-horizontal');
	var numRows=oTable.find('tr').length-2;
	oTable.find('thead th').each(function(){
		cols.push({'sName':$(this).attr('o')});
	});
	oTable.dataTable({
		"sScrollY": oTable[0].offsetHeight*.6,
		"sScrollX": "100%",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": '/ww.plugins/products/frontend/get-datatable-data.php?pid='
			+pagedata.id,
		"aoColumns": cols,
		"bScrollInfinite": true,
		"bScrollCollapse": true,
		"iDisplayLength" : numRows,
		"oLanguage": { "sSearch": "Search all columns:" }
	}).fnSetFilteringDelay();

	var oInput=$('table.product-horizontal tfoot input');
	oInput.keyup( function () {
		clearTimeout(window.dt_filter);
		var $this=this;
		window.dt_filter=setTimeout(function(){
			oTable.fnFilter( $this.value, oInput.index($this) );
		}, 500);
	} );
	oInput.each( function (i) {
		asInitVals[i] = this.value;
	} );
	oInput.focus( function () {
		if ( this.className == "search_init" ) {
			this.className = "";
			this.value = "";
		}
	} );
	oInput.blur( function (i) {
		if ( this.value == "" ) {
			this.className = "search_init";
			this.value = asInitVals[$("tfoot input").index(this)];
		}
	} );
});
