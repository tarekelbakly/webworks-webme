<?php

/**
  * The plugin.php file for Webme's comments plugin
  *
  * PHP Version 5
  *
  * @category   WebmeCommentsPlugin
  * @package    WebworksWembe
  * @subpackage Comments
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
**/

$plugin
	= array(
		'name'=>'Comments',
		'description' =>'Allow visitors to comment on pages on your site',
		'version'=>3,
		'admin'=>array(
			'menu'=>array(
				'Communication>Comments'=>'comments'
			),
			'page_panel'=>array(
				'name'=>'Comments', 
				'function'=>'comments_show_tab'
			)
		),
		'triggers'=>array(
			'page-content-created'=>'comments_show_page_comments'
		)
	);

/**
  * A stub function to display the contents of the comments tab
  *
  * @param Object $page     The page
  * @param Object $pagevars Page related variables
  *
  * @return void
  *
  * @see admin/comments-tab.php
  *
**/

function comments_show_tab ($page, $pagevars) {
	require_once SCRIPTBASE.'ww.plugins/comments/admin/comments-tab.php';
}

/**
  * A stub function to show comments
  *
  * @param Object $PAGEDATA The page
  *
  * @return string The comment html
  *
  * @see frontend/comments-show.php
  *
**/

function comments_show_page_comments($PAGEDATA) {
	$dir = dirname(__FILE__);
	require_once $dir.'/frontend/show-comments.php';
	$commentData = Comments_displayComments($PAGEDATA);
	return $commentData;
}
