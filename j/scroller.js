function sn_Init(){
	var els=$('div.scrollingNews');
	for(var j=0;els[j];++j){
		var el=els[j];
		if(parseFloat(el.getStyle('height'))!=parseInt(el.getStyle('height')))el.setStyle('height',200);
		{ //  variables 
			sn_Active[j]=1;
			sn_PauseStage[j]=0;
			sn_ScrolledAmount[j]=0;
			sn_POffset[j]=18;
		}
		{ //  remove empty paragraphs 
			var ps=$('blockquote',el);
			for(var k=ps.length-1;k>-1;--k){
				if(ps[k].innerHTML=='')ps[k].remove();
				else{
					ps[k].className='newsBlock';
					$(ps[k]).click((function(href){
						return function(){
							document.location=href;
						}
					})($E('a',ps[k]).href));
				}
			}
		}
		{ //  set id and styles for wrapper 
			el.id='sn_'+j;
			el.style.overflow='hidden';
			el.style.position='relative';
		}
		{ //  attach stop and start events 
			el.addEvent('mouseover',sn_Deactivate);
			el.addEvent('mouseout',sn_Activate);
		}
		$('<div class="news_scroller_gradient_top" style="width:'+el.offsetWidth+'px">&nbsp;</div>').appendTo(el);
		$('<div class="news_scroller_gradient_bottom" style="width:'+el.offsetWidth+'px">&nbsp;</div>').appendTo(el);
	}
	sn_Scroll();
}
function sn_Scroll(){
	for(j=0;j<sn_Active.length;j++){
		if(sn_Active[j]){
			var pEl=$M('sn_'+j),el=getEls('div',pEl)[0],elPs=getEls('blockquote',el);
			if(!sn_PauseStage[j]){
				if(!elPs[1])return;
				el.style.marginTop=sn_ScrolledAmount[j]-- +'px';
				if(parseInt(elPs[1].offsetTop)<1)sn_PauseStage[j]=1;
			}else{
				sn_PauseStage[j]+=sn_Speed;
				if(sn_PauseStage[j]>sn_PauseAmount){
					sn_PauseStage[j]=0;
					sn_ScrolledAmount[j]=0;
					el.style.marginTop=0;
					var ps=getEls('blockquote',el);
					ps[1].style.marginTop=0;
					ps[1].style.paddingTop=0;
					el.appendChild(elPs[0]);
				}
			}
		}
	}
	setTimeout('sn_Scroll()',sn_Speed);
}
function sn_Deactivate(e){
	sn_setActive((new Event(e)).target,0);
}
function sn_Activate(e){
	sn_setActive((new Event(e)).target,1);
}
function sn_setActive(el,a){
	while(!el.id && el)el=el.parentNode;
	sn_Active[el.id.replace(/.*_/,'')]=a;
}
// {  variables 
var sn_Active=[],sn_PauseStage=[],sn_ScrolledAmount=[],sn_POffset=[];
var sn_PauseAmount=2000,sn_Speed=25;
// }
sn_Init();
