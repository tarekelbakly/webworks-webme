<?php
function startElement($parser, $name, $attrs) {
    global $parentElements;
	global $currentElement;
	global $currentTSSCheck;
	
	array_push($parentElements, $name);
	$currentElement = join("_", $parentElements);

	foreach ($attrs as $attr => $value) {
		if ($currentElement == "RESPONSE_TSS_CHECK" and $attr == "ID") {
			$currentTSSCheck = $value;
		}

		$attributeName = $currentElement."_".$attr;
		// print out the attributes..
		//print "$attributeName\n";

		global $$attributeName;
		$$attributeName = $value;
	}

	// uncomment the "print $currentElement;" line to see the names of all the variables you can 
	// see in the response.
	// print $currentElement;

}
function cDataHandler($parser, $cdata) {
	global $currentElement;
	global $currentTSSCheck;
	global $TSSChecks;

	if ( trim ( $cdata ) ) { 
		if ($currentTSSCheck != 0) {
			$TSSChecks["$currentTSSCheck"] = $cdata;
		}

		global $$currentElement;
		$$currentElement .= $cdata;
	}
	
}
function endElement($parser, $name) {
    global $parentElements;
	global $currentTSSCheck;

	$currentTSSCheck = 0;
	array_pop($parentElements);
}
function os_submitRealex($merchantid,$secret,$account,$amount,$cardnumber,$cardname,$cardtype,$expdate,$formid){
	$parentElements = array();
	$TSSChecks = array();
	$currentElement = 0;
	$currentTSSCheck = "";
	$currency = "EUR";

	if($cardtype=='mastercard')$cardtype='MC';
	if($cardtype=='lasercard')$cardtype='LASER';
	
	//Creates timestamp that is needed to make up orderid
	$timestamp = strftime("%Y%m%d%H%M%S");
	mt_srand((double)microtime()*1000000);
	
	//You can use any alphanumeric combination for the orderid.Although each transaction must have a unique orderid.
	$orderid = $formid.'-'.$timestamp."-".mt_rand(1, 999);
	
	// This section of code creates the md5hash that is needed
	$tmp = "$timestamp.$merchantid.$orderid.$amount.$currency.$cardnumber";
	$md5hash = md5($tmp);
	$tmp = "$md5hash.$secret";
	$md5hash = md5($tmp);
	
	
	// Create and initialise XML parser
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "cDataHandler");
	
	//A number of variables are needed to generate the request xml that is send to Realex Payments.
	$xml = "<request type='auth' timestamp='$timestamp'>
		<merchantid>$merchantid</merchantid>
		<account>$account</account>
		<orderid>$orderid</orderid>
		<amount currency='$currency'>$amount</amount>
		<card> 
			<number>$cardnumber</number>
			<expdate>$expdate</expdate>
			<type>$cardtype</type> 
			<chname>$cardname</chname> 
		</card> 
		<autosettle flag='1'/>
		<md5hash>$md5hash</md5hash>
		<tssinfo>
			<address type=\"billing\">
				<country>ie</country>
			</address>
		</tssinfo>
	</request>";
	
	// Send the request array to Realex Payments
	$ch = curl_init();    
	curl_setopt($ch, CURLOPT_URL, "https://epage.payandshop.com/epage-remote.cgi");
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_USERAGENT, "payandshop.com php version 0.9"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // this line makes it work under https 
	$response = curl_exec ($ch);     
	curl_close ($ch); 
	
	//Tidy it up
	$response = eregi_replace ( "[[:space:]]+", " ", $response );
	$response = eregi_replace ( "[\n\r]", "", $response );
	
	$response_number=trim(preg_replace('/.*<result>([^<]*)<\/result.*>/','$1',join('',explode("\n",$response))));
	$response=trim(preg_replace('/<[^>]*>/','',join('',explode("\n",$response))));
	$error_message=preg_replace('/^[^ ]* | [^ ]*$/','',$response);
	
//	print $TSSChecks["3202"];
	
	// garbage collect the parser.
	xml_parser_free($xml_parser);
	return array($response_number,$response);
}
