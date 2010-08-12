<?php
/**
  * Prompts the user to save the file. When I tried to create
  * and save together it tried to save the page rather than the file
  *
  * PHP Version 5
  *
  * @category   ProductsPlugin
  * @package    WebWorksWebMe
  * @subpackage Products_Plugin
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
 */
$filename = $_REQUEST['filename'];
$dir = $_REQUEST['dir'];
header('Content-Type: text/csv');
header('Content-Dispositon: attachment; filename="'.$filename.'"');
readfile($dir.'/'.$filename);
