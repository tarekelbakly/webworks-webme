<?php
function dbAll($query,$key='') {
	$q = dbQuery($query);
	if(PEAR::isError($q)){
		echo 'dbAll: '.$q->getMessage();
		exit;
	}
	$results=$q->fetchAll();
	if(!$key)return $results;
	$arr=array();
	foreach($results as $r)$arr[$r[$key]]=$r;
	return $arr;
}
function dbInit(){
	require_once 'MDB2.php';
	if(isset($GLOBALS['db']))return $GLOBALS['db'];
	global $DBVARS;
	$dsn = 'mysql://' . $DBVARS['username'] . ':' . $DBVARS['password'] . '@' . $DBVARS['hostname'] . '/' . $DBVARS['db_name'];
	$db = &MDB2::connect($dsn);
	$db->num_queries=0;
	if(Pear::isError($db)){
		echo '<p>Error connecting to database.</p><p>Please make sure the access details are correct, and that the server has the Pear packages MDB2 and MDB2_Driver_mysql installed.</p>';
		exit;
	}
	$db->setCharset('utf8');
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	$GLOBALS['db']=$db;
	return $db;
}
function dbOne($query, $field='') {
	$r = dbRow($query);
	return $r[$field];
}
function dbQuery($query){
	$db=dbInit();
	$q = $db->query($query);
	if(PEAR::isError($q)){
		echo 'dbQuery:: '.$q->getMessage();
		exit;
	}
	$db->num_queries++;
	return $q;
}
function dbRow($query) {
	$q = dbQuery($query);
	if(PEAR::isError($q)){
		echo 'dbRow:: '.$q->getMessage();
		exit;
	}
	return $q->fetchRow();
}
