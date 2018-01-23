<?php
	session_start();
	function checksession(){
		if(!isset($_SESSION['userid'])){
			echo 'Session expired!<span color1 cursorpointer onclick="clickheretologinclicked()"> Click here</span> to login.';
			//echo 'onclick="function(){window.location.reload()}"'
			breakhere($con="");
		}
	}
	class Mysqlm{
		public $db=null;
		function connect(){
			$this->db = new PDO('mysql:host=127.0.0.1;dbname=unnamed','root','admin12345');
			//$this->db = new PDO('mysql:host=172.16.161.37;dbname=unnamed','root','itprog2013');
			return $this->db;
		}
		function mstarttransaction(){
			$this->db->beginTransaction();
		}
		function mcommit(){
			$this->db->commit();
		}
		function mysqlm_query($query){
			return $this->connect()->query($query);
		}
		function mquery($query){
			return $this->db->query($query);
		}
		function mysqlm_rowcount($query){
			return $query->rowCount();
		}
		function mysqlm_columncount($query){
			return $query->columnCount();
		}
		function mysqlm_fetch_array($query){
			return $query->fetch(PDO::FETCH_BOTH);
		}
		function mysqlm_fetch_column($query){
			return $query->fetch(PDO::FETCH_COLUMN);
		}
		function __destruct(){
			$this->db=null;
		}
	}
	$con="";
	function mysqlm_query($query){
		$mysqlm=new Mysqlm();
		return $mysqlm->mysqlm_query($query);
	}
	function mysqlm_rowcount($query){
		$mysqlm=new Mysqlm();
		return $mysqlm->mysqlm_rowcount($query);
	}
	function mysqlm_fetch_array($query){
		$mysqlm=new Mysqlm();
		return $mysqlm->mysqlm_fetch_array($query);
	}
	function mysqlm_real_escape_string($query){
		return $query;
	}
	function mysqlm_num_fields($query){
		$mysqlm=new Mysqlm();
		return $mysqlm->mysqlm_columncount($query);
	}
	function mysqlm_fetch_column($query){
		$mysqlm=new Mysqlm();
		return $mysqlm->mysqlm_fetch_column($query);
	}
	function breakhere($con){
		die();
	}
	function myGET($strgetname){
		return $_GET[$strgetname];
	}
	function mySTRget($strgetname){
		return '"'.$_GET[$strgetname].'"';
	}
	function mySTR($strname){
		return '"'.$strname.'"';
	}
	function mySTRpost($strpostname){
		return '"'.$_POST[$strpostname].'"';
	}
	function myPOST($strpostname){
		return $_POST[$strpostname];	
	}
	function strsing($str){
		return "'".$str."'";
	}
	function strdouble($str){
		return '"'.$str.'"';
	}
	function removecommas($number){
		return str_replace(",","",$number);
	}
	function python($file,$args){
		//echo $args;
		exec("taskkill /f /im python.exe");
		return exec("python2\\dist\\python\\".$file.' '.$args);
	}
	function setthesessions(){
		$query = mysqlm_query("select usertype,rfsonly from users where id='".$_SESSION['currentuserid']."'");
		$row = mysqlm_fetch_array($query);
		$_SESSION['usertype'] = $row[0];
		$_SESSION['rfsonly'] = $row[1];
		$query = mysqlm_query("select usertype from usertype where id = ".$row[0]);
		$row = mysqlm_fetch_array($query);
		$_SESSION['usertypename'] = $row[0];
		
	//	$query = mysqlm_query("select concat(firstname,' ',lastname) from users where id='".$_SESSION['currentuserid']."'");
		$query = mysqlm_query("select firstname,lastname from users where id='".$_SESSION['currentuserid']."'");
		$row = mysqlm_fetch_array($query);
		$_SESSION['userfirstname'] = $row [0];
		$_SESSION['userlastname'] = $row [1];
		
		$query = mysqlm_query("select id from users where id='".$_SESSION['currentuserid']."'");
		$_SESSION['userid'] = mysqlm_fetch_array($query)[0];
		
		$query = mysqlm_query("select businessunit,b.id from tblbusinessunit b, users u, tblcompany c where b.companyid = c.id and u.businessunitid = b.id and u.id='".$_SESSION['currentuserid']."'");
		$row = mysqlm_fetch_array($query);
		$_SESSION['buname'] = $row[0];
		$_SESSION['buid'] = $row[1];
		
		$query = mysqlm_query("select companyname,b.contactnumber,b.address from tblbusinessunit b, users u, tblcompany c where b.companyid = c.id and u.businessunitid = b.id and u.id='".$_SESSION['currentuserid']."'");
		$row = mysqlm_fetch_array($query);
		$_SESSION['companyname'] = $row[0];
		$_SESSION['contactnumber'] = $row[1];
		$_SESSION['address'] = $row[2];
	}
	function initusers(){
		$query2 = mysqlm_query("select UPPER(concat(firstname,' ',lastname)) as fullname,id from users");
		$arr = array();
		while($row=mysqlm_fetch_array($query2)){
			$arr[$row['id']] = $row['fullname'];
		}
		return $arr;
	}
	function initbu(){
		$query2 = mysqlm_query("select UPPER(businessunit) as bu,id from tblbusinessunit");
		$arr = array();
		while($row=mysqlm_fetch_array($query2)){
			$arr[$row['id']] = $row['bu'];
		}
		return $arr;
	}
	function initroles(){
		return array(
				"2" => array("buheadrole","Business Unit Head Manager"),
				"3" => array("executerole","Programmer/MIS"),
				"4" => array("gp4role","Group IV Manager"),
				"5" => array("iadrole","IAD")
			);
	}
	function getrfsonly(){
		return $_SESSION['rfsonly'];
	}
	function getusertype(){
		return $_SESSION['usertype'];
	}function getusertypename(){
		return $_SESSION['usertypename'];
	}
	function getuserfullname(){
		return ucwords($_SESSION['userfirstname'].' '.$_SESSION['userlastname']);
	}
	function getuserfirstname(){
		return ucwords($_SESSION['userfirstname']);
	}
	function getuserlastname(){
		return ucwords($_SESSION['userlastname']);
	}
	function getuserid(){
		return $_SESSION['userid'];
	}
	function getbuid(){
		return $_SESSION['buid'];
	}
	function getbuname($requestid=null){
		if($requestid==""){
			return $_SESSION['buname'];
		}
		else{
			$query = mysqlm_query("select userid,businessunit from requests where id = ".$requestid);
			$row = mysqlm_fetch_array($query);
			$buid = $row['businessunit'];
			if($buid==""){
				$query2 = mysqlm_query("select businessunitid from users where id = ".$row['userid']);
				$row2 = mysqlm_fetch_array($query2);
				$buid = $row2[0];
			}
			$query3 = mysqlm_query("select businessunit from tblbusinessunit where id=".$buid);
			return mysqlm_fetch_array($query3)[0];
		}
	}
	function getcompanyname($requestid=null){
		if($requestid==""){
			return $_SESSION['companyname'];
		}
		else{
			$query = mysqlm_query("select userid,businessunit from requests where id = ".$requestid);
			$row = mysqlm_fetch_array($query);
			$buid = $row['businessunit'];
			if($buid==""){
				$query2 = mysqlm_query("select businessunitid from users where id = ".$row['userid']);
				$row2 = mysqlm_fetch_array($query2);
				$buid = $row2[0];
			}
			$query3 = mysqlm_query("select companyname from tblcompany, tblbusinessunit where tblbusinessunit.companyid = tblcompany.id and tblbusinessunit.id=".$buid);
			return mysqlm_fetch_array($query3)[0];
		}
	}
	function getcontactnumber($requestid=null){
		if($requestid==""){
			return $_SESSION['contactnumber'];
		}
		else{
			$query = mysqlm_query("select userid,businessunit from requests where id = ".$requestid);
			$row = mysqlm_fetch_array($query);
			$buid = $row['businessunit'];
			if($buid==""){
				$query2 = mysqlm_query("select businessunitid from users where id = ".$row['userid']);
				$row2 = mysqlm_fetch_array($query2);
				$buid = $row2[0];
			}
			$query3 = mysqlm_query("select contactnumber from tblbusinessunit where id =".$buid);
			return mysqlm_fetch_array($query3)[0];
		}
	}
	function getaddress($requestid=null){
		if($requestid==""){
			return $_SESSION['address'];
		}
		else{
			$query = mysqlm_query("select userid,businessunit from requests where id = ".$requestid);
			$row = mysqlm_fetch_array($query);
			$buid = $row['businessunit'];
			if($buid==""){
				$query2 = mysqlm_query("select businessunitid from users where id = ".$row['userid']);
				$row2 = mysqlm_fetch_array($query2);
				$buid = $row2[0];
			}
			$query3 = mysqlm_query("select address from tblbusinessunit where id = ".$buid);
			return mysqlm_fetch_array($query3)[0];
		}
	}
	function checkstatus($arr){
		$approvedcount=0;
		$pendingcount=0;
		foreach($arr as $status){
			if($status=='Pending'){
				$pendingcount++;
			}
			elseif($status=='Approved'){
				$approvedcount++;
			}
		}
		if($pendingcount==count($arr)){
			$whatstatus='tananPending';
		}
		elseif($approvedcount==count($arr)){
			$whatstatus='tananApproved';
		}
		else{
			$whatstatus='trolls';
		}
		return $whatstatus;
	}
	function setcurrentbu($buid){
		$_SESSION['currentbu'] = $buid;
		$query = mysqlm_query("select a.contactnumber as cont,a.address as addr,b.companyname as comp,a.businessunit as bu from tblbusinessunit a, tblcompany b where a.companyid = b.id and a.id = ".$buid);
		$row = mysqlm_fetch_array($query);
		$_SESSION['companyname'] = $row['comp'];
		$_SESSION['contactnumber'] = $row['cont'];
		$_SESSION['address'] = $row['addr'];
		$_SESSION['buname'] = $row['bu'];
	}
	function getcurrentbu(){
		$bus = initbu();
		if(isset($_SESSION['currentbu'])){
			return $bus[$_SESSION['currentbu']];
		}
		else{
			return getbuname();
		}
	}
	function getcurrentbuid(){
		return $_SESSION['currentbu'];
	}
?>