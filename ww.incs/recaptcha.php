<?php
require_once $_SERVER['DOCUMENT_ROOT']
	.'/ww.incs/recaptcha-php-1.11/recaptchalib.php';
define('RECAPTCHA_PRIVATE','6LffZAwAAAAAANXRgBLgD51o6fZnvXknLNNXgCUr');
define('RECAPTCHA_PUBLIC','6LffZAwAAAAAALA70eSDf73p4DTddBu0jgULjukb'); 
function Recaptcha_getHTML(){
	return '<script>var RecaptchaOptions = { theme: "custom", lang: "en", custom_theme_widget: "recaptcha_widget" };</script> <div id="recaptcha_widget" style="display:none"> <div id="recaptcha_image"></div> <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div> <span class="recaptcha_only_if_image">Enter the words above:</span> <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /> </div> <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k='.RECAPTCHA_PUBLIC.'"> </script>';
}
