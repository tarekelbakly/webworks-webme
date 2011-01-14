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
		$tmpUrl='http://127.0.0.1/?page=logout';
		curl_setopt($tmpCurlHandle, CURLOPT_URL, $tmpUrl);
		curl_exec($tmpCurlHandle);
		curl_close($tmpCurlHandle);
		$this->curl_handle = curl_init();
		$this->url = 'http://127.0.0.1/ww.admin/pages.php';
		curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, 1);
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
		$fields
			=array(
				'email'=>$email,
				'password'=>$pass,
				'action'=>'login'
			);
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $fields);
		$response=curl_exec($this->curl_handle);
		$dir=dirname(__FILE__);
		$hasPageForm=strpos($response, 'div id="pages-wrapper"');
		$hasLoginTab=strpos($response, 'div id="admin-login"');
		$this->assertEquals(false, $hasLoginTab);
		$this->assertNotEquals(false, $hasPageForm);
	}
	/**
	  * Tests a user logging in with the wrong password
	  *
	**/
	function testWrongPasswordLogin() {
		$email='bhamilton';
		$password='wrongpass';
		$fields
			=array(
				'email'=>$email,
				'password'=>$password,
				'action'=>'login'
			);
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $fields);
		$response=curl_exec($this->curl_handle);
		$hasPageForm=strpos($response, 'div id="pages-wrapper"');
		$hasLoginForm=strpos($response, 'div id="admin-login"');
		$this->assertEquals(false, $hasPageForm);
		$this->assertNotEquals(false, $hasLoginForm);
	}
	function testNonAdminLogin() {
		$email='belinda0304@hotmail.com';
		$password='belindapass';
		$fields
			=array(
				'email'=>$email,
				'password'=>$password,
				'action'=>'login'
			);
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $fields);
		$response=curl_exec($this->curl_handle);
		$hasPageForm=strpos($response, 'div id="pages-wrapper"');
		$hasLoginForm=strpos($response, 'div id="admin-login"');
		$this->assertEquals(false, $hasPageForm);
		$this->assertNotEquals(false, $hasLoginForm);
	}
}
