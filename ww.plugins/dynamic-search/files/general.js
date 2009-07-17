/*
        Webme Dynamic Search Plugin v0.2
        File: files/general.js
        Developer: Conor Mac Aoidh <http://macaoidh.name>
        Report Bugs: <conor@macaoidh.name>
*/

$(document).ready(function(){
        $('#dynamic_searchfield').focus(dynamic_key);
        $('#dynamic_searchfield').blur(dynamic_key);
	$('#dynamic_search').submit(dynamic_search);
	$('.popular').click(popular_search);
});

function dynamic_search(){
        var dynamic_search = $('#dynamic_searchfield').attr('value');
	if(dynamic_search==''){
		alert('Please enter search criteria.');
		return false;
	}
        var dynamic_category = $('#dynamic_search_select').attr('value');
        var content = $('#dynamic_search_results');
	content.css({display:'none'});
        $('#stuff').css({display:'none'});
	$.ajax({
		url:"/ww.plugins/dynamic-search/files/jsresults.php?dynamic_search=" + dynamic_search + "&dynamic_category=" + dynamic_category,
		success: function(html){
			content.html(html);
		}
	});
	content.fadeIn('slow');
        return false;
}

function popular_search(){
	var string=this.href.replace(/.*\?/,'');
	alert(string);
        var content = $('#dynamic_search_results');
        $('#stuff').css({display:'none'});
        $.ajax({
                url:"/ww.plugins/dynamic-search/files/jsresults.php?" + string,
                success: function(html){
                        content.html(html);
                }
        });
        content.fadeIn('slow');
	return false;
}

function dynamic_key(){
        var search = $('#dynamic_searchfield');
        if(search.attr('value')=='Enter Keywords...') search.attr('value','');
        else if(search.attr('value')=='')  search.attr('value','Enter Keywords...');
}
