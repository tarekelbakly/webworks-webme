function pr_addImageUploadForm(){
	var wrapper=$M('product_image_inputs');
	var f1=newForm('/j/'+ww.FCKEDITOR+'/editor/plugins/kfm/upload.php','POST','multipart/form-data','kfm_iframe');
	f1.id='kfm_uploadForm';
	var iframe=newEl('iframe','kfm_iframe');
	iframe.setStyle('display','none');
	iframe.src='javascript:false';
	var submit=new Element('input',{
		'type':'submit',
		'value':'Upload'
	});
	var input=newInput('kfm_file','file');
	var maxsize=newInput('MAX_FILE_SIZE','hidden','999999999');
	var onload=newInput('onupload','hidden','parent.x_pr_get_image_list(parent.pr_imagedir,parent.pr_showimages);parent.$M(\'kfm_file\').type=\'text\';parent.$M(\'kfm_file\').type=\'file\';');
	addEls(f1,[input,onload,maxsize,submit]);
	addEls(wrapper,[f1,iframe]);
}
function pr_initialise(){
	pr_addImageUploadForm();
	x_pr_get_image_list(pr_imagedir,pr_showimages);
}
function pr_showimages(res){
	var files=res.files;
	var wrapper=removeChildren('product_image_icons');
	if(files.length){
		var icons=[];
		for(var i=0;i<files.length;++i){
			var icon=newEl('div','','pr_icon');
			if(files[i].id==pr_defaultimage)addClass(icon,'pr_default_image');
			addEls(icon,[
				newImg('/kfmget/'+files[i].id+',width='+pr_icon_size+',height='+pr_icon_size),
				files[i].name,
				newLink('javascript:pr_defaultimage='+files[i].id+';x_pr_mark_as_default('+pr_id+','+files[i].id+',pr_showimages)','d','pr_markasdefault'+files[i].id,'pr_markasdefault'),
				newLink('javascript:if(confirm("are you sure you want to remove this image?"))x_pr_remove_image('+pr_id+','+files[i].id+',pr_showimages)','x','pr_removeimage'+files[i].id,'pr_removeimage')
			]);
			icon.fileid=files[i].id;
			icons.push(icon);
		}
		addEls(wrapper,icons);
	}
	else addEls(wrapper,'no images yet');
}
function pr_hideimagelinks(id){
	if(window.pr_linkover)return;
	delEl('pr_removeimage'+id);
	delEl('pr_markasdefault'+id);
}
pr_initialise();
var pr_icon_size=48;
var pr_linkover=0;
