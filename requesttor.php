<?php //TOR
	if(isset($_GET['loadtable'])){//load main table
		include 'db/dbconnect.php';
		$currentpage = (myGET('currentpage') * 12);
		$bus = initbu();
		echo '<table mytable fullwidth>
				<tr>
					<th col2>Transaction Date</th>
					<th>Company Name</th>
					<th>Business Unit</th>
					<th>Request type</th>
					<th centertext>Status</th>
					<th col1 centertext>Action</th>
				</tr>';
				
				
			//code for requesttyperole
			$requesttyperolerfs = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='RFS' and usertypeid=".getusertype());
			$requesttyperolerfsids = mysqlm_fetch_array($requesttyperolerfs)[0];
			$requesttyperoletor = mysqlm_query("select GROUP_CONCAT(typeofrequestid SEPARATOR ',') from typeofrequestrole where userid=".getuserid()." and requesttype='TOR' and usertypeid=".getusertype());
			$requesttyperoletorids = mysqlm_fetch_array($requesttyperoletor)[0];
			//end of requesttyperole
			
			$query = mysqlm_query("select
								DATE_FORMAT(datetoday,'<span colorgreen>%a &bull;</span><span color1>%h:%i %p &bull;</span> %b %d, %Y'),
								com.companyname thecom,
								if(gettortaskstatus(bu.id,4,a.status)='false','Pending',
									if(gettortaskstatus(bu.id,3,a.executed)='false','Pending',
										if(gettortaskstatus(bu.id,2,a.buheadid)='false','Pending',
											if(gettortaskstatus(bu.id,5,a.iadstatus)='false','Pending','Approved')
										)
									)
								) as thestatus,
								b.tortype as thetortype,
								LPAD(requestnumber,4,0),
								count(requestgroup),
								requestgroup,
								bu.businessunit as thebu,a.id as requestid,a.businessunit as bu2,
								bu.id as buid,a.remarks as remarks
								from requests a,tortypes b , tblcompany com,tblbusinessunit bu,users u
								where a.tortype = b.id and u.id = ".getuserid()." and u.businessunitid = bu.id and com.id = bu.companyid and u.id = a.userid  and a.thetype=2
								and (
										".(strlen($requesttyperolerfsids)>0?"(a.typeofrequest in (".$requesttyperolerfsids."))":"false")."
										or ".(strlen($requesttyperoletorids)>0?"  (a.tortype in (".$requesttyperoletorids."))":"false").
										((strlen($requesttyperolerfsids)==0 and strlen($requesttyperoletorids)==0)?" or true":"")."
										)
								
								and (".(myGET('filternumber')==""?"(DATE_FORMAT(a.datetoday,'%m/%Y')=DATE_FORMAT(STR_TO_DATE(".mySTRget('date').",'%d/%m/%Y'),'%m/%Y') or ".mySTRget('date')."=\"all\")":
									"a.requestnumber = '".ltrim(myGET('filternumber'),'0')."'").")
								group by a.requestgroup ".(myGET('showapproved')=='unchecked'?"Having thestatus='Pending'":"")."
								order by a.datetoday desc limit 12 offset ".$currentpage.";");
				while($row=mysqlm_fetch_array($query)){
					echo '<tr trhoverable>
							<td>'.$row[0].'</td>
							<td>'.$row['thecom'].'</td>
							<td>'.($row['bu2']==""?$row['thebu']:$bus[$row['bu2']]).'</td>
							<td>'.$row['thetortype'].'</td>
							<td  class="tdrequestsstatus" '.$row['thestatus'].' centertext>'.$row['thestatus'].($row['remarks']!=''?" <i class='fa fa-commenting' title='remarked'/>":"").'</td>
							<td centertext><div iconbtn onclick="iconrequestopenclicked(\''.$row['requestid'].'\',\''.$row['requestgroup'].'\',\''.$row['buid'].'\')" title="open"><i class="fa fa-list"/></div></td>
						</tr>';
				}
			
		echo '</table>';
		if(mysqlm_rowcount($query)==0){
			echo '<div mygroup col12 style="background-color:white" centertext paddingedall bbox>No results.</div>';
		}
		breakhere($con);
	}
	elseif(isset($_GET['addnewrequest'])){//add new request
		include 'db/dbconnect.php';
		echo '<div class="mygroup" col12>
				<div col2 coli colctrl>Company Name</div><input class="txtcompanyname" readonly value="'.getcompanyname().'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Business Unit</div><input class="txtbusinessunit" readonly value="'.getbuname().'" type="text" col3/>
				<div col2 coli colctrl>Contact Number</div><input class="txtcontactno" readonly value="'.getcontactnumber().'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Date</div><input class="txtdate" type="text" col3/>
				<div col2 coli colctrl>Address</div><input class="txtaddress" readonly value="'.getaddress().'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Programmer/MIS</div>';
				$query2=mysqlm_query("select concat(firstname,' ',lastname),id from users where usertype=3");
				echo '<select class="cbotoexecute" col3>';
				while($row2 = mysqlm_fetch_array($query2)){
					echo '<option value="'.$row2[1].'">'.$row2[0].'</option>';
				}
				echo '</select>';
		echo '</div>';
		echo '<div mygroup nobordertop nopadding col12>';
			echo '<div mygroup noborder displaytablecell col6>
					<div fontbold marginedbottom>Type of request</div>';
				$query = mysqlm_query("select a.tortype,a.id from tortypes a,userrequesttyperoletor b where a.id=b.requesttypeid and b.userid=".getuserid()." order by a.tortype");
				while($row=mysqlm_fetch_array($query)){
					echo '<div coli col12 marginedbottom><div class="mycheckbox chktortypes" thevalue="'.$row[0].'" thevalueid="'.$row[1].'" coli></div><span marginedleft>'.$row[0].'</span></div>';
				}
			echo '</div>';
			echo '<div mygroup noborder hasborderleft displaytablecell col6>
					<div fontbold marginedbottom>Details</div>
					<textarea class="txtdetails" resizenone bbox style="height:100px" col12></textarea>';
			echo '</div>';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>
				<div fontbold marginedbottom>Purpose</div>
				<input class="txtpurpose" type="text" col12 />
				';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>';
			echo '<button marginedtop col3 floatright onclick="btnaddnewrequestsubmit(\'add\',\'\')">Submit</button>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['editrequest'])){//edit request
		include 'db/dbconnect.php';
		$requestid=myGET('requestid');
		$query3 = mysqlm_query("select LPAD(requestnumber,4,0) as requestnumber,date,tortype,requesttypevalue,purpose from requests where id = $requestid");
		$row3 = mysqlm_fetch_array($query3);
		echo '<div mygroup col12>
				<div col2 coli colctrl>Company Name</div><input class="txtcompanyname" readonly value="'.getcompanyname().'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Business Unit</div><input class="txtbusinessunit" readonly value="'.getbuname().'" type="text" col3/>
				<div col2 coli colctrl>Control Number</div><input class="txtcontrolnumber" readonly type="text" colorred value="'.$row3['requestnumber'].'" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Date</div><input class="txtdate" type="text" value="'.$row3['date'].'" col3/>
				<div col2 coli colctrl>Address</div><input class="txtaddress" readonly value="'.getaddress().'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Programmer/MIS</div>';
				$query2=mysqlm_query("select concat(firstname,' ',lastname),id from users where usertype=3");
				echo '<select class="cbotoexecute" col3>';
				while($row2 = mysqlm_fetch_array($query2)){
					echo '<option value="'.$row2[1].'">'.$row2[0].'</option>';
				}
				echo '</select>';
		echo '</div>';
		echo '<div mygroup nobordertop displaytablecell col5>
				<div fontbold marginedbottom>Type of request</div>';
			$query = mysqlm_query("select tortype,id from tortypes");
			while($row=mysqlm_fetch_array($query)){
				echo '<div coli col12 marginedbottom><div class="mycheckbox chktortypes" '.($row3['tortype']==$row['id']?'value="checked"':'').' thevalue="'.$row[0].'" thevalueid="'.$row[1].'" coli></div><span marginedleft>'.$row[0].'</span></div>';
			}
		echo '</div>';
		echo '<div mygroup nobordertop noborderleft displaytablecell col6>
				<div fontbold marginedbottom>Details</div>
				<textarea class="txtdetails" resizenone bbox style="height:100px" col12>'.$row3['requesttypevalue'].'</textarea>';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>
				<div fontbold marginedbottom>Purpose</div>
				<input class="txtpurpose" type="text" col12 value="'.$row3['purpose'].'" />
				';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>';
			echo '<div buttongroup col4 floatright>';
				echo '<button col6 onclick="btnaddnewrequestsubmit(\'edit\',\''.$requestid.'\')"><i class="fa fa-save"/> Save</button>';
				echo '<button col6 onclick="btncanceleditmodeclicked('.$requestid.')"><i class="fa fa-times"/> Cancel</button>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_POST['saveedit'])){//save edited request
		include 'db/dbconnect.php';
		$requestid=myPOST('requestid');
		$typeofrequest=mySTRpost('typeofrequest');
		$details=mySTRpost('details');
		$date=mySTRpost('date');
		$purpose=mySTRpost('purpose');
		$toexecute=mySTRpost('toexecute');
		mysqlm_query("update requests set tortype=$typeofrequest,
										requesttypevalue=$details,
										date=$date,
										purpose=$purpose,
										userexecute=$toexecute
										where id=$requestid");
		breakhere($con);
	}
	elseif(isset($_POST['submitrequest'])){//submit request
		include 'db/dbconnect.php';
		$date=mySTRpost("date");
		$typeofrequest=mySTRpost("typeofrequest");
		$details=mySTRpost("details");
		$purpose=mySTRpost("purpose");
		$userid = getuserid();
		$query3 = mysqlm_query("select IFNULL(max(requestgroup)+1,0) from requests");
		$requestgroup = mysqlm_fetch_array($query3)[0];
		$query2 = mysqlm_query("select IFNULL(max(requestnumber)+1,0) from requests where thetype=2");
		$requestnumber = mysqlm_fetch_array($query2)[0];
		$toexecute = mySTR(myPOST('toexecute'));
		$idcount = mysqlm_fetch_array(mysqlm_query("select max(idcount) + 1 from requests"))[0];
		mysqlm_query("insert into requests(id,date,thetype,tortype,requesttypevalue,purpose,userid,datetoday,requestgroup,requestnumber,userexecute".(isset($_SESSION['currentbu'])?",businessunit":"").")
						values(null,$date,2,$typeofrequest,$details,$purpose,$userid,CURRENT_TIMESTAMP,$requestgroup,$requestnumber,$toexecute".(isset($_SESSION['currentbu'])?",".mySTR(getcurrentbuid()):"").")");
		breakhere($con);
 	}
	elseif(isset($_GET['requestopenview'])){//view request TOR
		include 'db/dbconnect.php';
		$requestid = myGET('requestid');
		$query2 = mysqlm_query("select date,tortype,purpose,requesttypevalue,LPAD(requestnumber,4,0) as requestnumber,remarks,id from requests where id = $requestid");
		$row2 = mysqlm_fetch_array($query2);
		echo '<div class="mygroup" col12>
				<div col2 coli colctrl>Company Name</div><input class="txtcompanyname" readonly value="'.getcompanyname($row2['id']).'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Business Unit</div><input class="txtbusinessunit" readonly value="'.getbuname($row2['id']).'" type="text" col3/>
				<div col2 coli colctrl>Control Number</div><input class="txtcontrolnumber" readonly type="text" colorred value="'.$row2['requestnumber'].'" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Date</div><input class="txtdate" value="'.$row2['date'].'" readonly type="text" col3/>
				<div col2 coli colctrl>Address</div><input class="txtaddress" readonly value="'.getaddress($row2['id']).'" type="text" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Requested by</div><input class="txtaddress" readonly type="text" value="'.getuserfullname().'" col3/>
			</div>';
		echo '<div displaytable col12>';
			echo '<div class="mygroup" nobordertop col3 displaytablecell>
					<div fontbold marginedbottom>Type of request</div>';
					$query = mysqlm_query("select tortype,id from tortypes where id=".$row2['tortype']);
					$row=mysqlm_fetch_array($query);
					echo '<div coli col12 marginedbottom><div class="mycheckbox chktortypes" value="checked" coli></div><span marginedleft>'.$row[0].'</span></div>';
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
				echo '<button col4 onclick="editrequestclicked(\''.$requestid.'\',this)"><i class="fa fa-pencil"/> Edit</button>';
				echo '<button col4 onclick="deleterequestclicked(\''.$requestid.'\')"><i class="fa fa-trash"/> Delete</button>';
				echo '<button col4 onclick="printrequestclicked(\''.$requestid.'\',\''.$row2['requestnumber'].'\')"><i class="fa fa-print"/> Print</button>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	// elseif(isset($_GET['requestopen'])){//open table to view tor approve status
		// include 'db/dbconnect.php';
		// $buid=myGET('buid');
		// $requestgroup = mySTRget('requestgroup');
		// $query = mysqlm_query("select LPAD(requestnumber,4,0) as number,`date`,
									// if(IFNULL(a.iadstatus,'Pending')='Pending','Pending','Approved') as iadstatus,
									// a.id as mainid,
									// if(IFNULL(a.executed,'Pending')='Pending','Pending','Approved') as executed,
									// if(IFNULL(a.buheadid,'Pending')='Pending','Pending','Approved') as buhead,
									// IFNULL(`status`,'Pending') as groupivstatus
									// from requests a
									// where a.requestgroup = '".myGET('requestgroup')."' order by a.requestnumber");
		// echo '<table mytable fullwidth>
				// <tr>
				// <th col1>Number</th>
				// <th>Date</th>';
			// $butasks = getbutaskstor($buid,2);
				// foreach($butasks as $butask){
					// echo '<th centertext>'.$butask[0].'</th>';
				// }
			// // echo '<th centertext col3>Approved</th>
				// // <th centertext>Verified</th>
				// // <th col2 centertext>Adjusted/Reprinted</th>';
			// echo '<th col2 centertext>Action</th></tr>
		// ';
		// while($row=mysqlm_fetch_array($query)){
			// $approvedna=checkstatus(array($row['iadstatus'],$row['executed'],$row['buhead']));
			// echo '<tr trhoverable>
					// <td colorred fontbold centertext>'.$row['number'].'</td>
					// <td>'.$row['date'].'</td>';
					// foreach($butasks as $butask){
						// echo'<td class="tdrequestsstatus" '.$row[$butask[1]].' centertext>'.$row[$butask[1]].'</td>';
					// }
				// // echo'<td class="tdrequestsstatus" '.$row['buhead'].' centertext>'.$row['buhead'].'</td>
					// // <td class="tdrequestsstatus" '.$row['iadstatus'].' centertext>'.$row['iadstatus'].'</td>
					// // <td class="tdrequestsstatus" '.$row['executed'].' centertext>'.$row['executed'].'</td>';
				// echo'<td centertext>
						// <div iconbtn onclick="iconrequestmodeviewclicked(\''.$row['mainid'].'\',\''.$approvedna.'\')" coli title="view"><i class="fa fa-list"/></div>
						// <!--'.($row[4]=="Approved"?'<div iconbtn coli marginedleft title="print"><i class="fa fa-print"/></div>':"").'-->
					// </td>
				// </tr>';
		// }
		// echo '</table>';
		// breakhere($con);	
	// }
	elseif(isset($_GET['printthisform'])){//print TOR form
		include 'db/dbconnect.php';
		$row = mysqlm_fetch_array(mysqlm_query("select LPAD(a.requestnumber,4,0) as number,
												   b.businessunit as businessunit,
												   concat(u.firstname,' ',u.lastname) as requestedby,
												   a.purpose as purpose,
												   a.tortype as tortype,
												   a.requesttypevalue as details,
												   a.iadstatus as verifiedby,
												   a.executed as adjustedby,
												   a.buheadid as approvedby,
												   a.id as idnumber
											from requests a,tblbusinessunit b,users u
											where b.id=u.businessunitid and u.id=a.userid and a.id=".myGET('requestid')));
												
		$verifiedby = mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['verifiedby']))[0];
		$adjustedby = mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['adjustedby']))[0];
		$approvedby = mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['approvedby']))[0];
		
		$txtfile = "printing\\tempxlsx\\".$row['idnumber'].".txt";
		file_put_contents($txtfile,str_replace(array("\n","\r"),",      ",$row['details']));
		
		echo python("python.exe",mySTR("TOR").//type
								" ".mySTR("printing/logomarcelafarms.png").//imagefile
								" ".mySTR($row['businessunit']).//businessunit
								" ".mySTR($row['idnumber']).//idnumber
								" ".mySTR($row['number']).//number
								" ".mySTR($row['purpose']).//purpose
								" ".mySTR(($row['tortype']=="1"?"a":"")).//adjustment
								" ".mySTR(($row['tortype']=="2"?"a":"")).//authoritytoreprint
								" ".mySTR(($row['tortype']=="3"?"a":"")).//authoritytocancel
								" ".mySTR($txtfile).//details
								//" ".mySTR($row['details']).//details
								" ".mySTR(strtoupper($row['requestedby'])).//requestedby
								" ".mySTR(strtoupper($verifiedby)).//verifiedby
								" ".mySTR(strtoupper($adjustedby)).//adjustedby
								" ".mySTR(strtoupper($approvedby))//approvedby
								);
		
		breakhere($con);
	}
?>
<br/>
<?php
	include 'db/dbconnect.php';
	checksession();
?>
<div class="myframe">
	<h3>
		Current BU: 
		<?php
			if(getusertype()==1){
				echo '<span color1>'.getbuname().'</span>';
			}
			elseif(getusertype()==2 or getusertype()==7){
				echo '<span color1 class="currentbu">'.getcurrentbu().' </span><span class="editcurrentbu" onclick="editcurrentbuclicked(\'TOR\')" title="Change Current BU">&nbsp;<i class="fa fa-pencil"/></span>';
			}
		?>
	</h3>
	<div coli col12>
		<div mytoolsgroup col6 displaytablecell nopadding>
			<div mytoolsgroup displaytablecell noborder col3>
				<button onclick="addnewrequestclicked()" col12> <i class="fa fa-plus"/> Add New Request</button>
			</div>
			<div mytoolsgroup displaytablecell noborder hasborderleft col5>
				<div coli marginedright fontbold>Filter Date</div>
				<button class="monthselector filterrequestmonth" datevalue="">This month</button>
			</div>
			<div mytoolsgroup displaytablecell noborder hasborderleft col4>
				<input class="filternumber" centertext colorred type="text" col12 placeholder="Control Number"/>
			</div>
		</div>
		<div mytoolsgroup col6 displaytablecell noborderleft>
			<div coli col3 marginedleft><div class="mycheckbox chkshowapproved" value="checked" coli></div><div coli marginedleft> Show approved</div></div>
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
	var modaladdrequest = new MyModal();
	var typeofrequest;
	var currentpage=0;
	$.get("execute.php?getmaxrecords",function(result){
		$(".mypagination").mypagination(result,function(selectedpage){
			currentpage=selectedpage;
			loadtable();
		});
	});
	$(".mycheckbox").mycheckbox();
	$(".monthselector").monthselector(function(){
		loadtable();
	});
	$(".filternumber").on('keyup',function(evt){
		if(evt.keyCode==13){
			loadtable();
		}
	});
	loadtable();
	$(".chkshowapproved").mycheckboxonclick(function(){
		loadtable();
	});
	function loadtable(){
		var reqdate = $(".filterrequestmonth").datevalue();
		$.get("requesttor.php?loadtable&date="+reqdate+"&showapproved="+$(".chkshowapproved").mycheckboxvalue()+
							"&filternumber="+$(".filternumber").val()+
							"&currentpage="+currentpage,function(result){
			$(".requeststable").html(result);
		});
	}
	function addnewrequestclicked(){
		modaladdrequest.showcustom("TOR Form Request","60%");
		$.get("requesttor.php?addnewrequest",function(result){
			modaladdrequest.body(result);
			$(".txtdate").datepicker();
			$(".mycheckbox").mycheckbox();
			$(".chktortypes").checkboxradio();
			$(".chktortypes").mycheckboxonclick(function(){
				typeofrequest = $(this).attr("thevalueid");
			});
		});
	}
	function btnaddnewrequestsubmit(iswhatmode,requestid){
		if($(".txtdate").val()==""){
			showMyAlertError("Form Request Submission Failed!","Please select date.");
			return;
		}
		var typeofrequest=$(".chktortypes[value=checked]").attr("thevalueid");
		if(typeofrequest==undefined){
			showMyAlertError("Error!","Please select type of request.");
			return;
		}
		var details = $(".txtdetails").val();
		var date = $(".txtdate").val();
		var purpose = $(".txtpurpose").val();
		var	toexecute = $(".cbotoexecute").val();
		if(iswhatmode=="add"){
			modaladdrequest.showloading();
			$.post("requesttor.php",
					{details:details,
					date:date,
					purpose:purpose,
					typeofrequest:typeofrequest,
					submitrequest:emptyval,
					toexecute:toexecute
					},
					function(result){
						modaladdrequest.close();
						showMyAlert("New request added!","");
						loadtable();
					}
			);
		}
		else if(iswhatmode=="edit"){
			modalrequestview.showloading();
			$.post("requesttor.php",
				{requestid:requestid,
				details:details,
				date:date,
				purpose:purpose,
				typeofrequest:typeofrequest,
				toexecute:toexecute,
				saveedit:emptyval
				},
				function(result){
					$.get("requesttor.php?requestopenview&requestid="+requestid,function(result2){
						modalrequestview.body(result2);
						modalrequestview.title("TOR");
						$(".mycheckbox").mycheckbox();
					});
					showMyAlert("Form Request saved!","");
					loadtable();
				}
			);
		}
	}
	var modalrequesttable = new MyModal();
	function iconrequestopenclicked(id,requestgroup,buid){
		selectedbuid = buid;
		modalrequesttable.showcustom("Request",'60%');
		modalrequesttable.settag(requestgroup);
		$.get("requests.php?requestopen&requestid="+id+"&requestgroup="+requestgroup+"&buid="+selectedbuid,function(result){
			modalrequesttable.body(result);
			$(".mycheckbox").mycheckbox();
		});
	}
	var modalrequestview = new MyModal();
	function iconrequestmodeviewclicked2(requestid,checkstatus){
		modalrequestview.showcustom("TOR","60%");
		$.get("requesttor.php?requestopenview&requestid="+requestid,function(result){
			modalrequestview.body(result);
			modalrequestview.settag(checkstatus);
			$(".mycheckbox").mycheckbox();
		});
	}
	var modalprint = new MyModal();
	function deleterequestclicked(requestid){
		if(modalrequestview.gettag()=='trolls' || modalrequestview.gettag()=='tananApproved'){
			showMyAlertError("Cannot delete, Transaction Override Request is already approved.",'');
			return;
		}
		showMyConfirm("Delete request?",function(){
			$.get("request.php?deleterequest&requestid="+requestid,function(result){
				showMyAlert("Request successfully deleted!","");
				loadtable();
				modalrequesttable.close();
				// $.get("requesttor.php?requestopenview&requestid="+requestid+"&requestgroup="+modalrequesttable.gettag(),function(result){
					// modalrequesttable.body(result);
					// $(".mycheckbox").mycheckbox();
				// });
				modalrequestview.close();
			});
		},function(){});
	}
	function editrequestclicked(requestid,thisel){
		if(modalrequestview.gettag()!='tananPending'){
			showMyAlertError("Cannot edit, Transaction Override Request is already approved.",'');
			return;
		}
		modalrequestview.showloading();
		$.get("requesttor.php?editrequest&requestid="+requestid,function(result){
			modalrequestview.title("TOR <span color1>(Edit)</span>")
			modalrequestview.body(result);
			$(".txtdate").datepicker();
			$(".mycheckbox").mycheckbox();
			$(".chktortypes").checkboxradio();
			$(".chktortypes").mycheckboxonclick(function(){
				typeofrequest = $(this).attr("thevalueid");
			});
		});
	}
	function btncanceleditmodeclicked(requestid){
		modalrequestview.showloading();
		modalrequestview.title("TOR");
		$.get("requesttor.php?requestopenview&requestid="+requestid,function(result){
			modalrequestview.body(result);
			$(".mycheckbox").mycheckbox();
		});
	}
	function printrequestclicked(requestid,requestnumber){
	//	var mywindow = window.open('printrequest.php', 'PRINT', 'height=400,width=600');
		if(modalrequestview.gettag()!='tananApproved'){
			showMyAlertError("Cannot print, Transaction Override Request still pending.",'');
			return;
		}
		showprint("TOR",requestid,requestnumber);
	//	mymodal.body('<iframe src="printrequest.php" col12 noborder style="height:70vh"></iframe>')
		//PrintElem($(el).parentsUntil("",".mymodalbody"));
	}
</script>