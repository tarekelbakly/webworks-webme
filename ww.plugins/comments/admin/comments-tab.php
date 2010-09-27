<?php

/**
  * Displays the contents of the comments tab
  *
  * PHP Version 5
  *
  * @category   CommentsPlugin
  * @package    WebworksWebme
  * @subpackage CommentsPlugin
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
**/

$id = $page['id'];
$commentsAllowed 
	=dbOne(
		'select value from page_vars 
		where name = "allow_comments" and page_id = '.$id,
		'value'
	);
$noModeration 
	= dbOne(
		'select value from site_vars where name = "comments_no_moderation"',
		'value'
	);
echo '<script>';
echo 'noModeration = '.$noModeration;
echo '</script>';
$html= 'Allow comments on this page? ';
$html.= '<input type="checkbox" name="page_vars[allow_comments]"';
if ($commentsAllowed=='on') {
	$html.= ' checked="checked"';
}
$html.= '/>';
$html.= '<br />Hide comments on this page? ';
$html.= '<input type="checkbox" name="page_vars[hide_comments]"';
$hideComments 
	= dbOne(
		'select value from page_vars 
		where name="hide_comments" and page_id = '.$id,
		'value'
	);
if ($hideComments) {
	$html.= ' checked="checked"';
}
$html.= ' />';
$html.= '<br /><strong>Comments for this page</strong>';
$comments = dbAll('select * from comments where objectid = '.$id);
$html.= '<div style="width:80%">';
$html.= '<table id="comments-table" style="width:100%">';
$html.= '<thead><tr>';
$html.= '<th>Date</th>';
$html.= '<th>Name</th>';
$html.= '<th>Email</th>';
$html.= '<th>URL</th>';
$html.= '<th>Comment</th>';
$html.= '<th>Mod</th>';
$html.= '<th>Edit</th>';
$html.= '<th>Delete</th>';
$html.= '</tr></thead>';
$html.= '<tbody>';
foreach ($comments as $comment) {
	$id = $comment['id'];
	$html.= '<tr id="'.$id.'">';
	$html.= '<td>'.$comment['cdate'].'</td>';
	$html.= '<td>'.$comment['name'].'</td>';
	$html.= '<td>'.$comment['email'].'</td>';
	$html.= '<td>'.$comment['homepage'].'</td>';
	$html.= '<td>'.$comment['comment'].'</td>';
	$html.= '<td>';
	$html.= '<a href="javascript:;" 
		onclick="start_moderation('.$id.','.((-1*$comment['isvalid'])+1).');">';
	$html.= $comment['isvalid']?'Unapprove':'Approve';
	$html.= '</a></td>';
	$html.= '<td><a href="javascript:;" 
		onclick="start_edit('.$id.',\''.$comment['comment'].'\')">';
	$html.= 'edit</a></td>';
	$html.= '<td><a href="javascript:;" onclick="start_delete('.$id.');">'
		.'[x]</a></td>';
	$html.='</tr>';
}
$html.= '</tbody></table>';
$html.= '</div>';
echo $html;
ww_addCss('/ww.plugins/comments/admin/comments-table.css');
ww_addScript('/ww.plugins/comments/admin/comments.js');
