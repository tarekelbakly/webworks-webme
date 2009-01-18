function os_countries_change(sel){
	var f,els,i,el,url;
	url=pagedata.url+"&__os_country="+sel.value;
	f=sel;
	while(f && f.tagName!='FORM')f=f.parentNode;
	if(!f)return document.location=url;
	els=f.getElementsByTagName('input');
	for(i=0;i<els.length;++i){
		el=els[i];
		switch(el.type){
			case 'hidden':
				if(el.name=='os_action')el.value='';
				break;
		}
	}
	f.submit();
}
