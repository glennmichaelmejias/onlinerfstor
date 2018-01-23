<?php
	if(isset($_GET['loadrequesttable'])){//load main table
		include 'db/dbconnect.php';
		$currentpage = (myGET('currentpage') * 12);
		$bus = initbu();
		echo '<table mytable fullwidth>
			<tr>
				<th style="width:12%">Transaction Date</th>
				<th displaynone>Number</th>
				<th displaynone>Request Mode</th>
				<th displaynone>Type of Request</th>
				<th style="width:6%">Company Name</th>
				<th col2>Business Unit</th>
				<th style="width:12%">Requested By</th>
				<th col1 centertext style="width:5%">Request Type</th>
				<!--<th col1>Total Requests</th>-->
				<th col1 centertext>Status</th>
				<th style="width:5%" centertext>Action</th>
			</tr>';
			
			
			//code for requesttyperole
			$requesttyperolerfs = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='RFS' and usertypeid=5");
			$requesttyperolerfsids = mysqlm_fetch_array($requesttyperolerfs)[0];
			$requesttyperoletor = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='TOR' and usertypeid=5");
			$requesttyperoletorids = mysqlm_fetch_array($requesttyperoletor)[0];
			//end of requesttyperole
			//showquery();
			$query = mysqlm_query("select
									DATE_FORMAT(datetoday,'<span colorgreen>%a &bull;</span>  <span color1>%h:%i %p &bull;</span> %b %d, %Y'),
									com.companyname as co,
									if(if((count(ifnull(`iadstatus`,'a'))-count(`iadstatus`='Approved'))>0,'Pending','Approved')='Approved',
										'Approved','Pending') as qwer,
									LPAD(requestnumber,4,0),if(a.thetype=1,count(requestgroup),1) as thetotalrequest,requestgroup,bu.businessunit as bu,concat(firstname,' ',lastname) as fullname,
									if(a.thetype=1,'RFS','TOR') as therequesttype,requestgroup,a.businessunit as bu2,bu.id as buid,
									a.remarks as remarks
									from requests a,users d,tblcompany com,tblbusinessunit bu
									where 
									(bu.companyid = com.id and 
											(a.thetype=2 or a.thetype=1) 
											and a.userid = d.id and d.businessunitid=bu.id and bu.id in (select buid from iadrole where userid=".getuserid()."))
									and (
										".(strlen($requesttyperolerfsids)>0?"(a.typeofrequest in (".$requesttyperolerfsids."))":"false")."
										or ".(strlen($requesttyperoletorids)>0?"  (a.tortype in (".$requesttyperoletorids."))":"false").
										((strlen($requesttyperolerfsids)==0 and strlen($requesttyperoletorids)==0)?" or true":"")."
										)
										
									and (".(myGET('filternumber')==""?"(DATE_FORMAT(a.datetoday,'%m/%Y')=DATE_FORMAT(STR_TO_DATE(".mySTRget('date').",'%d/%m/%Y'),'%m/%Y') or ".mySTRget('date')."=\"all\")":
										"a.requestnumber = '".ltrim(myGET('filternumber'),'0')."'").")
									and (".(myGET('showrfs')=="unchecked"?"thetype!=1":"true")." and
										".(myGET('showtor')=="unchecked"?"thetype!=2":"true").")
									".(myGET('filterbu')=='All Business Unit'?"":"and (bu.businessunit like '%".myGET('filterbu')."%')")."
									group by a.id ".(myGET('showapproved')=='unchecked'?"Having qwer='Pending'":"")." 
									order by datetoday desc limit 12 offset ".$currentpage.";");
			while($row=mysqlm_fetch_array($query)){
				echo '<tr trhoverable>
						<td>'.$row[0].'</td>
						<td displaynone>'.$row[5].'</td>
						<td displaynone>'.$row[4].'</td>
						<td displaynone>'.$row[1].'</td>
						<td>'.$row['co'].'</td>
						<td>'.($row['bu2']==""?$row['bu']:$bus[$row['bu2']]).'</td>
						<td>'.$row['fullname'].'</td>
						<td centertext>'.$row['therequesttype'].'</td>
						<!--<td centertext>'.$row['thetotalrequest'].'</td>-->
						<td centertext class="tdrequestsstatus" '.$row['qwer'].'>'.$row['qwer'].($row['remarks']!=''?" <i class='fa fa-commenting' title='remarked'/>":"").'</td>
						<td centertext>
							<div iconbtn onclick="iconrequestopenclicked(\''.$row['requestgroup'].'\',\''.$row['buid'].'\')" title="open"><i class="fa fa-list"/></div>
						</td>
					</tr>';
			}
		echo '</table>';
		if(mysqlm_rowcount($query)==0){
			echo '<div mygroup col12 style="background-color:white" centertext paddingedall bbox>No results.</div>';
		}
		breakhere($con);
	}
	// elseif(isset($_GET['requestopen'])){//open request to view approve status
		// include 'db/dbconnect.php';
		// $requestgroup = mySTRget('requestgroup');
		// $buid=myGET('buid');
		// $query2 = mysqlm_query("select thetype from requests where requestgroup = ".$requestgroup);
		// $row2 = mysqlm_fetch_array($query2);
		// $query = mysqlm_query("select LPAD(requestnumber,4,0) as number,`date` as date,
							// if(IFNULL(a.iadstatus,'Pending')='Pending','Pending','Approved') as iadstatus,a.id as aid,
							// if(IFNULL(executed,'Pending')='Pending','Pending','Approved') as executed,
							// if(IFNULL(buheadid,'Pending')='Pending','Pending','Approved') as buhead,
							// if(IFNULL(`status`,'Pending')='Pending','Pending','Approved') as groupivstatus,
							// b.tortype as tortype
							// from requests a,tortypes b 
							// where a.tortype = b.id and a.requestgroup = ".mySTRget('requestgroup')." 
							// order by a.requestnumber");
		// echo '<table mytable fullwidth>
				// <tr>
					// <th col1>Number</th>
					// <th>Date</th>
					// <th>Type</th>';
					// $butasks = getbutasksrfs($buid,1);
					// foreach($butasks as $butask){
						// echo '<th centertext>'.$butask[0].'</th>';
					// }
					// // '<th centertext>Approved</th>
					// // <th centertext col2>Verified</th>
					// // <th centertext col1>Adjusted/Reprinted</th>';
				// echo'<th col1 centertext>Action</th>
				// </tr>';
		// while($row=mysqlm_fetch_array($query)){
			// $approvedna=checkstatus(array($row['iadstatus'],$row['executed'],$row['buhead']));
			// echo '<tr trhoverable>
					// <td colorred fontbold centertext>'.$row['number'].'</td>
					// <td>'.$row['date'].'</td>
					// <td>'.$row['tortype'].'</td>';
					// foreach($butasks as $butask){
						// echo'<td class="tdrequestsstatus" '.$row[$butask[1]].' centertext>'.$row[$butask[1]].'</td>';
					// }
					// // '<td class="tdrequestsstatus" centertext '.$row['buhead'].'>'.$row['buhead'].'</td>
					// // <td class="tdrequestsstatus" centertext '.$row['iadstatus'].'>'.$row['iadstatus'].'</td>
					// // <td class="tdrequestsstatus" centertext '.$row['executed'].'>'.$row['executed'].'</td>'
				// echo'<td col1 centertext>
						// <div iconbtn coli title="view details" onclick="iconrequestmodeviewclicked2('.strsing($row['aid']).','.strsing($approvedna).')"><i class="fa fa-list"/></div>
						// <!--'.($row[4]=="Approved"?'<div iconbtn coli marginedleft title="print"><i class="fa fa-print"/></div>':"").'-->
					// </td>
				// </tr>';
		// }
		// echo '</table>';
		// breakhere($con);
	// }
	elseif(isset($_GET['requestmodeview'])){//view request rfs
		include 'db/dbconnect.php';
		$query = mysqlm_query("select id,userid,date,requestmode,typeofrequest,requesttypevalue,purpose,requestnumber,iadstatus from requests where id =".mySTRget('requestid'));
		$row = mysqlm_fetch_array($query);
		$query3 = mysqlm_query("select companyname,businessunit,address,contactnumber,concat(u.firstname,' ',u.lastname) as fullname from tblcompany c,users u,tblbusinessunit bu where u.id=".$row['userid']." and u.businessunitid = bu.id and c.id = bu.companyid");
		$row2 = mysqlm_fetch_array($query3);
		$requesttypevaluelabel="";
		echo '<div mygroup col6 nopadding displaytablecell>';
			echo '<div mygroup noborder col12>
					<div col5 coli colctrl marginedtop>Company Name</div><input class="txtcompanyname" readonly type="text" value="'.getcompanyname($row['id']).'" col7/>
					<div col5 coli colctrl marginedtop>Business Unit</div><input class="txtbusinessunit" readonly type="text" value="'.getbuname($row['id']).'" col7/>
					<div col5 coli colctrl marginedtop>Contact Number</div><input class="txtcontactno" readonly type="text" value="'.getcontactnumber($row['id']).'" col7/>
					<div col5 coli colctrl marginedtop>Date</div><input class="txtdate" readonly type="text" value="'.$row['date'].'"  col7/>
					<div col5 coli colctrl marginedtop>Address</div><input class="txtaddress" readonly type="text" value="'.getaddress($row['id']).'"  col7/>
					<div col5 coli colctrl marginedtop>Requested by</div><input class="txtaddress" readonly type="text" value="'.$row2['fullname'].'" col7/>';
			echo '</div>';
			echo '<div mygroup noborder marginedtop hasbordertop col12>';
				echo '<div col6 coli>
						<div colctrl fontbold>Request mode</div>';
						$query2 = mysqlm_query("select themode,id from requestmode where id='".$row['requestmode']."'");
						$row2 = mysqlm_fetch_array($query2);
						echo '<div coli col12 marginedbottom><div class="mycheckbox chkrequestmode" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
						$requesttypevaluelabel = '(<span color1>'.$row2[0].'</span>) ';
				echo '</div>';
				echo '<div col6 coli>
						<div colctrl fontbold>Request type</div>';
						$query2 = mysqlm_query("select requesttype,id from typeofrequest where id='".$row['typeofrequest']."'");
						$row2 = mysqlm_fetch_array($query2);
						echo '<div coli col12 marginedbottom><div class="mycheckbox chkrequesttype" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
						$requesttypevaluelabel = $requesttypevaluelabel.$row2[0];
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div mygroup col6 nopadding displaytablecell noborderleft>';
			echo '<div mygroup noborder col12>
					<div fontbold>'.$requesttypevaluelabel.'</div>
					<textarea resizenone readonly bbox style="height:150px" col12>'.str_replace("="," = ",$row['requesttypevalue']).'</textarea>';
			echo '</div>';
			echo '<div mygroup noborder marginedtop col12>
					<div fontbold>Purpose</div>
					<textarea resizenone readonly bbox style="height:100px" col12>'.$row['purpose'].'</textarea>';
			echo '</div>';
			echo '<div mygroup marginedtop noborder hasbordertop col12>
					<div buttongroup col12>';
						//'<button col6 onclick="inrequestmodeapproveclicked('.$row['id'].')">Approve</button>';
						echo ($row['iadstatus']==""?'<button col4 onclick="inrequestmodeapproveclicked('.$row['id'].')"> <i class="fa fa-check"/> Approve</button>':
						  '<button col4 onclick="requestmodedisapprovedclicked('.$row['id'].')"> <i class="fa fa-times"/> Disapprove</button>');
					echo '<button col4 onclick="showapproveclicked('.myGET('requestid').')"><i class="fa fa-eye"/> Show Approve</button>
						<button col4 onclick="printrequestclicked(\'RFS\','.strsing(myGET('requestid')).','.strsing($row['requestnumber']).')"> <i class="fa fa-print"/> Print</button>
					</div>
				</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['requestmodeview2'])){//view TOR form
		include 'db/dbconnect.php';
		$requestid = myGET('requestid');
		$query2 = mysqlm_query("select date,b.tortype as tortype,purpose,requesttypevalue,LPAD(requestnumber,4,0) as requestnumber,userid,remarks,iadstatus from requests a,tortypes b where a.id = $requestid and a.tortype=b.id");
		$row2 = mysqlm_fetch_array($query2);
		$query3 = mysqlm_query("select companyname,businessunit,address,contactnumber,concat(u.firstname,' ',u.lastname) as fullname from tblcompany c,users u,tblbusinessunit bu where u.id=".$row2['userid']." and u.businessunitid = bu.id and c.id = bu.companyid");
		$row4 = mysqlm_fetch_array($query3);
		echo '<div class="mygroup" col12>
				<div col2 coli colctrl>Company Name</div><input class="txtcompanyname" readonly value="'.getcompanyname($requestid).'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Business Unit</div><input class="txtbusinessunit" readonly value="'.getbuname($requestid).'" type="text" col3/>
				<div col2 coli colctrl>Contact Number</div><input class="txtcontactno" readonly value="'.getcontactnumber($requestid).'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Date</div><input class="txtdate" value="'.$row2['date'].'" readonly type="text" col3/>
				<div col2 coli colctrl>Address</div><input class="txtaddress" readonly value="'.getaddress($requestid).'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Requested by</div><input class="requestedby" readonly type="text" value="'.$row4['fullname'].'" col3/>
			</div>';
		echo '<div displaytable col12>';
			echo '<div class="mygroup" nobordertop col3 displaytablecell>
					<div fontbold marginedbottom>Type of request</div>';
					echo '<div coli col12 marginedbottom><div class="mycheckbox chktortypes" value="checked" coli></div><span marginedleft>'.$row2['tortype'].'</span></div>';
			echo '</div>';
			echo '<div class="mygroup" nobordertop noborderleft col8 displaytablecell>
					<div fontbold marginedbottom>Purpose</div>
					<input class="txtpurpose" readonly type="text" value="'.$row2['purpose'].'" col12 />';
			echo '</div>';
		echo '</div>';
		echo '<div col12 displaytable>';
			echo '<div class="mygroup" nobordertop col6 displaytablecell>
					<div fontbold marginedbottom>Details</div>
					<textarea class="txtdetails" readonly resizenone bbox style="height:100px" col12>'.$row2['requesttypevalue'].'</textarea>';
			echo '</div>';
			echo '<div class="mygroup" nobordertop noborderleft col6 displaytablecell>
					<div fontbold marginedbottom>Remarks</div>
					<textarea class="txtremarks" resizenone readonly bbox style="height:100px" col12>'.$row2['remarks'].'</textarea>';
			echo '</div>';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>';
			echo '<div buttongroup floatright col5>';
				echo ($row2['iadstatus']==""?'<button col4 onclick="inrequestmodeapproveclicked('.$requestid.')"> <i class="fa fa-check"/> Approve</button>':
						'<button col4 onclick="requestmodedisapprovedclicked('.$requestid.')"> <i class="fa fa-times"/> Disapprove</button>');
				echo '<button col5 onclick="showapproveclicked('.$requestid.')"> <i class="fa fa-eye"/> Show Approve</button>
					<button col3 onclick="printrequestclicked(\'TOR\',\''.$requestid.'\',\''.$row2['requestnumber'].'\')"> <i class="fa fa-print"/> Print</button>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['requestapprove'])){//approve request
		include 'db/dbconnect.php';
		$query = mysqlm_query("update requests set `iadstatus` = '".getuserid()."' where id='".myGET('requestid')."'");
		breakhere($con);
	}
	elseif(isset($_GET['requestdisapprove'])){//disapprove request
		include 'db/dbconnect.php';
		$query = mysqlm_query("update requests set `iadstatus` = null where id='".myGET('requestid')."'");
		breakhere($con);
	}
	elseif(isset($_GET['showapprovetor'])){
		include 'db/dbconnect.php';
		$users = initusers();
		$requestid = myGET('requestid');
		$query = mysqlm_fetch_array(mysqlm_query("select executed,buheadid,iadstatus,approvedid,thetype from requests where id=".$requestid));
		$pending = '<span colorred>Pending</span>';
		$buhead = (empty($query['buheadid'])?$pending:$users[$query['buheadid']]);
		$gp4status = (empty($query['approvedid'])?$pending:$users[$query['approvedid']]);
		$iadstatus = (empty($query['iadstatus'])?$pending:$users[$query['iadstatus']]);
		$programmermis = (empty($query['executed'])?$pending:$users[$query['executed']]);
		echo '<div mygroup fullwidth style="height:200px">';
			echo '<table mytable fullwidth>
					<tr><th></th><th>Approved By</th></tr>
					<tr>
						<td>BU Head Manager</td>
						<td>'.$buhead.'</td>
					</tr>
					<tr>
						'.($query['thetype']==2?'<td>IAD</td>
						<td>'.$iadstatus.'</td>
						':'<td>Group IV Manager</td>
						<td>'.$gp4status.'</td>
						').'
					</tr>
					<tr>
						<td>Executed</td>
						<td>'.$programmermis.'</td>
					</tr>
				</table>';
		echo '</div>';
		echo '<div mygroup nobordertop fullwidth>
				<button floatright col2 onclick="showapprovecloseclicked()">Close</button>
			</div>';
		breakhere($con);
	}
