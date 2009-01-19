function comments_init(){
		var cs,f,wrapper;
    wrapper=$('#webmeComments')[0];
    cs=newEl('div','ww_wrapper');
		f=new Element('form',{
			'id':'ww_form',
			'class':'formvalidation',
			'action':'javascript:comments_submit()'
		});
    addEls(wrapper,[cs,f]);
    var submit=newInput('ww_submit','submit','Comment');
    addEls(f,[
        newEl('h3','ww_newCommentHeader',0,'Add Comment'),
        newEl('label','ww_nameLabel',0,[new Element('span').appendText('Name'),new Element('input',{'class':'required','id':'ww_name'})]),
        newEl('label','ww_emailLabel',0,[new Element('span').appendText('Email'),new Element('input',{'class':'email required','id':'ww_email'})]),
        newEl('label','ww_homepageLabel',0,[new Element('span').appendText('Homepage'),newInput('ww_homepage',0,'http://')]),
        newEl('label','ww_commentLabel',0,[new Element('span').appendText('Comment'),new Element('textarea',{'class':'required','id':'ww_comment'})]),
        submit
    ]);
		loadFormValidation();
    x_comments_getAll(comment_lastId,comments_showAll);
}
function comments_showAll(comments){
    if(!isArray(comments)||comments.length==0){
        if(!isArray(comments)&&comments!='')alert(comments);
        else addEls(removeChildren('ww_wrapper'),newEl('em',0,0,'No comments'));
        return;
    }
		var i,wrapper;
		i=0;
		wrapper=$M('ww_wrapper');
		if(!comment_lastId)removeChildren('ww_wrapper');
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
						'src':  'http://www.gravatar.com/avatar.php?gravatar_id='+c.email+escape(document.location.toString().replace(/(\/\/.*\/).*/,"$1")+'i/gravatar.png'),
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
        addEls(wrapper,cwrapper);
        $M('ww_commentBody'+c.id).innerHTML=comments_parseComment(c.comment);
    }
    comment_lastId=comments[i-1].id;
    $M('ww_comment').value='';
}
function comments_replaceAll(comments){
    $M('ww_wrapper').empty();
    comments_showAll(comments);
}
function comments_submit(){
    x_comments_submit($F('ww_name'),$F('ww_email'),$F('ww_homepage'),$F('ww_comment'),comment_password,comment_lastId,comments_submitted);
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
