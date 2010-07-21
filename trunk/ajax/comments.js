function comments_init(){
		$('#webmeComments')
			.append('<div id="ww_wrapper"></div>'
				+'<form id="ww_form" class="formvalidation" action="javascript:comments_submit()">'
				+'<h3 id="ww_newCommentHeader">Add Comment</h3>'
				+'<label id="ww_nameLabel"><span>Name</span><input class="required" id="ww_name" /></label>'
				+'<label id="ww_emailLabel"><span>Email</span><input type="email" class="required" id="ww_email" /></label>'
				+'<label id="ww_homepageLabel"><span>Homepage</span><input value="http://" id="ww_homepage" /></label>'
				+'<label id="ww_commentLabel"><span>Comment</span><textarea class="required" id="ww_comment"></textarea></label>'
				+'<input type="submit" name="ww_submit" value="Comment" />'
				+'</form>'
			);
		loadFormValidation();
    x_comments_getAll(comment_lastId,comments_showAll);
}
function comments_showAll(comments){
    if(!isArray(comments)||comments.length==0){
        if(!isArray(comments)&&comments!='')alert(comments);
				else $('#ww_wrapper').html('<em>No Comments</em>');
        return;
    }
		var i,wrapper;
		i=0;
		$wrapper=$('#ww_wrapper');
		if(!comment_lastId)$('#ww_wrapper').empty();
    for(;i<comments.length;++i){
        var c=comments[i],cwrapper=newEl('div','ww_comment'+c.id,'ww_commentWrapper');
        var name=c.name?c.name:'anonymous';
        if(c.homepage)name=newLink(c.homepage,name);
				if(c.email!='d41d8cd98f00b204e9800998ecf8427e'){ // avatar
					var link=new Element('a',{
						'href': 'http://www.gravatar.com/',
						'class':'ww_commentGravatarLink'
					});
					var avatar=new Element('img',{
						'src':  'http://www.gravatar.com/avatar.php?gravatar_id='+c.email+'&default='+escape(document.location.toString().replace(/(\/\/.*\/).*/,"$1")+'i/gravatar.png'),
						'class':'ww_commentAvatar'
					});
					link.appendChild(avatar);
       		cwrapper.appendChild(link);
				}
        var header=newEl('div',0,'ww_commentTitle',['At '+date_m2h(c.cdate,'datetime')+', ',name,' said:']);
        var body=newEl('div','ww_commentBody'+c.id,'ww_commentBody');
        cwrapper.appendChild(header);
        cwrapper.appendChild(body);
        if(userdata.isAdmin){
            var del=new Element('a',{
                href:'javascript:if(confirm("are you sure you want to delete this comment?"))x_comments_delete('+c.id+',comments_replaceAll)'
            }).appendText('delete this comment');
            cwrapper.appendChild(del);
        }
				cwrapper.appendChild(new Element('hr'));
				$wrapper.append(cwrapper);
				$('#ww_commentBody'+c.id).html(comments_parseComment(c.comment));
    }
    comment_lastId=comments[i-1].id;
    $('#ww_comment').val('');
}
function comments_replaceAll(comments){
    $('#ww_wrapper').empty();
    comments_showAll(comments);
}
function comments_submit(){
	x_comments_submit(
		$('#ww_name').val(),
		$('#ww_email').val(),
		$('#ww_homepage').val(),
		$('#ww_comment').val(),
		comment_password,
		comment_lastId,
		comments_submitted
	);
}
function comments_submitted(ret){
	if($type(ret)!='array')return alert(ret);
	if(!ret[1])alert('An email has been sent to your address to verify it.\nYour comment has been held pending verification of your email');
	if(ret[0].length)comments_showAll(ret[0]);
}
function comments_parseComment(comment){
    var found=0;
    comment=comment.replace(comment_regexps.website,'<a href="$1">$1</a>');
    comment=comment.replace(comment_regexps.carriagereturn,'<br />');
    return comment;
}
comments_init();
var comment_regexps={
    website:/(http:\/\/[a-zA-Z0-9-.]*\/[^\s$]*)/g,
    carriagereturn:/(\n|&lt;br&gt;)/g
};
