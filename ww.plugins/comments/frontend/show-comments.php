<?php

/**
  * Displays validated comments
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

require_once SCRIPTBASE.'ww.incs/recaptcha.php';
/**
  * The main display function
  *
  * @param Object $page Page Info
  *
  * @return $html The comments and an add comment form
**/
function Comments_displayComments($page) {
	$location = 'http://ajax.microsoft.com/ajax/jquery.validate/1.5.5';
	$location.= '/jquery.validate.min.js';
	$html = '<div id="start-comments"><br />';
	$html.= '<script src="'.$location.'"></script>';
	WW_addScript('/ww.plugins/comments/frontend/comments-frontend.js');
	WW_addCSS('/ww.plugins/comments/frontend/comments.css');
	$hideComments 
		= dbOne(
			'select value from page_vars 
			where name = "hide_comments" and page_id = '.$page->id,
			'value'
		);
	$allowComments 
		= dbOne(
			'select value from page_vars 
			where name = "allow_comments" and page_id = '.$page->id,
			'value'
		);
	if ($hideComments) {
		$query = 'select * from comments where objectid = '.$page->id;
		$query.= ' and id in (';
		foreach ($_SESSION['comment_ids'] as $comment) {
			$query.= (int)$comment.', ';
		}
		if (is_numeric(strpos($query, ', '))) {
			$query = substr_replace($query, '', strrpos($query, ', '));
			$query.= ')';
		}
		else {
			$query = '';
		}
	}
	else {
		$query = 'select * from comments where objectid = '.$page->id;
		$query.= ' and (isvalid = 1 or id in (';
		if (isset($_SESSION['comment_ids']) && is_array($_SESSION['comment_ids'])) {
			foreach ($_SESSION['comment_ids'] as $comment) {
				$query.= (int)$comment.', ';
			}
		}
		if (is_numeric(strpos($query, ', '))) {
			$query = substr_replace($query, '', strrpos($query, ', '));
			$query.= '))';
		}
		else {
			$query = 'select * from comments where objectid = '.$page->id;
			$query.= ' and isvalid = 1';
		}
	}
	if (!empty($query)) {
		$comments = dbAll($query.' order by cdate asc');
	}
	if (count($comments)) {
		$html.= '<strong>Comments</strong><br /><br />';
	}
	$html.= '</div>';
	foreach ($comments as $comment) {
		$id = $comment['id'];
		$datetime = $comment['cdate'];
		$allowedToEdit 
			= is_admin()||in_array($id, $_SESSION['comment_ids'], false);
		if ($allowedToEdit) {
			$html.= '<div class="comments" id="comment-wrapper-'.$id.'" cdate="'.$datetime.'"
				comment="'.htmlspecialchars($comment['comment']).'">';
		}
		$html.=  '<div id="comment-info-'.$id.'">Posted by ';
		if (!empty($comment['site'])) {
			$html.= '<a href="'.$comment['site'].'" target=_blank>'
				.htmlspecialchars($comment['name']).'</a>';
		}
		else {
			$html.= htmlspecialchars($comment['name']);
		}
		$html.= ' on '.date_m2h($datetime).'</div>';
		$html.= '<div id="comment-'.$id.'">';
		$html.= htmlspecialchars($comment['comment']);
		$html.= '</div>';
		$html.= '<br /><br />';
		if ($allowedToEdit) {
			$html.= '</div>';
		}
	}
	if ($allowComments=='on') {
		$html.= Comments_showCommentForm($page->id);
	}
	return $html;
}

/**
  * Shows the add comment form
  *
  * @param int $pageID The page that the comment is to be displayed on
  *
  * @return $display The form
  *
**/
function Comments_showCommentForm($pageID) {
	if (is_logged_in()) {
		$userID = get_userid();
		$user 
			= dbRow(
				'select name, email from user_accounts 
				where id = '.$userID
			);
	}
	$display = '<strong>Add Comment</strong><br />';
	$display.= '<form id="comment-form" method="post" 
		action="javascript:comments_check_captcha();">';
	$display.= '<input type="hidden" name="page" id="page" 
		value="'.$pageID.'" />';
	$display.='<table><tr><th>Name</th>';
	$display.= '<td><input id="name" name="name" ';
	if (isset($user)) {
		$display.= ' value="'.htmlspecialchars($user['name']).'"';
	}
	$display.= ' /></td></tr>';
	$display.= '<tr><th>Email</th>';
	$display.= '<td><input id="email" name="email"';
	if (isset($user)) {
		$display.= ' value="'.htmlspecialchars($user['email']).'"';
	}
	$display.= ' /></td></tr>';
	$display.= '<tr><th>Website</th>';
	$display.= '<td><input id="site" name="site" /></td></tr>';
	$display.= '<tr><th>Comment</th>';
	$display.= '<td><textarea id="comment" name="comment"></textarea></td></tr>';
	$display.= '<tr><td colspan="2"><div id="captcha">';
	$display.= recaptcha_get_html(RECAPTCHA_PUBLIC);
	$display.= '</div></td></tr>';
	$display.= '<tr><th>&nbsp;</th><td><input type="submit" id="submit" value="Submit Comment"  /></td></tr>';
	$display.= '</table></form>';
	return $display;
}
