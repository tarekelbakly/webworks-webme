<?php
/**
  *
  * Login Tests
  *
  * PHP Version 5
  *
  * @category Tests
  * @package  Webworks_Webme
  * @author   Belinda Hamilton
  * @license  GPL Version 2
  * @link     www.webworks.ie
**/

require_once 'PHPUnit/Framework/TestCase.php';

class LoginTests extends PHPUnit_Framework_TestCase {
	private $curl_handle;
	private $url;

	/**
	  *
	  * Constructor
	**/
	function LoginTests() {
	}
	/**
	  * Sets up the variables
	**/
	function setUp() {
		$tmpCurlHandle=curl_init();
		$tmpUrl='http://webworks-webme/?page=logout';
		curl_setopt($tmpCurlHandle, CURLOPT_URL, $tmpUrl);
		curl_exec($tmpCurlHandle);
		curl_close($tmpCurlHandle);
		$this->curl_handle = curl_init();
		$this->url = 'http://webworks-webme/ww.admin';
		curl_setopt($this->curl_handle, CURLOPT_URL, $this->url);
		curl_setopt($this->curl_handle, CURLOPT_POST, true);
	}
	/**
	  * Closes the session
	**/
	function tearDown() {
		curl_close($this->curl_handle);
	}
	/**
	  * Login with correct credentials
	**/
	function testAuthorisedLogin() {
		$email='bhamilton@webworks.ie';
		$pass='ive8448';
		$fields=array(
			'email'=>$email,
			'password'=>$pass
		);
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $fields);
		$response=curl_exec($this->curl_handle);
		curl_($this->curl_handle);
		$this->assertEquals(true, isset($_SESSION['userdata']));
	}
}
