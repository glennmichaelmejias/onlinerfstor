<?php
	include 'db/dbconnect.php';
	$db = new Mysqlm();
	$db->connect();
	$db->mstarttransaction();
	for($a = 710942;$a<789705;$a++){
		//$db->mquery("insert into requests(date,datetoday,requestnumber,requestgroup,requesttypevalue,userid,thetype,tortype)
                 //  values('05/20/2017',CURRENT_TIMESTAMP,60,39,'error',9,2,1)");
		$db->mquery("delete from requests where id=".$a);
	}
	$db->mcommit();
?>