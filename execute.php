<?php
	if(isset($_GET['loadtable'])){//load main table
		include 'db/dbconnect.php';
		$mysqlm = new Mysqlm();
		$mysqlm->connect();
		$currentpage = (myGET('currentpage') * 12);
		$bus = initbu();
		echo '<table mytable fullwidth>
				<tr>
					<th style="width:9%">Transaction Date</th>
					<th style="width:5%" centertext>Request Type</th>
					<th style="width:5%" centertext>Number</th>
					<th displaynone>Request Mode</th>
					<th displaynone>Type of Request</th>
					<th style="width:5%">Company Name</th>
					<th style="width:13%">Business Unit</th>
					<th col1>Requested By</th>
					<!--<th col1>Total Requests</th>-->
					<th style="width:5%" col1 centertext>Status</th>
					<th style="width:2%" centertext>Action</th>
				</tr>';
			$executerole = mysqlm_query("select buid from executerole where userid=".getuserid());
			$executeids = mysqlm_fetch_array(mysqlm_query("select GROUP_CONCAT(buid SEPARATOR ',') from executerole where userid=".getuserid()))[0];
			$exrolecount = mysqlm_rowcount($executerole);
			$requestscount=mysqlm_fetch_array(mysqlm_query("select count(id) from requests"))[0];
			$offset = $requestscount - ($currentpage + 20);
			//echo $currentpage;
			
			$butaskroles="'".initbutaskroles()."'";
			//code for requesttyperole
			$requesttyperolerfs = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='RFS' and usertypeid=3");
			$requesttyperolerfsids = mysqlm_fetch_array($requesttyperolerfs)[0];
			$requesttyperoletor = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='TOR' and usertypeid=3");
			$requesttyperoletorids = mysqlm_fetch_array($requesttyperoletor)[0];
			//end of requesttyperole
			
			
			//showquery();
			//var_dump($butaskroles);
			$query = mysqlm_query("select
								DATE_FORMAT(datetoday,'<span colorgreen>%a &bull;</span>  <span color1>%h:%i %p &bull;</span> %b %d, %Y'),
								'',com.companyname as co,
								if(if((count(ifnull(`status`,'a'))-count(`status`='Approved'))>0,'Pending','Approved')='Approved',
									if((count(ifnull(`executed`,'a'))-count(`executed`='Approved'))>0,'Pending',
										if((count(ifnull(a.buheadid,'a'))-count(a.buheadid='Approved'))>0,'Pending','Approved')
									),'Pending'
								) as rfsstatus,
								'',
								LPAD(requestnumber,4,0),
								'' as thetotalrequest,
								requestgroup,
								bu.businessunit as bu,
								UPPER(concat(firstname,' ',lastname)),
								if(a.thetype=1,'RFS','TOR') as therequesttype,
								if(if((count(ifnull(`iadstatus`,'a'))-count(`iadstatus`='Approved'))>0,'Pending','Approved')='Approved',
									if((count(ifnull(`executed`,'a'))-count(`executed`='Approved'))>0,'Pending',
										if((count(ifnull(a.buheadid,'a'))-count(a.buheadid='Approved'))>0,'Pending','Approved')
									),'Pending'
								) as torstatus,a.thetype as thetype,
								if(ifnull(a.softsysstatus,'Pending')='Pending','Pending',
									if(ifnull(a.executed,'Pending')='Pending','Pending','Approved')
								) as softsysstatus,
								a.typeofrequest as typeofrequest, a.businessunit as bu2,
								bu.id as buid,
								if(ifnull(a.executed,'asdf')='asdf','Pending','Approved') as executestatus,
								a.remarks as remarks
								from requests a,users d,tblcompany com,tblbusinessunit bu
								where ((a.thetype=1 or
									a.thetype=2)".
									($exrolecount>0?"and bu.id in (".$executeids.")":"").
								")
								and (
										".(strlen($requesttyperolerfsids)>0?"(a.typeofrequest in (".$requesttyperolerfsids."))":"false")."
										or ".(strlen($requesttyperoletorids)>0?"  (a.tortype in (".$requesttyperoletorids."))":"false").
										((strlen($requesttyperolerfsids)==0 and strlen($requesttyperoletorids)==0)?" or true":"")."
										)
								and a.userid = d.id and d.businessunitid=bu.id and com.id=bu.companyid 
								and (getstatuspending(bu.id,3,a.executed,$butaskroles,a.thetype) 
									or getstatuspending(bu.id,5,a.iadstatus,$butaskroles,a.thetype) 
									or getstatuspending(bu.id,2,a.buheadid,$butaskroles,a.thetype) 
									or getstatuspending(bu.id,4,a.status,$butaskroles,a.thetype)
									)
								".//"and (a.idcount > ".$offset." and a.idcount < ".($offset+20).")".
								"and (".(myGET('filternumber')==""?"(DATE_FORMAT(a.datetoday,'%m/%Y')=DATE_FORMAT(STR_TO_DATE(".mySTRget('date').",'%d/%m/%Y'),'%m/%Y') or ".mySTRget('date')."=\"all\")":
									"a.requestnumber = '".ltrim(myGET('filternumber'),'0')."'").")
								".(myGET('filterbu')=='All Business Unit'?"":"and (bu.businessunit like '%".myGET('filterbu')."%')")."
								and (".(myGET('showrfs')=="unchecked"?"thetype!=1":"true")." and
										".(myGET('showtor')=="unchecked"?"thetype!=2":"true").")
								group by a.id ".(myGET('showapproved')=='unchecked'?"Having if(typeofrequest=7,softsysstatus='Pending',if(therequesttype='RFS',rfsstatus='Pending',torstatus='Pending')) ":"")."
								desc limit 12 offset ".$currentpage.";");
			$thestatus;
			while($row=mysqlm_fetch_array($query)){
				($row['thetype']==1?($row['typeofrequest']!=7?$thestatus=$row['rfsstatus']:$thestatus=$row['softsysstatus']):$thestatus=$row['torstatus']);
				echo '<tr trhoverable>
						<td>'.$row[0].'</td>
						<td centertext>'.$row['therequesttype'].'</td>
						<td fontbold colorred centertext>'.$row[5].'</td>
						<td displaynone>'.$row[4].'</td>
						<td displaynone>'.$row[1].'</td>
						<td>'.$row['co'].'</td>
						<td>'.($row['bu2']==""?$row['bu']:$bus[$row['bu2']]).'</td>
						<td>'.$row[9].'</td>
						
						<!--<td centertext>'.$row['thetotalrequest'].'</td>-->
						<td centertext class="tdrequestsstatus" '.$row['executestatus'].'>'.$row['executestatus'].($row['remarks']!=''?" <i class='fa fa-commenting' title='remarked'/>":"").'</td>
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
	// elseif(isset($_GET['openrequest'])){//open table to view approve status
		// include 'db/dbconnect.php';
		// $requestgroup = mySTRget('requestgroup');
		// $query2 = mysqlm_query("select thetype,typeofrequest from requests where requestgroup = ".$requestgroup);
		// $row2 = mysqlm_fetch_array($query2);
		// $buid = myGET('buid');
		// if($row2['thetype']==1){//rfs
			// $query = mysqlm_query("select LPAD(requestnumber,4,0),`date`,themode,requesttype,IFNULL(`status`,'Pending') as groupivstatus,a.id,
								// if(IFNULL(`executed`,'Pending')='Pending','Pending','Approved') as executed,
								// if(IFNULL(a.buheadid,'Pending')='Pending','Pending','Approved') as buhead,
								// if(IFNULL(a.iadstatus,'Pending')='Pending','Pending','Approved') as iadstatus,
								// if(IFNULL(a.softsysstatus,'Pending')='Pending','Pending','Approved') as softsysstatus,thetype,
								// a.typeofrequest as typeofrequest
								// from requests a,typeofrequest b,requestmode c 
								// where a.typeofrequest = b.id and a.requestmode = c.id and a.requestgroup = ".mySTRget('requestgroup')." 
								// order by a.requestnumber");
			// $butasks = getbutasksrfs($buid,1);			
			// if($row2['typeofrequest']!=7){
				// echo '<table mytable fullwidth>
					// <tr>
						// <th col1>Number</th>
						// <th>Date</th>
						// <th>Mode</th>
						// <th>Type</th>';
						// foreach($butasks as $butask){
							// echo '<th centertext>'.$butask[0].'</th>';
						// }
						// // '<th centertext>Approved</th>
						// // <th centertext>Reviewed</th>
						// // <th centertext col1>Implemented</th>';
					// echo'<th col1 centertext>Action</th>
					// </tr>
				// ';
				// while($row=mysqlm_fetch_array($query)){
					// $approvedna=checkstatus(array($row['groupivstatus'],$row['executed'],$row['buhead']));
					// echo '<tr trhoverable>
							// <td fontbold colorred centertext>'.$row[0].'</td>
							// <td>'.$row[1].'</td>
							// <td>'.$row[2].'</td>
							// <td>'.$row[3].'</td>';
							// foreach($butasks as $butask){
								// echo'<td class="tdrequestsstatus" '.$row[$butask[1]].' centertext>'.$row[$butask[1]].'</td>';
							// }
							// // '<td class="tdrequestsstatus" centertext '.$row['buhead'].'>'.$row['buhead'].'</td>
							// // <td class="tdrequestsstatus" centertext '.$row[4].'>'.$row[4].'</td>
							// // <td class="tdrequestsstatus" centertext '.$row[6].'>'.$row[6].'</td>'
						// echo'<td col1 centertext>
								// <div iconbtn coli title="view details" onclick="iconrequestmodeviewclicked('.strsing($row[5]).','.strsing($row['typeofrequest']).','.strsing($approvedna).')"><i class="fa fa-list"/></div>
							// </td>
						// </tr>';
				// }
				// echo '</table>';
			// }
			// else{
				// echo '<table mytable fullwidth>
					// <tr>
						// <th col1>Number</th>
						// <th>Date</th>
						// <th>Mode</th>
						// <th>Type</th>';
						// foreach($butasks as $butask){
							// echo '<th centertext>'.$butask[0].'</th>';
						// }
						// // '<th centertext col3>Corporate Audit Manager and Compliance Officer</th>
						// // <th centertext col1>Executed</th>';
						
					// echo'<th col1 centertext>Action</th>
					// </tr>
				// ';
				// while($row=mysqlm_fetch_array($query)){
					// $approvedna=checkstatus(array($row['groupivstatus'],$row['executed'],$row['buhead']));
					// echo '<tr trhoverable>
							// <td fontbold colorred centertext>'.$row[0].'</td>
							// <td>'.$row[1].'</td>
							// <td>'.$row[2].'</td>
							// <td>'.$row[3].'</td>
							// <td class="tdrequestsstatus" centertext '.$row['softsysstatus'].'>'.$row['softsysstatus'].'</td>
							// <td class="tdrequestsstatus" centertext '.$row[6].'>'.$row[6].'</td>
							// <td col1 centertext>
								// <div iconbtn coli title="view details" onclick="iconrequestmodeviewclicked('.strsing($row[5]).','.strsing($row['typeofrequest']).','.strsing($approvedna).')"><i class="fa fa-list"/></div>
							// </td>
						// </tr>';
				// }
				// echo '</table>';
			// }
		// }
		// else{//tor
			// $query = mysqlm_query("select LPAD(requestnumber,4,0) as number,`date` as date,
								// if(IFNULL(iadstatus,'Pending')='Pending','Pending','Approved') as iadstatus,a.id as aid,
								// if(IFNULL(executed,'Pending')='Pending','Pending','Approved') as executed,
								// if(IFNULL(buheadid,'Pending')='Pending','Pending','Approved') as buhead,
								// b.tortype as tortype
								// from requests a,tortypes b 
								// where a.tortype = b.id and a.requestgroup = ".mySTRget('requestgroup')." 
								// order by a.requestnumber");
			// echo '<table mytable fullwidth>
					// <tr><th col1>Number</th>
					// <th>Date</th>
					// <th>Type</th>
					// <th centertext>Approved</th>
					// <th centertext>Verified</th>
					// <th centertext col1>Adjusted/Reprinted</th>
					// <th col1 centertext>Action</th></tr>
			// ';
			// while($row=mysqlm_fetch_array($query)){
				// $approvedna=checkstatus(array($row['iadstatus'],$row['executed'],$row['buhead']));
				// echo '<tr trhoverable>
						// <td colorred fontbold centertext>'.$row['number'].'</td>
						// <td>'.$row['date'].'</td>
						// <td>'.$row['tortype'].'</td>
						// <td class="tdrequestsstatus" centertext '.$row['buhead'].'>'.$row['buhead'].'</td>
						// <td class="tdrequestsstatus" centertext '.$row['iadstatus'].'>'.$row['iadstatus'].'</td>
						// <td class="tdrequestsstatus" centertext '.$row['executed'].'>'.$row['executed'].'</td>
						// <td col1 centertext>
							// <div iconbtn coli title="view details" onclick="iconrequestmodeviewclicked2('.strsing($row['aid']).','.strsing($approvedna).')"><i class="fa fa-list"/></div>
							// <!--'.($row[4]=="Approved"?'<div iconbtn coli marginedleft title="print"><i class="fa fa-print"/></div>':"").'-->
						// </td>
					// </tr>';
			// }
			// echo '</table>';
		// }
		// breakhere($con);
	// }
	elseif(isset($_GET['requestmodeview2'])){//view TOR form
		include 'db/dbconnect.php';
		$requestid = myGET('requestid');
		$query2 = mysqlm_query("select date,b.tortype as tortype,purpose,requesttypevalue,LPAD(requestnumber,4,0) as requestnumber,userid,a.remarks,a.executed as executed,a.businessunit as bu2 from requests a,tortypes b where a.id = $requestid and a.tortype=b.id");
		$row2 = mysqlm_fetch_array($query2);
		$query3 = mysqlm_query("select companyname,businessunit,address,contactnumber,concat(u.firstname,' ',u.lastname) as fullname from tblcompany c,users u,tblbusinessunit bu where u.id=".$row2['userid']." and ".($row2['bu2']==""?"u.businessunitid":$row2['bu2'])." = bu.id and c.id = bu.companyid");
		$row4 = mysqlm_fetch_array($query3);
		echo '<div class="mygroup" col12>
				<div col2 coli colctrl>Company Name</div><input class="txtcompanyname" readonly value="'.$row4['companyname'].'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Business Unit</div><input class="txtbusinessunit" readonly value="'.$row4['businessunit'].'" type="text" col3/>
				<div col2 coli colctrl>Contact Number</div><input class="txtcontactno" readonly value="'.$row4['contactnumber'].'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Date</div><input class="txtdate" value="'.$row2['date'].'" readonly type="text" col3/>
				<div col2 coli colctrl>Address</div><input class="txtaddress" readonly value="'.$row4['address'].'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Requested by</div><input class="txtaddress" readonly type="text" value="'.$row4['fullname'].'" col3/>
			</div>';
		echo '<div displaytable col12>';
			echo '<div class="mygroup" nobordertop col3 displaytablecell>
					<div fontbold marginedbottom >Type of request</div>
					<div col12 marginedbottom><div class="mycheckbox chktortypes" value="checked" coli></div><span marginedleft>'.$row2['tortype'].'</span></div>';
			echo '</div>';
			echo '<div class="mygroup" nobordertop noborderleft col8 displaytablecell>
					<div fontbold marginedbottom>Purpose</div>
					<input class="txtpurpose" readonly type="text" value="'.$row2['purpose'].'" col12 />
					';
			echo '</div>';
		echo '</div>';
		echo '<div col12 displaytable>';
			echo '<div class="mygroup" nobordertop col6 displaytablecell>
					<div fontbold marginedbottom>Details</div>
					<textarea class="txtdetails" readonly resizenone bbox style="height:130px" col12>'.$row2['requesttypevalue'].'</textarea>';
			echo '</div>';
			echo '<div class="mygroup" nobordertop noborderleft col6 displaytablecell>
					<div fontbold marginedbottom>Remarks</div>
					<textarea class="txtremarks" resizenone bbox style="height:80px" col12>'.$row2['remarks'].'</textarea>
					<button floatright col3 onclick="btnsaveremarksclicked(\''.$requestid.'\')"> <i class="fa fa-save"/> Save</button>';
			echo '</div>';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>';
			echo '<div buttongroup floatright col5>';
				if($row2['executed']==""){
					echo '<button col4 onclick="inrequestmodeapproveclicked('.$requestid.')"> <i class="fa fa-check"/> Approve</button>';
				}
				else{
					echo '<button col4 onclick="inrequestmodedisapproveclicked('.$requestid.')"> <i class="fa fa-times"/> Disapprove</button>';
				}
				echo '
				<button col5 onclick="showapproveclicked('.$requestid.')"> <i class="fa fa-eye"/> Show Approve</button>
				<button col3 onclick="printrequestclicked(\'TOR\',\''.$requestid.'\',\''.$row2['requestnumber'].'\')"> <i class="fa fa-print"/> Print</button>';
				//echo '<button col6>Disapprove</button>';
				
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['requestmodeview'])){//view RFS form
		include 'db/dbconnect.php';
		$query = mysqlm_query("select id,date,requestmode,typeofrequest,requesttypevalue,purpose,LPAD(requestnumber,4,0) as requestnumber,requestnumber as requestnumber2,userid,executed,businessunit as bu2,remarks from requests where id =".mySTRget('requestid'));
		$row = mysqlm_fetch_array($query);
		$query3 = mysqlm_query("select companyname,businessunit,address,contactnumber,concat(u.firstname,' ',u.lastname) as fullname from tblcompany c,users u,tblbusinessunit bu where u.id=".$row['userid']." and ".($row['bu2']==""?"u.businessunitid":$row['bu2'])." = bu.id and c.id = bu.companyid");
		$row2 = mysqlm_fetch_array($query3);
		$requesttypevaluelabel="";
		echo '<div mygroup col6 nopadding displaytablecell>';
			echo '<div mygroup noborder col12>
					<div col5 coli colctrl marginedtop>Company Name</div><input class="txtcompanyname" readonly type="text" value="'.$row2['companyname'].'" col7/>
					<div col5 coli colctrl marginedtop>Business Unit</div><input class="txtbusinessunit" readonly type="text" value="'.$row2['businessunit'].'" col7/>
					<div col5 coli colctrl marginedtop>Contact Number</div><input class="txtcontactno" readonly type="text" value="'.$row2['contactnumber'].'" col7/>
					<div col5 coli colctrl marginedtop>Date</div><input class="txtdate" readonly type="text" value="'.$row['date'].'"  col7/>
					<div col5 coli colctrl marginedtop>Address</div><input class="txtaddress" readonly type="text" value="'.$row2['address'].'"  col7/>
					<div col5 coli colctrl marginedtop>Requested by</div><input class="txtaddress" readonly type="text" value="'.$row2['fullname'].'" col7/>
					<div col5 coli colctrl marginedtop>Request Number</div><input class="txtaddress" colorred readonly type="text" value="'.$row['requestnumber'].'" col7/>';
			echo '</div>';
			
			echo '<div mygroup noborder marginedtop col12>
						<div fontbold>Purpose</div>
						<textarea resizenone readonly bbox style="height:100px" col12>'.$row['purpose'].'</textarea>';
				echo '</div>';
			$query4 = mysqlm_fetch_array(mysqlm_query("select count(id) from attachedfiles where requestnumber = 'RFS-".$row['requestnumber2']."'"));
			echo '<div mygroup noborder hasbordertop col12 >';
				echo '<div fontbold marginedbottom>Attachment</div>
					<div colorred paddingedall col6 coli bbox fontbold font14>('.$query4[0].' files)</div>
					<div col6 coli bbox><button col8 onclick="btnviewattachmentclicked(\''.$row['requestnumber2'].'\')"> <i class="fa fa-file-text"/> View files</button></div>'; 
			echo '</div>';
		echo '</div>';
		echo '<div mygroup col6 nopadding displaytablecell noborderleft>';
			echo '<div mygroup noborder col12>';
				echo '<div col6 coli>
						<div colctrl fontbold>Request type</div>';
						$query2 = mysqlm_query("select requesttype,id from typeofrequest where id='".$row['typeofrequest']."'");
						$row2 = mysqlm_fetch_array($query2);
						echo '<div coli col12 marginedbottom><div class="mycheckbox chkrequesttype" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
						//$requesttypevaluelabel = '(<span color1>'.$row2[0].'</span>) ';
				echo '</div>';
				echo '<div col6 coli>
						<div colctrl fontbold>Request mode</div>';
						$query2 = mysqlm_query("select themode,id from requestmode where id='".$row['requestmode']."'");
						$row2 = mysqlm_fetch_array($query2);
						echo '<div coli col12 marginedbottom><div class="mycheckbox chkrequestmode" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
						//$requesttypevaluelabel = $requesttypevaluelabel.$row2[0];
				echo '</div>';
			echo '</div>';
			echo '<div mygroup noborder col12>
					<div fontbold>Details</div>
					<textarea resizenone readonly bbox style="height:140px" col12>'.str_replace("="," = ",$row['requesttypevalue']).'</textarea>';
			echo '</div>';
			echo '<div class="mygroup" noborder col12>
					<div fontbold marginedbottom>Remarks</div>
					<textarea class="txtremarks" resizenone bbox style="height:80px" col12>'.$row['remarks'].'</textarea>
					<button floatright col3 onclick="btnsaveremarksclicked(\''.myGET('requestid').'\')"> <i class="fa fa-save"/> Save</button>';
			echo '</div>';
			echo '<div mygroup marginedtop noborder hasbordertop col12>
					<div buttongroup col12>';
						if($row['executed']==""){
							echo '<button col4 onclick="inrequestmodeapproveclicked('.$row['id'].')"> <i class="fa fa-check"/> Approve</button>';
						}
						else{
							echo '<button col4 onclick="inrequestmodedisapproveclicked('.$row['id'].')"> <i class="fa fa-times"/> Disapprove</button>';
						}
						echo'
						<button col5 onclick="showapproveclicked('.$row['id'].')"> <i class="fa fa-eye"/> Show Approve</button>
						<button col3 onclick="printrequestclicked(\'RFS\','.strsing(myGET('requestid')).','.strsing($row['requestnumber']).')"> <i class="fa fa-print"/> Print</button>
						<!--<button col6>Disapprove</button>-->
					</div>
				</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['executerequest'])){//execute request
		include'db/dbconnect.php';
	//	$query = mysqlm_query("select IFNULL(`status`,'Pending') from requests where id=".myGET('requestid'));
	//	if(mysqlm_fetch_array($query)[0]=='Pending'){
	//		echo 'failed';
	//	}
	//	else{
		$requestid = myGET('requestid');
		$query = mysqlm_fetch_array(mysqlm_query("select executed from requests where id=".$requestid));
		if($query['executed']==""){
			mysqlm_query("update requests set executed=".getuserid()." where id=".$requestid);
			echo 'success';
		}
		else{
			echo 'fail';
		}
			
	//		echo 'success';
	//	}
		
		breakhere($con);
	}
	elseif(isset($_POST['saveremarks'])){//save remarks
		include 'db/dbconnect.php';
		$requestid = myPOST('requestid');
		$remarks = mySTRpost('remarks');
		mysqlm_query("update requests set remarks=".$remarks." where id=".$requestid);
		breakhere($con);
	}
	elseif(isset($_GET['disapproverequest'])){//disapprove request
		include 'db/dbconnect.php';
		$requestid = myGET('requestid');
		$query = mysqlm_fetch_array(mysqlm_query("select executed from requests where id = ".$requestid));
		$arr = initusers();
		if(getuserid()==$query['executed']){
			$query = mysqlm_query("update requests set executed=null where id=".$requestid);
			echo 'success';
		}
		else{
			echo $arr[$query['executed']];
		}
		breakhere($con);
	}
	elseif(isset($_GET['getmaxrecords'])){//get requests count
		include 'db/dbconnect.php';
		$query = mysqlm_query("select count(id) from requests");
		echo mysqlm_fetch_array($query)[0];
		breakhere($con);
	}
	elseif(isset($_GET['getnotification'])){//
		include 'db/dbconnect.php';
		$query = mysqlm_fetch_array(mysqlm_query("select count(id) from requests where isread is null"))[0];
		echo $query;
		breakhere($con);
	}
	elseif(isset($_GET['readnotifications'])){
		include 'db/dbconnect.php';
		mysqlm_query("update requests set isread=1");
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
<div class="mytab tabs1">
	<div class="mytabbody" fullwidth caption="<span class='navprogmispending'>Pending Requests</span>">
			<!--<h3 fontbold><span color1><?php //echo getusertypename()?></span></h3>-->
			<!--<h3 fontbold><span color1>Implement, Adjust/Reprint</span></h3>-->
			<div coli col12 >
				<div mytoolsgroup col6 displaytablecell nopadding>
					<div mytoolsgroup displaytablecell noborder col6>
						<div coli marginedright fontbold>Filter Date</div>
						<button class="monthselector filterrequestmonth" datevalue="">This month</button>
					</div>
					<div mytoolsgroup displaytablecell noborder hasborderleft col6>
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
					<div coli col2 marginedleft><div class="mycheckbox chkshowrfs" value="checked" coli></div><div coli marginedleft> RFS</div></div>
					<div coli col2 marginedleft><div class="mycheckbox chkshowtor" value="checked" coli></div><div coli marginedleft> TOR</div></div>
				</div>
			</div>
			<div mygroup col12 bordertopwhite>
				<div class="requeststable" >
					
				</div>
				<div fullwidth displaytable>
					<div buttongroup class="mypagination" marginedtop floatright>
						
					</div>
				</div>
			</div>
			<!--<div coli marginedright fontbold>Select Page</div>-->
		
	</div>
	<div class="mytabbody" fullwidth caption="Approved Requests">
		<!--<h3 fontbold><span color1>Implement, Adjust/Reprint</span></h3>-->
		<div coli col12>
			<div mytoolsgroup col6 displaytablecell nopadding>
				<div mytoolsgroup displaytablecell noborder col6>
					<div coli marginedright fontbold>Filter Date</div>
					<button class="monthselector2 filterrequestmonth2" datevalue="">This month</button>
				</div>
				<div mytoolsgroup displaytablecell noborder hasborderleft col6>
					<div class="selectbusinessunit2" myselect col6 placeholder="Business unit">
						<?php
							$query = mysqlm_query("select businessunit,id from tblbusinessunit where active='1' order by businessunit");
							echo '<div myselectoption value="">All Business Unit</div>';
							while($row=mysqlm_fetch_array($query)){
								echo '<div myselectoption value="'.$row['id'].'">'.$row[0].'</div>';
							}
						?>
					</div>
					<input class="filternumber2" title="Press Enter" centertext colorred type="text" col4 placeholder="Control Number"/>
				</div>
			</div>
			<div mytoolsgroup col6 displaytablecell noborderleft>
				<div coli col2 marginedleft><div class="mycheckbox chkshowrfs2" value="checked" coli></div><div coli marginedleft> RFS</div></div>
				<div coli col2 marginedleft><div class="mycheckbox chkshowtor2" value="checked" coli></div><div coli marginedleft> TOR</div></div>
			</div>
		</div>
		<div mygroup col12 bordertopwhite>
			<div class="requeststable2">
				
			</div>
			<div fullwidth displaytable>
				<div buttongroup class="mypagination2" marginedtop floatright>
					
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	//start of pending requests
	var filterbu = "";
	var currentpage=0;
	$(".maximizable").maximizable();
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
	$(".tabs1").mytab({"fragments1":function(){
						loadrequests();
					},
					"fragments2":function(){
						loadrequests2();
					}
				});
	//loadrequests();
	function loadrequests(){
		var reqdate = $(".filterrequestmonth").datevalue();
		$.get("execute.php?loadtable&date="+reqdate+"&showapproved="+$(".chkshowapproved").attr("value")+"&filterbu="+filterbu+
							"&filternumber="+$(".filternumber").val()+
							"&showrfs="+$(".chkshowrfs").mycheckboxvalue()+
							"&showtor="+$(".chkshowtor").mycheckboxvalue()+
							"&currentpage="+currentpage,function(result){
			$(".requeststable").html(result);
			refreshthis($(".bodyelements"));
		});
		$(".navprogmispending").html('Pending Requests');
		$(".navexecute").html('Execute');
		maxpendingrecords = tempmaxpendingrecords;
		currentpendingrecords = maxpendingrecords;
		progmisnotification();
		stoptitleanimation();
	}
	//end of pending requests
	//start of approves requests
	var filterbu2 = "";
	var currentpage2=0;
	$.get("execute.php?getmaxrecords",function(result){
		$(".mypagination2").mypagination(result,function(selectedpage){
			currentpage2=selectedpage;
			loadrequests2();
		});
	});
	$(".selectbusinessunit2").myselect(function(elinputval){
		filterbu2 = elinputval;
		loadrequests2();
	});
	$(".monthselector2").monthselector(function(){
		loadrequests2();
	});
	$(".chkshowapproved2").mycheckbox(function(){
		loadrequests2();
	});
	$(".chkshowrfs2").mycheckbox(function(){
		loadrequests2();
	});
	$(".chkshowtor2").mycheckbox(function(){
		loadrequests2();
	});
	$(".filternumber2").on('keyup',function(evt){
		if(evt.keyCode==13){
			loadrequests2();
		}
	});
	//loadrequests2();
	function loadrequests2(){
		var reqdate = $(".filterrequestmonth2").datevalue();
		$.get("executeapproved.php?loadtable&date="+reqdate+"&showapproved="+$(".chkshowapproved2").attr("value")+"&filterbu="+filterbu2+
							"&filternumber="+$(".filternumber2").val()+
							"&showrfs="+$(".chkshowrfs2").mycheckboxvalue()+
							"&showtor="+$(".chkshowtor2").mycheckboxvalue()+
							"&currentpage="+currentpage2,function(result){
			$(".requeststable2").html(result);
		});
	}
	//end if approved requests
	function iconrequestopenclicked(requestgroup,buid){
		selectedbuid=buid;
		mymodal.showcustom("Request","70%");
		mymodal.settag(requestgroup);
		$.get("requests.php?requestopen&requestgroup="+requestgroup+"&buid="+selectedbuid,function(result){
			mymodal.body(result);
		});
	}
	function iconrequestmodeviewclicked(requestid,checkstatus){
		mymodal2.showcustom("Execute","60%");
		//$.get((typeofrequest==7?"sysupdate":"execute") + ".php?requestmodeview&requestid="+requestid+"&noedit",function(result){
		$.get("execute.php?requestmodeview&requestid="+requestid+"&noedit",function(result){
			mymodal2.body(result);
			mymodal2.settag(checkstatus);
			$(".mycheckbox").mycheckbox();
		});
	}
	function inrequestmodeapproveclicked(requestid){
		var prevbody = mymodal2.getbody();
		mymodal2.showloading();
		$.get("execute.php?executerequest&requestid="+requestid,function(result){
			if(result=="success"){
				loadrequests();
				loadrequests2();
				//mymodal.close();
				$.get("requests.php?requestopen&requestgroup="+mymodal.gettag()+"&buid="+selectedbuid,function(result2){
					mymodal.body(result2);
				});
				mymodal2.close();
				showMyAlert("Request Approved!","");
			}
			else{
				showMyAlertError("Request already approved!","");
				mymodal2.body(prevbody);
			}
			
		});
	}
	function inrequestmodedisapproveclicked(requestid){
		//mymodal2.showloading();
		//alert("asdf");
		showMyConfirm("Disapprove request?",function(){
			mymodal2.settag(mymodal2.getbody());
			mymodal2.showloading();
			
			$.get("execute.php?disapproverequest&requestid="+requestid,function(result){
				if(result=="success"){
					mymodal.showloading();
					$.get("requests.php?requestopen&requestgroup="+mymodal.gettag()+"&buid="+selectedbuid,function(result2){
						mymodal.body(result2);
					});
					mymodal2.close();
					showMyAlert("Request Disapproved!","");
					loadrequests();
					loadrequest2();
				}
				else{
					showMyAlertError("Can only be disapprove by "+result+".","");
					mymodal2.body(mymodal2.gettag());
				}
			//	mymodal2.close();
				//showMyAlert("Request Approved!","");
			});	
		},function(){});
	}
	function iconrequestmodeviewclicked2(requestid,checkstatus){
		mymodal2.showcustom("Execute","60%");
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
</script>