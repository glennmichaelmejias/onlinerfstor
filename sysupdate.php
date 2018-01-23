<?php
	if(isset($_GET['loadtable'])){
		include 'db/dbconnect.php';
		echo '<table mytable fullwidth>
				<tr>
					<th col2>Transaction Date</th>
					<th displaynone>Number</th>
					<th displaynone>Request Mode</th>
					<th displaynone>Type of Request</th>
					<th col2>Company Name</th>
					<th col2>Business Unit</th>
					<th col1>Requested By</th>
					<th col1>Request Type</th>
					<th col1>Total Requests</th>
					<th col1 centertext>Status</th>
					<th col1 centertext>Action</th>
				</tr>';
			$query = mysqlm_query("select
									DATE_FORMAT(datetoday,'<span colorgreen>%a &bull;</span>  <span color1>%h:%i %p &bull;</span> %b %d, %Y'),
									requesttype,com.companyname as co,
									if(if((count(ifnull(`status`,'a'))-count(`status`='Approved'))>0,'Pending','Approved')='Approved',
										if((count(ifnull(`executed`,'a'))-count(`executed`='Approved'))>0,'Pending',
											if((count(ifnull(a.buheadid,'a'))-count(a.buheadid='Approved'))>0,'Pending','Approved')
										),'Pending'
									) as qwer,
									themode,LPAD(requestnumber,4,0),if(a.thetype=1,count(requestgroup),1) as thetotalrequest,requestgroup,bu.businessunit as bu,concat(firstname,' ',lastname),
									if(a.thetype=1,'RFS','TOR') as therequesttype,
									if(if((count(ifnull(`iadstatus`,'a'))-count(`iadstatus`='Approved'))>0,'Pending','Approved')='Approved',
										if((count(ifnull(`executed`,'a'))-count(`executed`='Approved'))>0,'Pending',
											if((count(ifnull(a.buheadid,'a'))-count(a.buheadid='Approved'))>0,'Pending','Approved')
										),'Pending'
									) as qwer2,a.thetype as thetype,
									if(ifnull(a.softsysstatus,'Pending')='Pending','Pending',
										if(ifnull(a.executed,'Pending')='Pending','Pending','Approved')
									) as softsysstatus
									from requests a,typeofrequest b,requestmode c,users d,tblcompany com,tblbusinessunit bu
									where ((a.typeofrequest = b.id and a.requestmode = c.id and d.id = a.userid and d.businessunitid = bu.id and com.id = bu.companyid and a.thetype=1) and a.typeofrequest=7)
									and (".(myGET('filternumber')==""?"(DATE_FORMAT(a.datetoday,'%m/%Y')=DATE_FORMAT(STR_TO_DATE(".mySTRget('date').",'%m/%Y'),'%m/%Y') or ".mySTRget('date')."=\"all\")":
										"a.requestnumber = '".ltrim(myGET('filternumber'),'0')."'").")
									".(myGET('filterbu')=='All Business Unit'?"":"and (bu.businessunit like '%".myGET('filterbu')."%')")."
									group by a.requestgroup ".(myGET('showapproved')=='unchecked'?"Having softsysstatus='Pending'":"")."
									order by datetoday desc;");
			
			while($row=mysqlm_fetch_array($query)){
				//($row['thetype']==1?$thestatus=$row['qwer']:$thestatus=$row['qwer2']);
				$thestatus=$row['softsysstatus'];
				echo '<tr trhoverable>
						<td>'.$row[0].'</td>
						<td displaynone>'.$row[5].'</td>
						<td displaynone>'.$row[4].'</td>
						<td displaynone>'.$row[1].'</td>
						<td>'.$row['co'].'</td>
						<td>'.$row['bu'].'</td>
						<td>'.$row[9].'</td>
						<td centertext>'.$row['therequesttype'].'</td>
						<td centertext>'.$row['thetotalrequest'].'</td>
						<td centertext class="tdrequestsstatus" '.$thestatus.'>'.$thestatus.'</td>
						<td centertext>
							<div iconbtn onclick="iconrequestopenclicked(\''.$row[7].'\')" title="open"><i class="fa fa-list"/></div>
						</td>
					</tr>';
			}
		echo '</table>';
		if(mysqlm_rowcount($query)==0){
			echo '<div mygroup col12 style="background-color:white" centertext paddingedall bbox>No results.</div>';
		}
		breakhere($con);
	}
	elseif(isset($_GET['openrequest'])){
		include 'db/dbconnect.php';
		$requestgroup = mySTRget('requestgroup');
		$query2 = mysqlm_query("select thetype from requests where requestgroup = ".$requestgroup);
		$row2 = mysqlm_fetch_array($query2);
		$query = mysqlm_query("select LPAD(requestnumber,4,0),`date`,themode,requesttype,
							if(IFNULL(`softsysstatus`,'Pending')='Pending','Pending','Approved') as softsysstatus,a.id,
							if(IFNULL(`executed`,'Pending')='Pending','Pending','Approved'),
							if(IFNULL(a.buheadid,'Pending')='Pending','Pending','Approved') as buheadid,thetype
							from requests a,typeofrequest b,requestmode c 
							where a.typeofrequest = b.id and a.requestmode = c.id and a.requestgroup = ".mySTRget('requestgroup')." 
							order by a.requestnumber");
		echo '<table mytable fullwidth>
			<tr>
			<th col1>Number</th>
			<th>Date</th>
			<th>Mode</th>
			<th>Type</th>
			<th centertext col3>Corporate Audit Manager and Compliance Officer</th>
			<th centertext col1>Executed</th>
			<th col1 centertext>Action</th></tr>
		';
		while($row=mysqlm_fetch_array($query)){
			echo '<tr trhoverable>
					<td fontbold colorred centertext>'.$row[0].'</td>
					<td>'.$row[1].'</td>
					<td>'.$row[2].'</td>
					<td>'.$row[3].'</td>
					<td class="tdrequestsstatus" centertext '.$row['softsysstatus'].'>'.$row['softsysstatus'].'</td>
					<td class="tdrequestsstatus" centertext '.$row[6].'>'.$row[6].'</td>
					<td col1 centertext>
						<div iconbtn coli title="view details" onclick="iconrequestmodeviewclicked(\''.$row[5].'\')"><i class="fa fa-list"/></div>
					</td>
				</tr>';
		}
		echo '</table>';
		breakhere($con);
	}
	elseif(isset($_GET['requestmodeview'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("select * from requests where id =".mySTRget('requestid'));
		$row = mysqlm_fetch_array($query);
		$query3 = mysqlm_query("select companyname,businessunit,address,contactnumber,concat(u.firstname,' ',u.lastname) as fullname from tblcompany c,users u,tblbusinessunit bu where u.id=".$row['userid']." and u.businessunitid = bu.id and c.id = bu.companyid");
		$row2 = mysqlm_fetch_array($query3);
		$requesttypevaluelabel="";
		echo '<div mygroup col6 nopadding displaytablecell>';
			echo '<div mygroup noborder col12>
					<div col5 coli colctrl marginedtop>Company Name</div><input class="txtcompanyname" readonly type="text" value="'.$row2['companyname'].'" col7/>
					<div col5 coli colctrl marginedtop>Business Unit</div><input class="txtbusinessunit" readonly type="text" value="'.$row2['businessunit'].'" col7/>
					<div col5 coli colctrl marginedtop>Contact Number</div><input class="txtcontactno" readonly type="text" value="'.$row2['contactnumber'].'" col7/>
					<div col5 coli colctrl marginedtop>Date</div><input class="txtdate" readonly type="text" value="'.$row['date'].'"  col7/>
					<div col5 coli colctrl marginedtop>Address</div><input class="txtaddress" readonly type="text" value="'.$row2['address'].'"  col7/>
					<div col5 coli colctrl marginedtop>Requested by</div><input class="txtaddress" readonly type="text" value="'.$row2['fullname'].'" col7/>';
			echo '</div>';
			echo '<div mygroup noborder marginedtop hasbordertop col12>';
				echo '<div col6 coli>
						<div colctrl fontbold>Type of System</div>';
						$query2 = mysqlm_query("select systemtype,id from systemtype where id=".$row['systemtype']);
						$row2 = mysqlm_fetch_array($query2);
						echo '<div coli col12 marginedbottom><div class="mycheckbox chkrequesttype" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
						$requesttypevaluelabel = $requesttypevaluelabel.$row2[0];
				echo '</div>';
				echo '<div col6 coli>
						<div colctrl fontbold>Request mode</div>';
						$query2 = mysqlm_query("select themode,id from requestmode where id='".$row['requestmode']."'");
						$row2 = mysqlm_fetch_array($query2);
						echo '<div coli col12 marginedbottom><div class="mycheckbox chkrequestmode" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
						$requesttypevaluelabel = '(<span color1>'.$row2[0].'</span>) ';
				echo '</div>';
			echo '</div>';
			echo '<div mygroup  noborder hasbordertop col12>
					<div fontbold>Purpose</div>
					<textarea resizenone readonly bbox style="height:50px" col12>'.$row['purpose'].'</textarea>';
			echo '</div>';
		echo '</div>';
		
		echo '<div mygroup col6 nopadding displaytablecell noborderleft>';
			echo '<div mygroup noborder col12>
					<div fontbold>'.$requesttypevaluelabel.'</div>
					<textarea resizenone readonly bbox style="height:100px" col12>'.str_replace("="," = ",$row['requesttypevalue']).'</textarea>';
			echo '</div>';
			if(isset($_GET['noedit'])){
				echo '<div mygroup marginedtop noborder hasbordertop col12>
						<div fontbold>Remarks</div>
						<textarea class="txtremarks" readonly resizenone bbox style="height:150px" col12>'.$row['remarks'].'</textarea>';
				echo '</div>';
			}
			else{
				echo '<div mygroup marginedtop noborder hasbordertop col12>
						<div fontbold>Remarks</div>
						<textarea class="txtremarks" resizenone bbox style="height:100px" col12>'.$row['remarks'].'</textarea>
						<button col3 floatright onclick="btnsaveremarksclicked(\''.myGET('requestid').'\')">Save</button>';
				echo '</div>';
			}
			
			echo '<div mygroup marginedtop noborder hasbordertop col12>
					<div buttongroup col6 floatright>
						<button col6 onclick="inrequestmodeapproveclicked('.$row['id'].')">Approve</button>
						<button col6>Disapprove</button>
					</div>
				</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['executerequest'])){
		include'db/dbconnect.php';
	//	$query = mysqlm_query("select IFNULL(`status`,'Pending') from requests where id=".myGET('requestid'));
	//	if(mysqlm_fetch_array($query)[0]=='Pending'){
	//		echo 'failed';
	//	}
	//	else{
			mysqlm_query("update requests set softsysstatus=".getuserid()." where id=".myGET('requestid'));
	//		echo 'success';
	//	}
		
		breakhere($con);
	}
	elseif(isset($_GET['saveremarks'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("update requests set remarks=".mySTRget('remarks')." where id=".myGET('requestid'));
		breakhere($con);
	}
?>
<br/>
<?php
	include 'db/dbconnect.php';
	checksession();
	if(getusertype()!=6 and getusertype()!=0){
		echo '<br/>You should be a user type who can execute the request to access this page.';
		breakhere($con);
	}
?>
<style>
	.tdrequestsstatus[Approved]{
		color:green;
	}
	.tdrequestsstatus[Pending]{
		color:red;
	}
</style>
<div class="myframe">
	<h3 fontbold><span color1>Corporate Audit Manager and Compliance Officer</span></h3>
	<div coli col12>
		<div mytoolsgroup col6 displaytablecell nopadding>
			<div mytoolsgroup col5 displaytablecell noborder hasborderright>
				<div coli marginedright fontbold>Filter Date</div>
				<button class="monthselector filterrequestmonth" datevalue="">This month</button>
			</div>
			<div mytoolsgroup col7 displaytablecell noborder>
				<div class="selectbusinessunit" myselect col6 placeholder="Business unit">
					<?php
						$query = mysqlm_query("select businessunit,id from tblbusinessunit where active='1' order by businessunit");
						echo '<div myselectoption value="">All Business Unit</div>';
						while($row=mysqlm_fetch_array($query)){
							echo '<div myselectoption value="'.$row['id'].'">'.$row[0].'</div>';
						}
					?>
				</div>
				<input class="filternumber" centertext colorred type="text" col5  placeholder="Control Number"/>
			</div>
		</div>
		<div mytoolsgroup col6 displaytablecell noborderleft>
			<div coli col3 marginedleft><div class="mycheckbox chkshowapproved" value="checked" coli></div><div coli marginedleft> Show approved</div></div>
		</div>
	</div>
	<div class="requeststable" mygroup col12 bordertopwhite>
		
	</div>
</div>
<script type="text/javascript">
	var filterbu = "";
	$(".selectbusinessunit").myselect(function(elinputval){
		filterbu = elinputval;
		loadrequests();
	});
	$(".mycheckbox").mycheckbox();
	$(".chkshowapproved").mycheckboxonclick(function(){
		loadrequests();
	});
	$(".monthselector").monthselector(function(){
		loadrequests();
	});
	$(".filternumber").on('keyup',function(evt){
		if(evt.keyCode==13){
			loadrequests();
		}
	});
	loadrequests();
	var mymodal = new MyModal();
	var mymodal2 = new MyModal();
	$(".dateselector").dateselector();

	function loadrequests(){
		var reqdate = $(".filterrequestmonth").datevalue();
		$.get("sysupdate.php?loadtable&date="+reqdate+"&showapproved="+$(".chkshowapproved").mycheckboxvalue()+"&filterbu="+filterbu+
							"&filternumber="+$(".filternumber").val(),function(result){
			$(".requeststable").html(result);
		});
	}
	function iconrequestopenclicked(requestgroup){
		mymodal.showcustom("Request","60%");
		mymodal.settag(requestgroup);
		$.get("sysupdate.php?openrequest&requestgroup="+requestgroup,function(result){
			mymodal.body(result);
		});
	}
	function iconrequestmodeviewclicked(requestid){
		mymodal2.showcustom("System/Software");
		$.get("sysupdate.php?requestmodeview&requestid="+requestid,function(result){
			mymodal2.body(result);
			$(".mycheckbox").mycheckbox();
		});
	}
	function inrequestmodeapproveclicked(requestid){
		mymodal2.showloading();
		$.get("sysupdate.php?executerequest&requestid="+requestid,function(result){
			mymodal2.close();
			mymodal.showloading();
			$.get("sysupdate.php?openrequest&requestgroup="+mymodal.gettag(),function(result){
				mymodal.body(result);
			});
			showMyAlert("Request Approved!","");
		});
	}
	function iconrequestmodeviewclicked2(requestid){
		mymodal2.showcustom("Requests","55%");
		$.get("requests.php?requestmodeview2&requestid="+requestid,function(result){
			mymodal2.body(result);
			$(".mycheckbox").mycheckbox();
		});
	}
	function btnsaveremarksclicked(requestid){
		var remarks = $(".txtremarks").val();
		var prevbody = mymodal2.getbody();
		mymodal2.showloading();
		$.get("sysupdate.php?saveremarks&remarks="+remarks+"&requestid="+requestid,function(result){
			showMyAlert("Remarks saved!","");
			mymodal2.close();
		});
	}
</script>