?>
<br/>
<?php include 'db/dbconnect.php'  ?>
<div class="myframe">
	<h3 fontbold><span color1>Requests</span></h3>
	<div coli col12>
		<div mytoolsgroup col6 displaytablecell nopadding>
			<div mytoolsgroup displaytablecell noborder col3>
				<div coli marginedright fontbold>Filter Date</div>
				<button class="monthselector filterrequestmonth" datevalue="">This month</button>
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
		<div mytoolsgroup col6 displaytablecell noborderleft>
			<div coli col3 marginedleft><div class="mycheckbox chkshowapproved" value="checked" coli></div><div coli marginedleft> Show approved</div></div>
			<?php
				$query = mysqlm_query("select rfs,tor from taskrole where userid=".getuserid()." and usertypeid=".getusertype());
				$row = mysqlm_fetch_array($query);
				echo '<div coli col2 marginedleft '.($row['rfs']=='unchecked'?'displayhidden':'').'><div class="mycheckbox chkshowrfs" value="'.$row['rfs'].'" coli></div><div coli marginedleft> RFS</div></div>';
				echo '<div coli col2 marginedleft '.($row['tor']=='unchecked'?'displayhidden':'').'><div class="mycheckbox chkshowtor" value="'.$row['tor'].'" coli></div><div coli marginedleft> TOR</div></div>';

			?>
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
	$(".filternumber").on('keyup',function(evt){
		if(evt.keyCode==13){
			loadrequests();
		}
	});
	loadrequests();
	$(".chkshowapproved").mycheckbox(function(){
		loadrequests();
	});
	//$(".mycheckbox").mycheckbox();
	$(".chkshowrfs").mycheckbox(function(){
		loadrequests();
	});
	$(".chkshowtor").mycheckbox(function(){
		loadrequests();
	});
	var mymodalgroup = new MyModal();
	function loadrequests(){
		var reqdate = $(".filterrequestmonth").datevalue();
		$.get("iad.php?loadrequesttable&date="+reqdate+"&showapproved="+$(".chkshowapproved").attr("value")+
							"&filterbu="+filterbu+
							"&filternumber="+$(".filternumber").val()+
							"&currentpage="+currentpage+
							"&showrfs="+$(".chkshowrfs").mycheckboxvalue()+
							"&showtor="+$(".chkshowtor").mycheckboxvalue(),function(result){
			$(".requeststable").html(result);
			refreshthis($(".bodyelements"));
		});
	}
	function iconrequestopenclicked(requestgroup,buid){
		selectedbuid = buid;
		mymodalgroup.showcustom("Requests","70%");
		mymodalgroup.settag(requestgroup);
		$.get("requests.php?requestopen&requestgroup="+requestgroup+"&buid="+selectedbuid,function(result){
			mymodalgroup.body(result);
		});
	}
	var modalview = new MyModal();
	function iconrequestmodeviewclicked(requestid){
		modalview.showcustom("Requests","60%");
		$.get("iad.php?requestmodeview&requestid="+requestid,function(result){
			modalview.body(result);
			$(".mycheckbox").mycheckbox();
		})
	}
	function iconrequestmodeviewclicked2(requestid,checkstatus){
		modalview.showcustom("Requests","60%");
		$.get("iad.php?requestmodeview2&requestid="+requestid,function(result){
			modalview.settag(checkstatus);
			modalview.body(result);
			$(".mycheckbox").mycheckbox();
		});
	}
	function inrequestmodeapproveclicked(id){
		modalview.showloading();
		modalview.close();
		$.get("iad.php?requestapprove&requestid="+id,function(result){
			mymodalgroup.showloading();
			$.get("requests.php?requestopen&requestgroup="+mymodalgroup.gettag()+"&buid="+selectedbuid,function(result){
				mymodalgroup.body(result);
				loadrequests();
			});
			showMyAlert("Request Approved!","");
		});
	}
	function requestmodedisapprovedclicked(id){
		showMyConfirm("Disapprove request?",function(){
			//mymodal.showloading();
			modalview.showloading();
			$.get("iad.php?requestdisapprove&requestid="+id,function(result){
				//mymodal.close();
				mymodalgroup.showloading();
				$.get("requests.php?requestopen&requestgroup="+mymodalgroup.gettag()+"&buid="+selectedbuid,function(result2){
					mymodalgroup.body(result2);
					modalview.close();
					loadrequests();
				});
				showMyAlert("Request Disapproved!","");
			});	
		},function(){});
		
	}
	function printrequestclicked(formtype,requestid,requestnumber){
		if(modalview.gettag()!='tananApproved'){
			showMyAlertError("Cannot print, Form Request still pending.",'');
			return;
		}
		showprint(formtype,requestid,requestnumber);
		
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
</script>