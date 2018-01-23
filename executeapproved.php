<?php
	if(isset($_GET['loadtable'])){//load main table
		include 'db/dbconnect.php';
		$currentpage = (myGET('currentpage') * 12);
		echo '<table mytable fullwidth>
				<tr>
					<th style="width:8%">Transaction Date</th>
					<th centertext style="width:3%">Request Type</th>
					<th style="width:4%" centertext>Number</th>
					<th displaynone>Request Mode</th>
					<th displaynone>Type of Request</th>
					<!--<th style="width:14%">Company Name</th>-->
					<th style="width:10%">Business Unit</th>
					<th col1>Requested By</th>
					
					<!--<th col1>Total Requests</th>-->
					<th col1>Approved By</th>
					<th style="width:2%" centertext>Action</th>
				</tr>';
			$executerole = mysqlm_query("select buid from executerole where userid=".getuserid());
			$exrolecount = mysqlm_rowcount($executerole);
			$query2 = mysqlm_query("select UPPER(concat(firstname,' ',lastname)) as fullname,id from users");
			$arr = initusers();
			// while($row=mysqlm_fetch_array($query2)){
				// $arr[$row['id']] = $row['fullname'];
			// }
			
			
			//code for requesttyperole
			
			$requesttyperolerfs = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='RFS' and usertypeid=3");
			$requesttyperolerfsids = mysqlm_fetch_array($requesttyperolerfs)[0];
			$requesttyperoletor = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='TOR' and usertypeid=3");
			$requesttyperoletorids = mysqlm_fetch_array($requesttyperoletor)[0];
			//end of requesttyperole
			
			$butaskroles="'".initbutaskroles()."'";
			$executeids = mysqlm_fetch_array(mysqlm_query("select GROUP_CONCAT(buid SEPARATOR ',') from executerole where userid=".getuserid()))[0];
			$query = mysqlm_query("select
								DATE_FORMAT(datetoday,'<span colorgreen>%a &bull;</span>  <span color1>%h:%i %p &bull;</span> %b %d, %Y'),
								'',com.companyname as co,
								if(if((count(ifnull(`status`,'a'))-count(`status`='Approved'))>0,'Pending','Approved')='Approved',
									if((count(ifnull(`executed`,'a'))-count(`executed`='Approved'))>0,'Pending',
										if((count(ifnull(a.buheadid,'a'))-count(a.buheadid='Approved'))>0,'Pending','Approved')
									),'Pending'
								) as rfsstatus,
								'',LPAD(requestnumber,4,0),
								'' as thetotalrequest,
								requestgroup,bu.businessunit as bu,UPPER(concat(d.firstname,' ',d.lastname)),
								if(a.thetype=1,'RFS','TOR') as therequesttype,
								if(if((count(ifnull(`iadstatus`,'a'))-count(`iadstatus`='Approved'))>0,'Pending','Approved')='Approved',
									if((count(ifnull(`executed`,'a'))-count(`executed`='Approved'))>0,'Pending',
										if((count(ifnull(a.buheadid,'a'))-count(a.buheadid='Approved'))>0,'Pending','Approved')
									),'Pending'
								) as torstatus,a.thetype as thetype,
								if(ifnull(a.softsysstatus,'Pending')='Pending','Pending',
									if(ifnull(a.executed,'Pending')='Pending','Pending','Approved')
								) as softsysstatus,
								a.typeofrequest as typeofrequest,
								a.executed as approvedby,
								bu.id as buid
								from requests a,users d,tblcompany com,tblbusinessunit bu
								where (((a.thetype=1) or
									(a.thetype=2))".
									($exrolecount>0?"and bu.id in (".$executeids.")":"").
								")
								
								
								and (
										".(strlen($requesttyperolerfsids)>0?"(a.typeofrequest in (".$requesttyperolerfsids."))":"false")."
										or ".(strlen($requesttyperoletorids)>0?"  (a.tortype in (".$requesttyperoletorids."))":"false").
										((strlen($requesttyperolerfsids)==0 and strlen($requesttyperoletorids)==0)?" or true":"")."
										)
								
								and a.userid = d.id and d.businessunitid=bu.id and com.id=bu.companyid 
								and (getstatusapproved(bu.id,3,a.executed,$butaskroles,a.thetype) 
									and getstatusapproved(bu.id,5,a.iadstatus,$butaskroles,a.thetype) 
									and getstatusapproved(bu.id,2,a.buheadid,$butaskroles,a.thetype) 
									and getstatusapproved(bu.id,4,a.status,$butaskroles,a.thetype)
									)
								and (".(myGET('filternumber')==""?"(DATE_FORMAT(a.datetoday,'%m/%Y')=DATE_FORMAT(STR_TO_DATE(".mySTRget('date').",'%d/%m/%Y'),'%m/%Y') or ".mySTRget('date')."=\"all\")":
									"a.requestnumber = '".ltrim(myGET('filternumber'),'0')."'").")
								".(myGET('filterbu')=='All Business Unit'?"":"and (bu.businessunit like '%".myGET('filterbu')."%')")."
								and (".(myGET('showrfs')=="unchecked"?"thetype!=1":"true")." and
										".(myGET('showtor')=="unchecked"?"thetype!=2":"true").")
								group by a.id ".(myGET('showapproved')=='unchecked'?"Having if(typeofrequest=7,softsysstatus='Pending',if(therequesttype='RFS',rfsstatus='Pending',torstatus='Pending')) ":"")."
								 desc limit 12 offset ".$currentpage.";");
			$thestatus;
			while($row=mysqlm_fetch_array($query)){
				($row['thetype']==1?($row['typeofrequest']!=7?$thestatus=$row['rfsstatus']:$thestatus=$row['softsysstatus']):$thestatus=$row['torstatus']);
				$approvedby = $arr[($row['approvedby']==""?0:$row['approvedby'])];
				echo '<tr trhoverable>
						<td>'.$row[0].'</td>
						<td centertext>'.$row['therequesttype'].'</td>
						<td centertext colorred fontbold>'.$row[5].'</td>
						<td displaynone>'.$row[4].'</td>
						<td displaynone>'.$row[1].'</td>
						<!--<td font11>'.$row['co'].'</td>-->
						<td font11>'.$row['bu'].'</td>
						<td>'.$row[9].'</td>
						<!--<td centertext>'.$row['thetotalrequest'].'</td>-->
						<!--<td centertext class="tdrequestsstatus" '.$thestatus.'>'.$thestatus.'</td>-->
						<td>'.($approvedby=="Admin Admin"?"":$approvedby).'</td>
						<td centertext>
							<div iconbtn onclick="iconrequestopenclicked(\''.$row[7].'\',\''.$row['buid'].'\')" title="open"><i class="fa fa-list"/></div>
						</td>
					</tr>';
			}
		echo '</table>';
		if(mysqlm_rowcount($query)==0){
			echo '<div mygroup col12 style="background-color:white" centertext paddingedall bbox>No results.</div>';
		}
		breakhere($con);
	}
	elseif(isset($_GET['generatereport'])){//generatereport
		include 'db/dbconnect.php';
		echo '<div class="selectbusinessunit2" myselect col6 placeholder="Business unit">';
				$query = mysqlm_query("select businessunit,id from tblbusinessunit where active='1' order by businessunit");
				echo '<div myselectoption value="">All Business Unit</div>';
				while($row=mysqlm_fetch_array($query)){
					echo '<div myselectoption value="'.$row['id'].'">'.$row[0].'</div>';
				}
					
		echo'</div>';
		breakhere($con);
	}
?>
<br/>
<?php
	include 'db/dbconnect.php';
	checksession();
	// if(getusertype()!=3 and getusertype()!=0){
		// echo '<br/>You should be a user type who can execute the request to access this page.';
		// breakhere($con);
	// }
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
	<!--<h3 fontbold><span color1><?php//echo getusertypename()?></span></h3>-->
	<h3 fontbold><span color1>Implement, Adjust/Reprint</span></h3>
	<div coli col12>
		<div mytoolsgroup col7 displaytablecell nopadding>
			<div mytoolsgroup displaytablecell noborder col4>
				<div coli marginedright fontbold>Filter Date</div>
				<button class="monthselector filterrequestmonth" datevalue="">This month</button>
				
				
				<!--<button onclick="generatereportclicked()" marginedleft>Generate Report</button>-->
			</div>
			<div mytoolsgroup displaytablecell noborder hasborderleft col4>
				<div class="selectbusinessunit" myselect col6 placeholder="Business unit">
					<?php
						$query = mysqlm_query("select businessunit,id from tblbusinessunit where active='1' order by businessunit");
						echo '<div myselectoption value="">All Business Unit</div>';
						while($row=mysqlm_fetch_array($query)){
							echo '<div myselectoption value="'.$row['id'].'">'.$row[0].'</div>';
						}
					?>
				</div>
				<input class="filternumber" title="Press Enter" centertext colorred type="text" col4 placeholder="Control Number"/>
			</div>
		</div>
		<div mytoolsgroup col5 displaytablecell noborderleft>
			<!--<div coli col3 marginedleft><div class="mycheckbox chkshowapproved" value="checked" coli></div><div coli marginedleft> Show approved</div></div>-->
			<div coli col2 marginedleft><div class="mycheckbox chkshowrfs" value="checked" coli></div><div coli marginedleft> RFS</div></div>
			<div coli col2 marginedleft><div class="mycheckbox chkshowtor" value="checked" coli></div><div coli marginedleft> TOR</div></div>
		</div>
	</div>
	<div mygroup col12 bordertopwhite>
		<div class="requeststable">
			
		</div>
		<div fullwidth displaytable>
			<div buttongroup class="mypagination" marginedtop floatright>
				
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var filterbu = "";
	var currentpage=0;
	$.get("execute.php?getmaxrecords",function(result){
		$(".mypagination").mypagination(result,function(selectedpage){
			currentpage=selectedpage;
			loadrequests();
		});
	});
	$(".selectbusinessunit").myselect(function(elinputval){
		filterbu = elinputval;
		loadrequests();
	});
	$(".monthselector").monthselector(function(){
		loadrequests();
	});
	$(".chkshowapproved").mycheckbox(function(){
		loadrequests();
	});
	$(".chkshowrfs").mycheckbox(function(){
		loadrequests();
	});
	$(".chkshowtor").mycheckbox(function(){
		loadrequests();
	});
	var mymodal = new MyModal();
	var mymodal2 = new MyModal();
	$(".filternumber").on('keyup',function(evt){
		if(evt.keyCode==13){
			loadrequests();
		}
	});
	loadrequests();
	function loadrequests(){
		var reqdate = $(".filterrequestmonth").datevalue();
		$.get("executeapproved.php?loadtable&date="+reqdate+"&showapproved="+$(".chkshowapproved").attr("value")+"&filterbu="+filterbu+
							"&filternumber="+$(".filternumber").val()+
							"&showrfs="+$(".chkshowrfs").mycheckboxvalue()+
							"&showtor="+$(".chkshowtor").mycheckboxvalue()+
							"&currentpage="+currentpage,function(result){
			$(".requeststable").html(result);
			refreshthis($(".bodyelements"));
		});
	}
	function iconrequestopenclicked(requestgroup,buid){
		selectedbuid = buid;
		mymodal.showcustom("Request","70%");
		mymodal.settag(requestgroup);
		$.get("requests.php?requestopen&requestgroup="+requestgroup+"&buid="+selectedbuid,function(result){
			mymodal.body(result);
		});
	}
	function iconrequestmodeviewclicked(requestid,checkstatus,typeofrequest){
		mymodal2.showcustom("Execute","60%");
		$.get((typeofrequest==7?"sysupdate":"execute") + ".php?requestmodeview&requestid="+requestid+"&noedit",function(result){
			mymodal2.body(result);
			mymodal2.settag(checkstatus);
			$(".mycheckbox").mycheckbox();
		});
	}
	function inrequestmodedisapproveclicked(requestid){
		showMyConfirm("Disapprove request?",function(){
			mymodal2.settag(mymodal2.getbody());
			mymodal2.showloading();
			$.get("execute.php?disapproverequest&requestid="+requestid,function(result){
				if(result=="success"){
					mymodal.close();
					mymodal2.close();
					showMyAlert("Request Disapproved!","");
					loadrequests();
				}
				else{
					showMyAlertError("Can only be disapprove by "+result+".","");
					mymodal2.body(mymodal2.gettag());
				}
			});
		},function(){});
	}
	function iconrequestmodeviewclicked2(requestid,checkstatus){
		mymodal2.showcustom("Requests","60%");
		$.get("execute.php?requestmodeview2&requestid="+requestid,function(result){
			mymodal2.body(result);
			mymodal2.settag(checkstatus);
			$(".mycheckbox").mycheckbox();
		});
	}
	function printrequestclicked(formtype,requestid,requestnumber){
		if(mymodal2.gettag()!='tananApproved'){
			showMyAlertError("Cannot print, Form Request still pending.",'');
			return;
		}
		showprint(formtype,requestid,requestnumber);
	}
	function btnsaveremarksclicked(requestid){
		var remarks = $(".txtremarks").val();
		$.post("execute.php",
				{saveremarks:emptyval,
				requestid:requestid,
				remarks:remarks},
				function(result){
					showMyAlert("Remarks saved!","");
				}
		);
	}
	// var modalshowapprove = new MyModal();
	// function showapproveclicked(requestid){
		// modalshowapprove.show("Approved Requests");
		// $.get("iad.php?showapprovetor&requestid="+requestid,function(result){
			// modalshowapprove.body(result);
		// });
	// }
	// function showapprovecloseclicked(){
		// modalshowapprove.close();
	// }
	// var modalgeneratereport = new MyModal();
	// function generatereportclicked(){
		// modalgeneratereport.show("Generate Report");
		// $.get("executeapproved.php?generatereport",function(result){
			// modalgeneratereport.body(result);
			// $(".selectbusinessunit2").myselect(function(elinputval){
				// filterbu = elinputval;
			// });
		// });
	// }
</script>