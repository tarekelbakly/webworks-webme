function scrollingEvents_Init(){
	var els=$('div.scrollingEvents');
	for(var j=0;els[j];++j){
		var el=els[j];
		if(parseFloat(el.getStyle('height'))!=parseInt(el.getStyle('height')))el.setStyle('height',100);
		{ //  variables 
			scrollingEvents_Active[j]=1;
			scrollingEvents_PauseStage[j]=0;
			scrollingEvents_ScrolledAmount[j]=0;
			scrollingEvents_POffset[j]=18;
		}
		{ //  remove empty paragraphs 
			var ps=$('blockquote',el);
			for(var k=ps.length-1;k>-1;--k){
				if(ps[k].innerHTML=='')ps[k].remove();
				else{
					$(ps[k]).click((function(href){
						return function(){
							document.location=href;
						}
					})($E('a',ps[k]).href));
				}
			}
		}
		{ //  set id and styles for wrapper 
			el.id='scrollingEvents_'+j;
			el.style.overflow='hidden';
			el.style.position='relative';
		}
		{ //  attach stop and start events 
			el.addEvent('mouseover',scrollingEvents_Deactivate);
			el.addEvent('mouseout',scrollingEvents_Activate);
		}
	}
	scrollingEvents_Scroll();
}
function scrollingEvents_Scroll(){
	for(j=0;j<scrollingEvents_Active.length;j++){
		if(scrollingEvents_Active[j]){
			var pEl=$M('scrollingEvents_'+j),el=getEls('div',pEl)[0],elPs=getEls('blockquote',el);
			if(!scrollingEvents_PauseStage[j]){
				if(!elPs[1])return;
				el.style.marginTop=scrollingEvents_ScrolledAmount[j]-- +'px';
				if(elPs[1].offsetTop==0)scrollingEvents_PauseStage[j]=1;
			}else{
				scrollingEvents_PauseStage[j]+=scrollingEvents_Speed;
				if(scrollingEvents_PauseStage[j]>scrollingEvents_PauseAmount){
					scrollingEvents_PauseStage[j]=0;
					scrollingEvents_ScrolledAmount[j]=0;
					el.style.marginTop=0;
					var ps=getEls('blockquote',el);
					ps[1].style.marginTop=0;
					ps[1].style.paddingTop=0;
					el.appendChild(elPs[0]);
				}
			}
		}
	}
	setTimeout('scrollingEvents_Scroll()',scrollingEvents_Speed);
}
function scrollingEvents_Deactivate(e){
	scrollingEvents_setActive((new Event(e)).target,0);
}
function scrollingEvents_Activate(e){
	scrollingEvents_setActive((new Event(e)).target,1);
}
function scrollingEvents_setActive(el,a){
	while(!el.id && el)el=el.parentNode;
	scrollingEvents_Active[el.id.replace(/.*_/,'')]=a;
}
{ //  variables 
	var scrollingEvents_Active=[],scrollingEvents_PauseStage=[],scrollingEvents_ScrolledAmount=[],scrollingEvents_POffset=[];
	var scrollingEvents_PauseAmount=500,scrollingEvents_Speed=50;
}
scrollingEvents_Init();
