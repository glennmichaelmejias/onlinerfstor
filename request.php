<?php //RFS
	if(isset($_GET['loadtable'])){//load main table
		include 'db/dbconnect.php';
		$currentpage = (myGET('currentpage') * 12);
		$bus = initbu();
		echo '<table mytable fullwidth>
				<tr>
					<th col2>Transaction Date</th>
					<th displaynone>Number</th>
					<th displaynone>Request Mode</th>
					<th displaynone>Type of Request</th>
					<th>Company Name</th>
					<th>Business Unit</th>
					<th col2 centertext>Total Requests</th>
					<th centertext>Status</th>
					<th col1 centertext>Action</th>
				</tr>';
				$query = mysqlm_query("select
										DATE_FORMAT(datetoday,'<span colorgreen>%a &bull;</span>  <span color1>%h:%i %p &bull;</span> %b %d, %Y'),
										requesttype,
										com.companyname thecom,
										if(getrfstaskstatus(bu.id,4,a.status,(count(ifnull(a.status,'a'))-count(a.status='Approved')))='false','Pending',
											if(getrfstaskstatus(bu.id,3,a.executed,(count(ifnull(a.executed,'a'))-count(a.executed='Approved')))='false','Pending',
												if(getrfstaskstatus(bu.id,2,a.buheadid,(count(ifnull(a.buheadid,'a'))-count(a.buheadid='Approved')))='false','Pending',
													if(getrfstaskstatus(bu.id,5,a.iadstatus,(count(ifnull(a.iadstatus,'a'))-count(a.iadstatus='Approved')))='false','Pending','Approved')
												)
											)
										) as rfsstatus,
										themode,
										LPAD(requestnumber,4,0),
										count(requestgroup),
										requestgroup,
										bu.id as thebu,
										if(ifnull(a.softsysstatus,'Pending')='Pending','Pending',
											if(ifnull(a.executed,'Pending')='Pending','Pending','Approved')
										) as softsysstatus,
										a.typeofrequest as typeofrequest,a.businessunit as bu2
										from requests a,typeofrequest b,requestmode c,tblcompany com,tblbusinessunit bu,users u
										where a.typeofrequest = b.id and a.requestmode = c.id and u.id = ".getuserid()." and u.businessunitid = bu.id and com.id = bu.companyid and u.id = a.userid  and a.thetype=1
										and (".(myGET('filternumber')==""?"(DATE_FORMAT(a.datetoday,'%m/%Y')=DATE_FORMAT(STR_TO_DATE(".mySTRget('date').",'%d/%m/%Y'),'%m/%Y') or ".mySTRget('date')."=\"all\")":
											"a.requestnumber = '".ltrim(myGET('filternumber'),'0')."'").")
										group by a.requestgroup ".(myGET('showapproved')=='unchecked'?"Having if(typeofrequest=7,softsysstatus='Pending',rfsstatus='Pending')":"")."
										order by a.datetoday desc limit 12 offset ".$currentpage.";");
				
				while($row=mysqlm_fetch_array($query)){
					$thestatus;
					($row['typeofrequest']==7?$thestatus=$row['softsysstatus']:$thestatus=$row['rfsstatus']);
					$buid = ($row['bu2']==""?$row['thebu']:$row['bu2']);
					echo '<tr trhoverable>
							<td>'.$row[0].'</td>
							<td displaynone>'.$row[5].'</td>
							<td displaynone>'.$row[4].'</td>
							<td displaynone>'.$row[1].'</td>
							<td>'.$row['thecom'].'</td>
							<td>'.$bus[$buid].'</td>
							<td centertext>'.$row[6].'</td>
							<td class="tdrequestsstatus" '.$thestatus.' centertext>'.$thestatus.'</td>
							<td centertext><div iconbtn onclick="iconrequestopenclicked(\''.$row[7].'\',\''.$buid.'\')" title="open"><i class="fa fa-list"/></div></td>
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
				<!--<div col7 coli colctrl></div>-->
				<div displaynone col2 colctrl >Requested by</div><input class="txtaddress" displaynone readonly type="text" value="'.getuserfullname().'" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Programmer/MIS</div>';
				$query=mysqlm_query("select concat(firstname,' ',lastname),id from users where usertype=3");
				echo '<select class="cbotoexecute" col3>';
				while($row = mysqlm_fetch_array($query)){
					echo '<option value="'.$row[1].'">'.$row[0].'</option>';
				}
				echo '</select>';
			//echo '<input class="txtaddress" readonly type="text" value="'.getuserfullname().'" col3/>';
		echo'</div>';
		echo '<div class="mygroup" nobordertop col12>
			<button marginedleft onclick="btnaddmodeclicked()"><i class="fa fa-plus"/> Add mode</button>
			<table mytable marginedtop class="requestmodes" fullwidth>
				<tr><th displaynone>Number</th><th displaynone>Mode</th><th>Type</th><th>Action</th></tr>
			</table>
		</div>';
		echo '<div class="mygroup" nobordertop col12>';
			echo '<div floatright coli col6><strong>Note: </strong> Click submit to send your request.<button marginedleft marginedtop col4 onclick="btnaddnewrequestsubmit()"><i class="fa fa-external-link-square"/> Submit</button></div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_POST['submitrequest'])){//submit request
		include 'db/dbconnect.php';
		$mysqlm = new Mysqlm();
		$mysqlm->connect();
		//$requestgroup = mySTRpost('requestgroup');
		$query3 = $mysqlm->mquery("select IFNULL(max(requestgroup)+1,0) from requests");
		$requestgroup = $mysqlm->mysqlm_fetch_array($query3)[0];
		$userid = getuserid();
		
	//var_dump($formfile['tmp_name']);
		//move_uploaded_file($_FILES['filesformdata']['tmp_name'], 'attachedfiles/' . $_FILES['filesformdata']['name']);
	//	var_dump($formfile);
		echo myPOST('requesttypevalue');
		for($a=0;$a<count(explode("<separator>,",$_POST['typeofrequest']))-1;$a++){
			$idcount = mysqlm_fetch_array(mysqlm_query("select max(idcount) + 1 from requests"))[0];
			$tdate=mySTR(explode("<separator>,",myPOST('tdate'))[$a]);
			$requestmode=mySTR(explode("<separator>,",myPOST('requestmode'))[$a]);
			$typeofrequest=mySTR(explode("<separator>,",myPOST('typeofrequest'))[$a]);
			$purpose=mySTR(explode("<separator>,",myPOST('purpose'))[$a]);
			$requesttypevalue=mySTR(explode("<separator>,",myPOST('requesttypevalue'))[$a]);
			$query2 = $mysqlm->mquery("select IFNULL(max(requestnumber)+1,0) from requests where thetype=1");
			$requestnumber = $mysqlm->mysqlm_fetch_array($query2)[0];
			$toexecute = mySTR(explode("<separator>,",myPOST('toexecute'))[$a]);
			$systemtype=mySTR(explode("<separator>,",myPOST('systemtype'))[$a]);
			$mysqlm->mquery("insert into requests(id,date,requestmode,typeofrequest,purpose,requesttypevalue,requestnumber,datetoday,requestgroup,userid,thetype,userexecute,systemtype".(isset($_SESSION['currentbu'])?",businessunit":"").")
									values(null,$tdate,$requestmode,$typeofrequest,$purpose,$requesttypevalue,$requestnumber,CURRENT_TIMESTAMP,$requestgroup,$userid,1,$toexecute,$systemtype".(isset($_SESSION['currentbu'])?",".mySTR(getcurrentbuid()):"").")");		
		}
		if(isset($_FILES['filesformdata'])){
			$formfile = $_FILES['filesformdata'];
			//var_dump($formfile);
			foreach($_FILES['filesformdata']['tmp_name'] as $key => $inputfiles){
				$trequestnumber=mySTR("RFS-".$requestnumber);
				$filename=mySTR($formfile['name'][$key]);
				$query4 = $mysqlm->mysqlm_fetch_array($mysqlm->mquery("select IFNULL(max(filenumber)+1,0) from attachedfiles"));
				$filenumber = $query4[0];
				$mysqlm->mquery("insert into attachedfiles(id,requestnumber,filename,filenumber) values(null,$trequestnumber,$filename,$filenumber)");
				move_uploaded_file($inputfiles, "attachedfiles/$filenumber");
			}	
		}
		
		breakhere($con);
	}
	elseif(isset($_GET['addnewmode'])){//add new mode
		include 'db/dbconnect.php';
		$query = mysqlm_query("select a.requesttype,a.id,a.thecolumns as thecolumns from typeofrequest a,userrequesttyperole b where a.id = b.requesttypeid and b.userid=".getuserid()." order by a.id");
		echo '<div displaytable fullheight fullwidth>';
			echo '<div displaytablecell col6>';
				echo '<div class="mygroup" col12>
						<div fontbold colctrl>Type of Request</div>';
						while($row=mysqlm_fetch_array($query)){
							echo '<div coli col6 marginedtop><div class="mycheckbox chkrequesttype" thevalueid="'.$row[1].'" thevalue="'.$row[0].'" coli thecolumns="'.$row['thecolumns'].'"></div><div marginedleft coli>'.$row[0].'</div></div>';
						}
				echo '</div>';
				$query = mysqlm_query("select themode,id from requestmode where fortype=0");
				echo '<div class="mygroup divrequestmode" nobordertop col12 style="height:50px">
						<div fontbold colctrl>Request mode</div>';
						while($row=mysqlm_fetch_array($query)){
							echo '<div coli col4 marginedbottom><div class="mycheckbox chkrequestmode" thevalueid="'.$row[1].'" thevalue="'.$row[0].'" coli></div><div marginedleft coli>'.$row[0].'</div></div>';
						}
				echo '</div>';
				echo '<div class="mygroup" nobordertop col12>
						<div fontbold>Purpose</div>
						<textarea class="txtpurpose" bbox resizenone col12 style="height:100px"></textarea>
						
					</div>';
			echo '</div>';
			echo '<div class="mygroup" noborderleft col6 displaytablecell>
					<div fontbold class="therequesttype">Details</div>
					<div class="requesttypediv" marginedtop style="height:321px;overflow:auto" col12>
						
					</div>
				</div>';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>';
			echo '<div buttongroup col4 floatright>';
				echo '<button col6 onclick="btnaddmodeattachfiles()"><i class="fa fa-file-text"/> Attachments</button>';
				echo '<button col6 onclick="btnmodesubmitclicked(\'add\',0)"><i class="fa fa-check"/> Done</button>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['editmode'])){//edit mode
		include 'db/dbconnect.php';
		$requestid = myGET('requestid');
		$query = mysqlm_query("select purpose,typeofrequest,requestmode,requesttypevalue,systemtype from requests where id=".$requestid);
		$row=mysqlm_fetch_array($query);
		$requesttypevaluelabel="";
		echo '<div displaytable fullheight>';
			echo '<div displaytablecell col6>';
				echo '<div class="mygroup" col12>
						<div fontbold colctrl>Type of Request</div>';
						$query2 = mysqlm_query("select a.requesttype,a.id,a.thecolumns as thecolumns from typeofrequest a,userrequesttyperole b where a.id = b.requesttypeid and b.userid=".getuserid()." order by a.id");
						$thecolumns="";
						while($row2 = mysqlm_fetch_array($query2)){
							if($row['typeofrequest']==$row2[1]){
								$thecolumns=$row2['thecolumns'];
								echo '<div coli col6 marginedbottom><div class="mycheckbox chkrequesttype" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli thecolumns="'.$row2['thecolumns'].'"></div><div marginedleft coli>'.$row2[0].'</div></div>';
								$requesttypevaluelabel = $requesttypevaluelabel.$row2[0];
							}
							else{
								echo '<div coli col6 marginedbottom><div class="mycheckbox chkrequesttype" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli thecolumns="'.$row2['thecolumns'].'"></div><div marginedleft coli>'.$row2[0].'</div></div>';
							}
						}
				echo '</div>';
				echo '<div class="mygroup divrequestmode" nobordertop col12 style="height:100px">';
					if($row['typeofrequest']==7){
						$query3 = mysqlm_query("select systemtype,id from systemtype");
						echo '<div fontbold colctrl> Type of System</div>';
						while($row3=mysqlm_fetch_array($query3)){
							if($row['systemtype']==$row3['id']){
								echo '<div coli col4><div class="mycheckbox chksystemtype" value="checked" thevalueid="'.$row3[1].'" thevalue="'.$row3[0].'" coli></div><div coli marginedleft>'.$row3['systemtype'].'</div></div>';
							}
							else{
								echo '<div coli col4><div class="mycheckbox chksystemtype" thevalueid="'.$row3[1].'" thevalue="'.$row3[0].'" coli></div><div coli marginedleft>'.$row3['systemtype'].'</div></div>';
							}
						}
					}
					else{
						echo '<div fontbold colctrl>Request mode</div>';
					}
					if($row['typeofrequest']!=7){
						$query2 = mysqlm_query("select themode,id from requestmode ".($row['typeofrequest']==7?" where fortype=7":" where fortype=0"));
						while($row2=mysqlm_fetch_array($query2)){
							if($row['requestmode']==$row2[1]){
								echo '<div coli '.($row['typeofrequest']==7?"col6":"col4").' marginedbottom><div class="mycheckbox chkrequestmode" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
								$requesttypevaluelabel = '(<span color1>'.$row2[0].'</span>) ';
							}
							else{
								echo '<div coli '.($row['typeofrequest']==7?"col6":"col4").' marginedbottom><div class="mycheckbox chkrequestmode" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
							}
						}
					}
				echo '</div>';
				echo '<div class="mygroup" nobordertop col12>
						<div fontbold>Purpose</div>
						<textarea class="txtpurpose" bbox resizenone col12 style="height:50px">'.$row['purpose'].'</textarea>
					</div>';
			echo '</div>';
			echo '<div class="mygroup" noborderleft col6 displaytablecell>';
				if($row['typeofrequest']==7){
					echo '<div fontbold class="therequesttype">System/Software</div>';
				}
				elseif($row['typeofrequest']==1){
					echo '<div fontbold class="therequesttype">Price Change Lists</div>';
				}
				else{
					echo '<div fontbold class="therequesttype">Details</div>';
				}
				echo '<div class="requesttypediv" marginedtop style="height:92%;overflow:auto" col12>';
						//if($row['typeofrequest']==1){
						if(strlen($thecolumns)>0){
							$str = explode("\n",$row['requesttypevalue']);
							$thecolumns = explode(",",$thecolumns);
							echo '<div col12>
									<table class="tblpricechangelist" mytable col12><tr><th col6>'.$thecolumns[0].'</th><th col6>'.$thecolumns[1].'</th></tr>';
										for($a=0;$a<50;$a++){
											if($a<count($str)-1 and count(explode("=",$str[$a]))>1){
												//if(count(explode("=",$str[$a]))>1){
													$item = explode("=",$str[$a])[0];
												//	echo count(explode("=",$str[$a]))."<br/>";
													$price = explode("=",$str[$a])[1];
												//}
											}
											else{
												$item="";
												$price="";
											}
											echo '<tr>
													<td class="tdeditable tditemname" centertext value="'.$item.'">'.$item.'</td>
													<td class="tdeditable tditemprice" centertext value="'.$price.'">'.$price.'</td>
												</tr>';
										}
									echo '</table>
								</div>';
						}
						elseif($row['typeofrequest']==7){
							$query2 = mysqlm_query("select themode,id from requestmode ".($row['typeofrequest']==7?" where fortype=7":" where fortype=0"));
							echo '<div col12 marginedtop>';
								while($row2=mysqlm_fetch_array($query2)){
									if($row['requestmode']==$row2[1]){
										echo '<div coli '.($row['typeofrequest']==7?"col6":"col4").' marginedbottom><div class="mycheckbox chkrequestmode" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
										$requesttypevaluelabel = '(<span color1>'.$row2[0].'</span>) ';
									}
									else{
										echo '<div coli '.($row['typeofrequest']==7?"col6":"col4").' marginedbottom><div class="mycheckbox chkrequestmode" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
									}
								}
							echo '</div>';
							echo '<div col12 marginedtop>';
								echo '<div fontbold> Details</div>';
								echo '<textarea class="requesttypevalue" resizenone bbox style="height:100px" col12>'.$row['requesttypevalue'].'</textarea>';
							echo '</div>';
						}
						else{
							echo '<textarea class="requesttypevalue" bbox resizenone col12 style="height:99%;">'.$row['requesttypevalue'].'</textarea>';
						}
						
				echo'</div>
				</div>';
		echo '</div>';
		echo '<div class="mygroup" nobordertop col12>';
			echo '<div buttongroup floatright col4>';
				echo '<button col6 onclick="btnmodesubmitclicked(\'edit\','.$requestid.')"><i class="fa fa-save"/> Save</button>';
				echo '<button col6 onclick="btncanceleditmodeclicked('.$requestid.')"><i class="fa fa-times"/> Cancel</button>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_POST['saveedit'])){//save edited
		include'db/dbconnect.php';
		$requestid=mySTRpost('requestid');
		$requestmodeid=mySTRpost('requestmodeid');
		$requesttypeid=mySTRpost('requesttypeid');
		$requesttypevalue=mySTRpost('requesttypevalue');
		$requestpurpose=mySTRpost('requestpurpose');
		$systemtype=mySTRpost('systemtype');
		mysqlm_query("update requests set requestmode=$requestmodeid,
											typeofrequest=$requesttypeid,
											requesttypevalue=$requesttypevalue,
											purpose=$requestpurpose,
											systemtype=$systemtype
											where id=".$requestid);
		breakhere($con);
	}
	elseif(isset($_GET['getmaxnumber'])){//get maximum requests count
		include 'db/dbconnect.php';
		$query = mysqlm_query("select max(requestnumber) from requests where thetype=1");
		echo mysqlm_fetch_array($query)[0];
		breakhere($con);
	}
	elseif(isset($_GET['getrequestgroup'])){//get maximum request group
		include 'db/dbconnect.php';
		$query = mysqlm_query("select IFNULL(max(requestgroup),0) from requests");
		echo mysqlm_fetch_array($query)[0];
		breakhere($con);
	}
	// elseif(isset($_GET['requestopen'])){//open table to view rfs approve status
		// include 'db/dbconnect.php';
		// $requestgroup = mySTRget('requestgroup');
		// $query=mysqlm_fetch_array(mysqlm_query("select typeofrequest from requests where requestgroup=$requestgroup"));
		// $buid=myGET('buid');
		// if($query[0]!=7){//RFS approve status
			// $query = mysqlm_query("select LPAD(requestnumber,4,0),`date`,themode,requesttype,
										// IFNULL(`status`,'Pending') as groupivstatus,
										// a.id,
										// if(IFNULL(a.executed,'Pending')='Pending','Pending','Approved') as executed,
										// if(IFNULL(a.buheadid,'Pending')='Pending','Pending','Approved') as buhead,
										// if(IFNULL(a.iadstatus,'Pending')='Pending','Pending','Approved') as iadstatus
										// from requests a,typeofrequest b,requestmode c 
										// where a.typeofrequest = b.id and a.requestmode = c.id and a.requestgroup = ".mySTRget('requestgroup')." order by a.requestnumber");
			// echo '<table mytable fullwidth>
					// <tr>
						// <th col1>Number</th>
						// <th>Date</th>
						// <th>Mode</th>
						// <th>Type</th>';
					// $butasks = getbutasksrfs($buid,1);
					// foreach($butasks as $butask){
						// echo '<th centertext>'.$butask[0].'</th>';
					// }
					// echo '<th col1 centertext>Action</th>
					// </tr>
				// ';
			// while($row=mysqlm_fetch_array($query)){
				// $approvedna=checkstatus(array($row['groupivstatus'],$row['executed'],$row['buhead']));
				// echo '<tr trhoverable>
						// <td centertext fontbold colorred>'.$row[0].'</td>
						// <td>'.$row[1].'</td>
						// <td>'.$row[2].'</td>
						// <td>'.$row[3].'</td>';
					// foreach($butasks as $butask){
						// echo'<td class="tdrequestsstatus" '.$row[$butask[1]].' centertext>'.$row[$butask[1]].'</td>';
					// }
					// echo '<td centertext>
							// <div iconbtn onclick="iconrequestmodeviewclicked(\''.$row[5].'\',\''.$approvedna.'\')" coli title="view"><i class="fa fa-list"/></div>
							// <!--'.($row[4]=="Approved"?'<div iconbtn coli marginedleft title="print"><i class="fa fa-print"/></div>':"").'-->
						// </td>
					// </tr>';
			// }
			// echo '</table>';
		// }
		// elseif($query[0]==7){
			// $query = mysqlm_query("select LPAD(requestnumber,4,0),`date`,themode,requesttype,
										// if(IFNULL(a.softsysstatus,'Pending')='Pending','Pending','Approved') as softsysstatus,a.id,
										// if(IFNULL(a.executed,'Pending')='Pending','Pending','Approved') as executed,
										// if(IFNULL(a.buheadid,'Pending')='Pending','Pending','Approved') as buhead
										// from requests a,typeofrequest b,requestmode c 
										// where a.typeofrequest = b.id and a.requestmode = c.id and a.requestgroup = ".mySTRget('requestgroup')." order by a.requestnumber");
			// echo '<table mytable fullwidth>
					// <tr>
					// <th col1>Number</th>
					// <th>Date</th>
					// <th>Mode</th>
					// <th>Type</th>
					// <th centertext col3>Corporate Audit Manager and Compliance Officer</th>
					// <th col2 centertext>Programmer/MIS</th>
					// <th col1 centertext>Action</th></tr>
			// ';
			// while($row=mysqlm_fetch_array($query)){
				// $approvedna=checkstatus(array($row['softsysstatus'],$row['executed']));
				// echo '<tr trhoverable>
						// <td centertext fontbold colorred>'.$row[0].'</td>
						// <td>'.$row[1].'</td>
						// <td>'.$row[2].'</td>
						// <td>'.$row[3].'</td>
						// <td class="tdrequestsstatus" '.$row['softsysstatus'].' centertext>'.$row['softsysstatus'].'</td>
						// <td class="tdrequestsstatus" '.$row['executed'].' centertext>'.$row['executed'].'</td>
						// <td centertext>
							// <div iconbtn onclick="iconrequestmodeviewclicked(\''.$row[5].'\',\''.$approvedna.'\')" coli title="view"><i class="fa fa-list"/></div>
							// <!--'.($row[4]=="Approved"?'<div iconbtn coli marginedleft title="print"><i class="fa fa-print"/></div>':"").'-->
						// </td>
					// </tr>';
			// }
			// echo '</table>';	
		// }
		// breakhere($con);	
	// }
	elseif(isset($_GET['requestmodeview'])){//view request RFS
		include 'db/dbconnect.php';
		$query = mysqlm_query("select LPAD(requestnumber,4,0) as requestnumber,date,typeofrequest,requestmode,requesttypevalue,purpose,id from requests where id =".mySTRget('requestid'));
		$row = mysqlm_fetch_array($query);
		$requesttypevaluelabel="";
		echo '<div class="mygroup" col12>
				<div col2 coli colctrl>Company Name</div><input class="txtcompanyname" readonly type="text" value="'.getcompanyname($row['id']).'" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Business Unit</div><input class="txtbusinessunit" readonly type="text" value="'.getbuname($row['id']).'" col3/>
				<div col2 coli colctrl>Control Number</div><input class="txtcontactno" readonly type="text" colorred value="'.$row['requestnumber'].'" col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Date</div><input class="txtdate" readonly type="text" value="'.$row['date'].'"  col3/>
				<div col2 coli colctrl>Address</div><input class="txtaddress" readonly type="text" value="'.getaddress($row['id']).'"  col3/>
				<div col1 coli colctrl></div>
				<div col2 coli colctrl>Requested by</div><input class="txtaddress" readonly type="text" value="'.getuserfullname().'" col3/>';
		echo '</div>';
		echo '<div class="mygroup" col12 nopadding noborder>';
			echo '<div class="mygroup" nobordertop col6 displaytablecell nopadding>';
				echo '<div col12 marginedbottom paddingedall bbox>';
					echo '<div col4 coli>
							<div colctrl fontbold>Request mode</div>';
							$query2 = mysqlm_fetch_array(mysqlm_query("select themode,id from requestmode ".($row['typeofrequest']==7?" where fortype=7":" where fortype=0")." and id=".$row['requestmode']));
							echo '<div coli  marginedbottom><div class="mycheckbox chkrequestmode" value="checked" thevalueid="'.$query2[1].'" thevalue="'.$query2[0].'" coli></div><div marginedleft coli>'.$query2[0].'</div></div>';
							$requesttypevaluelabel = '(<span color1>'.$query2[0].'</span>) ';
					echo '</div>';
					echo '<div col8 coli>';
							echo '<div colctrl fontbold>Request type</div>';
							$query2 = mysqlm_query("select a.requesttype,a.id from typeofrequest a,userrequesttyperole b where a.id = b.requesttypeid and b.userid=".getuserid()." and a.id=".$row['typeofrequest']);
							$row2 = mysqlm_fetch_array($query2);

							echo '<div coli col6 marginedbottom><div class="mycheckbox chkrequesttype" value="checked" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
							$requesttypevaluelabel = $requesttypevaluelabel.$row2[0];
					echo '</div>';
				echo '</div>';

				echo '<div col12 marginedtop borderedall noborder hasbordertop paddingedall bbox>
						<div fontbold>Purpose</div>
						<textarea resizenone readonly bbox style="height:100px" marginedtop col12>'.$row['purpose'].'</textarea>';
				echo '</div>';

			echo '</div>';
			echo '<div mygroup displaytablecell col6 nopadding noborderleft nobordertop>';
				echo '<div mygroup col12 noborder hasborderbottom>
						<div fontbold>'.$requesttypevaluelabel.'</div>
						<textarea resizenone readonly bbox style="height:200px" marginedtop col12>'.str_replace("="," = ",$row['requesttypevalue']).'</textarea>';
				echo '</div>';
				
				echo '<div mygroup col12 noborder>
						<div buttongroup col9 floatright>
							<button col4 onclick="editrequestclicked(\''.myGET('requestid').'\',this)"><i class="fa fa-pencil"/> Edit</button>
							<button col4 onclick="deleterequestclicked(\''.myGET('requestid').'\')"><i class="fa fa-trash"/> Delete</button>
							<button col4 onclick="printrequestclicked(\''.myGET('requestid').'\',\''.$row['requestnumber'].'\')"><i class="fa fa-print"/> Print</button>
						</div>
					</div>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['printthisform'])){//print RFS form
		include 'db/dbconnect.php';
		$users = initusers();
		showquery();
		$row = mysqlm_fetch_array(mysqlm_query("select LPAD(a.requestnumber,4,0) as requestnumber,
													   c.companyname as companyname,
													   b.businessunit as businessunit,
													   b.address as address,
													   b.contactnumber as contactno,
													   a.date as `date`,
													   a.requesttypevalue as details,
													   concat(u.firstname,' ',u.lastname) as requestedby,
													   a.buheadid as buhead,
													   a.approvedid as groupivstatus,
													   a.executed as executed,
													   a.iadstatus as iadstatus,
													   a.id as idnumber,
													   d.requesttype as typeofrequest,
													   e.themode as therequestmode,
													   a.purpose as purpose,
													   a.typeofrequest as typeofrequestid,
													   a.softsysstatus as softsysstatus,
													   a.remarks as remarks,
													   a.systemtype as systemtype
												from requests a,tblbusinessunit b,users u,tblcompany c,typeofrequest d,requestmode e
												where d.id=a.typeofrequest and e.id = a.requestmode and a.userid = u.id and b.id = u.businessunitid and c.id = b.companyid and a.id=".myGET('requestid')));
		//echo mySTR($row['details']);
		$txtfile = "printing\\tempxlsx\\".$row['idnumber'].".txt";
		file_put_contents($txtfile,str_replace(array("\n","\r"),",      ",$row['details']));
		//breakhere($con);
	//	echo $row['typeofrequestid'];
		if($row['typeofrequestid'] == 7){
			$buhead = mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['softsysstatus']))[0];
			$executed = mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['executed']))[0];
			$systemtype = mysqlm_fetch_array(mysqlm_query("select systemtype from systemtype where id=".$row['systemtype']))[0];
			echo python("python.exe",
				mySTR("RFSsoftsys")." ".
				mySTR($row['requestnumber'])." ".
				mySTR($row['companyname'])." ".
				mySTR($row['businessunit'])." ".
				mySTR($row['address'])." ".
				mySTR($row['contactno'])." ".
				mySTR($row['date'])." ".
				mySTR($txtfile)." ".
				mySTR(strtoupper($row['requestedby']))." ".
				mySTR(strtoupper($buhead))." ".
				mySTR(strtoupper($executed))." ".
				mySTR($row['idnumber'])." ".
				mySTR($row['therequestmode'])." ".
				mySTR($row['typeofrequest'])." ".
				mySTR($row['purpose'])." ".
				mySTR($systemtype)." ".
				mySTR($row['remarks']));
		}
		else{
			//echo $row['approvedby'];
			$buhead = ($row['buhead']!=''?$users[$row['buhead']]:'');//mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['buhead']))[0];
			$groupivstatus = ($row['groupivstatus']!=''?$users[$row['groupivstatus']]:'');//mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['groupivstatus']))[0];
			$executed = ($row['executed']!=''?$users[$row['executed']]:'');// mysqlm_fetch_array(mysqlm_query("select concat(firstname,' ',lastname) from users where id=".$row['executed']))[0];
			echo python("python.exe",mySTR("RFS").
				" ".mySTR($row['requestnumber']).
				" ".mySTR($row['companyname']).
				" ".mySTR($row['businessunit']).
				" ".mySTR($row['address']).
				" ".mySTR($row['contactno']).
				" ".mySTR($row['date']).
				" ".mySTR($txtfile).
				" ".mySTR(strtoupper($row['requestedby'])).
				" ".mySTR(strtoupper($buhead)).
				" ".mySTR(strtoupper($groupivstatus)).
				" ".mySTR(strtoupper($executed)).
				" ".mySTR($row['idnumber']).
				" ".mySTR($row['therequestmode']).
				" ".mySTR($row['typeofrequest']).
				" ".mySTR($row['purpose']));
		}
		breakhere($con);
	}
	elseif(isset($_GET['getmodes'])){//get modes
		include 'db/dbconnect.php';
		$query2 = mysqlm_query("select themode,id from requestmode where fortype=0");
		echo '<div colctrl fontbold>Request mode</div>';
		while($row2 = mysqlm_fetch_array($query2)){
			echo '<div coli col4 marginedbottom><div class="mycheckbox chkrequestmode" thevalueid="'.$row2[1].'" thevalue="'.$row2[0].'" coli></div><div marginedleft coli>'.$row2[0].'</div></div>';
		}
		breakhere($con);
	}
	elseif(isset($_GET['softwaresystem'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("select systemtype,id from systemtype");
		echo '<div fontbold colctrl> Type of System</div>';
		while($row=mysqlm_fetch_array($query)){
			echo '<div coli col4><div class="mycheckbox chksystemtype" thevalueid="'.$row[1].'" thevalue="'.$row[0].'" coli></div><div coli marginedleft>'.$row['systemtype'].'</div></div>';
		}
		breakhere($con);
	}
	elseif(isset($_GET['softwaresystem2'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("select themode,id from requestmode where fortype=7");
		echo '<div col12>';
			while($row=mysqlm_fetch_array($query)){
				echo '<div coli col6 marginedbottom><div class="mycheckbox chkrequestmode" thevalueid="'.$row[1].'" thevalue="'.$row[0].'" coli></div><div coli marginedleft>'.$row['themode'].'</div></div>';
			}
		echo '</div>';
		echo '<div col12 marginedtop>';
			echo '<div fontbold> Details</div>';
			echo '<textarea class="requesttypevalue" resizenone bbox style="height:100px" col12></textarea>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['deleterequest'])){
		include 'db/dbconnect.php';
		mysqlm_query("delete from requests where id = ".myGET('requestid'));
		breakhere($con);
	}
	elseif(isset($_GET['attachfiles'])){
		include 'db/dbconnect.php';
		//echo '';
		echo '<div mygroup col12>
				<div scrollable style="height:344px">
					<table class="tablefiles" mytable col12>
						<tr>
							<th col7>File name</th>
							<th col2>File size</th>
							<th col2>File type</th>
							<th col2 centertext>Action</th>
						</tr>
						<tbody class="tablefilesdata">
						</tbody>
					</table>
				</div>
			</div>';
		echo '<div mygroup col12 nobordertop>
				<button onclick="btnaddmodeuploadfilesclicked()"> <i class="fa fa-plus"/> Add Files</button>
			</div>';
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
				echo '<span color1 class="currentbu">'.getcurrentbu().' </span><span class="editcurrentbu" onclick="editcurrentbuclicked(\'RFS\')" title="Change Current BU">&nbsp;<i class="fa fa-pencil"/></span>';
			}
		?>
	</h3>
	<div coli col12>
		<div mytoolsgroup col6 displaytablecell nopadding>
			<div mytoolsgroup displaytablecell noborder col3>
				<button onclick="addnewrequestclicked()" col12><i class="fa fa-plus"/> Add New Request</button>
			</div>
			<div mytoolsgroup displaytablecell noborder hasborderleft col5>
				<div coli marginedright fontbold>Filter Date</div>
				<button class="monthselector filterrequestmonth" datevalue=""><i class="fa fa-filter"/> This month</button>
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
	$(".mycheckbox").mycheckbox();
	var currentpage=0;
	$.get("execute.php?getmaxrecords",function(result){
		$(".mypagination").mypagination(result,function(selectedpage){
			currentpage=selectedpage;
			loadtable();
		});
	});
	$(".dateselector").dateselector(function(){
		loadtable();
	});
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
		$.get("request.php?loadtable&date="+reqdate+"&showapproved="+$(".chkshowapproved").mycheckboxvalue()+
							"&filternumber="+$(".filternumber").val()+
							"&currentpage="+currentpage,function(result){
			$(".requeststable").html(result);
		});
	}
	var modalformrequest = new MyModal();
	var currentmaxnumber = 0;
	var currentrequestgroup = 0;
	var iconrequestopenmodal = new MyModal();
	function iconrequestopenclicked(requestgroup,buid){
		selectedbuid=buid;
		iconrequestopenmodal.showcustom("Requests","70%");
		$.get("requests.php?requestopen&requestgroup="+requestgroup+"&buid="+selectedbuid,function(result){
			//showMyModalSmall("Requests",result);
			iconrequestopenmodal.settag(requestgroup);
			iconrequestopenmodal.body(result);
		});
	}
	var requestmodeviewmodal=new MyModal();
	function iconrequestmodeviewclicked(id,checkstatus){
		requestmodeviewmodal.showcustom("RFS","60%");
		$.get("request.php?requestmodeview&requestid="+id,function(result){
			requestmodeviewmodal.body(result);
			requestmodeviewmodal.settag(checkstatus);
			$(".mycheckbox").mycheckbox();
		})
	}
	function btncanceleditmodeclicked(requestid){
		requestmodeviewmodal.showloading();
		$.get("request.php?requestmodeview&requestid="+requestid,function(result2){
				requestmodeviewmodal.body(result2);
				requestmodeviewmodal.title("RFS");
				$(".mycheckbox").mycheckbox();
		});
	}
	function btnmodesubmitclicked(whatmode,requestid){
		var requestmode=$(".chkrequestmode[value=checked]").attr("thevalue");
		var requestmodeid=$(".chkrequestmode[value=checked]").attr("thevalueid");
		var requesttype=$(".chkrequesttype[value=checked]").attr("thevalue");
		var requesttypeid=$(".chkrequesttype[value=checked]").attr("thevalueid");
		var systemtype=$(".chksystemtype[value=checked]").attr("thevalueid");
		var requesttypevalue = $(".requesttypevalue").val();
		(requesttypevalue==undefined?requesttypevalue="":"");
		var requestpurpose = $(".txtpurpose").val();
		$(".tblpricechangelist").find("tr").each(function(){
			if($(this).find(".tditemname").attr("value") != undefined && ($(this).find(".tditemname").attr("value")!="" || $(this).find(".tditemprice").attr("value")!="")){
				requesttypevalue = (requesttypevalue + ($(this).find(".tditemname").attr("value") + "=" + $(this).find(".tditemprice").attr("value"))+"\n");
			}
		});
		if(requestmodeid==undefined){
			showMyAlertError("Error!","Please select mode.");
			return;
		}
		if(requesttypeid==undefined){
			showMyAlertError("Error!","Please select type of request.");
			return;
		}
		if(requesttypeid==7){
			if(systemtype==undefined){
				showMyAlertError("Error!","Please select system type.");
				return;
			}
		}
		if(whatmode=="add"){
			$(".chkrequestmode").mycheckboxset("unchecked");
			$(".chkrequesttype").mycheckboxset("unchecked");
			$(".txtpurpose").val("");
			$(".requesttypevalue").val("");

			currentmaxnumber=currentmaxnumber+1;
			$(".requestmodes").append('<tr class="therequests" trhoverable>'+
										'<td class="tdrequestnumber" displaynone currentmaxnumber="'+currentmaxnumber+'">'+pad(currentmaxnumber,4)+'</td>'+
										'<td class="tdrequestmode" displaynone requestmodeid="'+requestmodeid+'">'+requestmode+'</td>'+
										'<td class="tdrequesttype" requesttypeid="'+requesttypeid+'">'+requesttype+'</td>'+
										'<td class="tdrequesttypevalue" displaynone>'+requesttypevalue+'</td>'+
										'<td class="tdrequestpurpose" displaynone>'+requestpurpose+'</td>'+
										'<td class="tdsystemtype" displaynone>'+systemtype+'</td>'+
										'<td></td>'+
									'</tr>');
			showMyAlert("Mode added!","");	
		}
		else{
			requestmodeviewmodal.showloading();
			iconrequestopenmodal.showloading();
			if(requesttypeid!=7){
				systemtype="";
			}
			$.post("request.php",{
				saveedit:emptyval,
				requestid:requestid,
				requestmodeid:requestmodeid,
				requesttypeid:requesttypeid,
				requesttypevalue:requesttypevalue,
				systemtype:systemtype,
				requestpurpose:requestpurpose
			},function(result){
				var requestgroup=iconrequestopenmodal.gettag();
			//	console.log(result);
				showMyAlert("Form Request saved!","");
				//requestmodeviewmodal.close();
				$.get("request.php?requestmodeview&requestid="+requestid,function(result2){
					requestmodeviewmodal.body(result2);
					requestmodeviewmodal.title("RFS");
					$(".mycheckbox").mycheckbox();
					$.get("requests.php?requestopen&requestgroup="+requestgroup+"&buid="+selectedbuid,function(result3){
						iconrequestopenmodal.settag(requestgroup);
						iconrequestopenmodal.body(result3);
					});
				});
			});
		}
		//mymodalclose(modaladdnewmode);
		modaladdnewmode.close();
	}
	var modaladdnewmode = new MyModal();
	function themode(strwhat){
		var prevdivreqmode = $(".divrequestmode").html();
		$(".mycheckbox").mycheckbox();
		var requestmode;
		var requesttype;
		$(".chkrequestmode").checkboxradio();
		$(".chkrequesttype").checkboxradio();
		$(".chkrequestmode").mycheckboxonclick(function(){
			requestmode = $(this).attr("thevalue");
		});
		var requesttypeid;
		var thecolumns;
		$(".chkrequesttype").mycheckboxonclick(function(){
			requesttype = $(this).attr("thevalue");
			requesttypeid = $(this).attr("thevalueid");
			thecolumns=$(this).attr("thecolumns");
			refreshthetwo();
		});
		requestmode==undefined?requestmode="":requestmode=requestmode;
		requesttype==undefined?requesttype="":requesttype=requesttype; 
		var refreshthetwo = function(){
			if(requesttypeid!=7){
				$.get("request.php?getmodes",function(result){
					$(".divrequestmode").html(result);
					$(".chkrequestmode").checkboxradio();
					$(".chkrequestmode").mycheckboxonclick(function(){
						requestmode = $(this).attr("thevalue");
					});
				});
				if(thecolumns.length>0){
					var thisstr;
					thisstr = '<div col12>\
									<table class="tblpricechangelist" mytable col12><tr>';

										thecolumns = thecolumns.split(",");
										for(b in thecolumns){
											thisstr += '<th col6>'+thecolumns[b]+'</th>';
										}
										thisstr += '</tr>';
										for(a=0;a<50;a++){
											thisstr += '<tr><td class="tdeditable tditemname" centertext value=""></td><td class="tdeditable tditemprice" centertext value=""></td></tr>';
										}
									thisstr += '</table>\
								</div>';
					$(".requesttypediv").html(thisstr);
					$(".therequesttype").html(requesttype + ' Lists');
					$(".tdeditable").tdeditable();
				}
				else{

					$(".requesttypediv").html('<textarea class="requesttypevalue" bbox resizenone col12 style="height:99%;"></textarea>');
					$(".therequesttype").html(requesttype + ' Details');	
				}
			}
			else{
				$.get("request.php?softwaresystem",function(result2){
					$(".divrequestmode").html(result2);
					$(".mycheckbox").mycheckbox();
					$(".chksystemtype").checkboxradio();
				});
				$.get("request.php?softwaresystem2",function(result2){
					$(".requesttypediv").html(result2);
					$(".mycheckbox").mycheckbox();
					$(".chkrequestmode").checkboxradio();
				});
				$(".therequesttype").html(requesttype);
			}
			
		}
		if(strwhat="edit"){
			$(".tdeditable").tdeditable();
			requesttypeid = $(".chkrequesttype").myradiogetchecked();
			requesttype = $(".chkrequesttype").myradiogetcheckedvalue();
			requestmode = $(".chkrequestmode").myradiogetchecked();
			$(".chksystemtype").checkboxradio();
			//refreshthetwo();
		}
	}
	function btnaddmodeclicked(){
		modaladdnewmode.showcustom("Add mode",'60%');
		$.get("request.php?addnewmode",function(result){
			modaladdnewmode.body(result);
			themode("add");
		});
	}
	function addnewrequestclicked(){
		filesformdata=[];
		$.get("request.php?getmaxnumber",function(result){
			currentmaxnumber = parseInt(result);
		});
		$.get("request.php?getrequestgroup",function(result){
			currentrequestgroup = parseInt(result)+1;
		});
		modalformrequest.showcustom("RFS Form Request","60%");
		$.get("request.php?addnewrequest",function(result){
			//modalformrequest = showMyModalCustom("Form Request",result,"50%");
			modalformrequest.body(result);
			//refreshtab(".tabrequestmode");
			$(".mycheckbox").mycheckbox();
			$(".chkrequesttype1").checkboxradio();
			$(".chkrequesttype2").checkboxradio();
			$(".chkrequesttype3").checkboxradio();
			$(".txtdate").datepicker();
		});
	}
	function btnaddnewrequestsubmit(){
		if($(".therequests").length==0){
			showMyAlertError("Form Request Submission Failed!","There are no requests.");
			return;
		}
		if($(".txtdate").val()==""){
			showMyAlertError("Form Request Submission Failed!","Please select date.");
			return;
		}
		var companyname = [];
		var businessunit = [];
		var contactno = [];
		var tdate = [];
		var address = [];
		//var requestnumber = $(this).find(".tdrequestnumber").attr("currentmaxnumber");
		var requestmode = [];
		var typeofrequest = [];
		var requesttypevalue = [];
		var purpose = [];
		var requestgroup = currentrequestgroup;
		var toexecute = [];
		var systemtype = [];
		$(".therequests").each(function(){
			
			companyname.push($(".txtcompanyname").val()+"<separator>");
			businessunit.push($(".txtbusinessunit").val()+"<separator>");
			contactno.push($(".txtcontactno").val()+"<separator>");
			tdate.push($(".txtdate").val()+"<separator>");
			address.push($(".txtaddress").val()+"<separator>");
			toexecute.push($(".cbotoexecute").val()+"<separator>");
			//var requestnumber = $(this).find(".tdrequestnumber").attr("currentmaxnumber");
			
			requestmode.push($(this).find(".tdrequestmode").attr("requestmodeid")+"<separator>");
			typeofrequest.push($(this).find(".tdrequesttype").attr("requesttypeid")+"<separator>");
			systemtype.push($(this).find(".tdsystemtype").html()+"<separator>");
			requesttypevalue.push($(this).find(".tdrequesttypevalue").html()+"<separator>");
			purpose.push($(this).find(".tdrequestpurpose").html()+"<separator>");
			requestgroup = currentrequestgroup;
		});
		modalformrequest.showloading();
		var data = new FormData();
		data.append('submitrequest',emptyval+",");
		data.append('companyname',companyname+",");
		data.append('businessunit',businessunit+",");
		data.append('contactno',contactno+",");
		data.append('tdate',tdate+",");
		data.append('address',address+",");
		data.append('requestmode',requestmode+",");
		data.append('purpose',purpose+",");
		data.append('typeofrequest',typeofrequest+",");
		data.append('requesttypevalue',requesttypevalue+",");
		data.append('toexecute',toexecute+",");
		data.append('systemtype',systemtype+",");
		
		for(var a=0;a<filesformdata.length;a++){
			data.append('filesformdata[]',filesformdata[a]);
		}
		$.ajax({url:"request.php",
				dataType:'text',
				contentType:false,
				processData:false,
				type:'post',
				data:data,
				success:function(result){
				//	echo(result);
				//	mymodalclose(modalformrequest);
					console.log(result);
					modalformrequest.close();
					showMyAlert("New Form Request Submitted!","");
					loadtable();
				}
		});
	}
	function printrequestclicked(requestid,requestnumber){
	//	var mywindow = window.open('printrequest.php', 'PRINT', 'height=400,width=600');
		if(requestmodeviewmodal.gettag()!='tananApproved'){
			showMyAlertError("Cannot print, Form Request still pending.",'');
			return;
		}
		showprint("RFS",requestid,requestnumber);
	//	mymodal.body('<iframe src="printrequest.php" col12 noborder style="height:70vh"></iframe>')
		//PrintElem($(el).parentsUntil("",".mymodalbody"));
	}
	function editrequestclicked(requestid,thisel){
		//alert(requestmodeviewmodal.gettag());
		if(requestmodeviewmodal.gettag()!='tananPending'){
			showMyAlertError("Cannot edit, Form Request is already approved.",'');
			return;
		}
		//$(thisel).html("Save");
		requestmodeviewmodal.title("RFS <span color1>(Edit)</span>");
		requestmodeviewmodal.showloading();
		$.get("request.php?editmode&requestid="+requestid,function(result){
			requestmodeviewmodal.body(result);
			themode("edit");
		});
	}
	function deleterequestclicked(requestid){
		if(requestmodeviewmodal.gettag()=='trolls' || requestmodeviewmodal.gettag()=='tananApproved'){
			showMyAlertError("Cannot delete, Form Request is already approved.",'');
			return;
		}
		showMyConfirm("Delete request?",function(){
			$.get("request.php?deleterequest&requestid="+requestid,function(result){
				showMyAlert("Request successfully deleted!","");
				loadtable();
				iconrequestopenmodal.close();
				// $.get("request.php?requestopen&requestgroup="+iconrequestopenmodal.gettag(),function(result2){
					// iconrequestopenmodal.body(result2);
				// });
				requestmodeviewmodal.close();
			});
		},function(){});
	}
	var modalattachfiles = new MyModal();
	function btnaddmodeattachfiles(){
		modalattachfiles.showcustom("Attach Files","60%");
		$.get("request.php?attachfiles",function(result){
			modalattachfiles.body(result);
			refreshfileslist();
		});
	}
	function refreshfileslist(){
		$(".tablefilesdata").html("");
		//console.log(filesformdata);
		for(var f in filesformdata){
			$(".tablefilesdata").append('<tr trhoverable>\
										<td>'+filesformdata[f].name+'</td>\
										<td>'+parseInt(parseFloat(filesformdata[f].size)/1024)+'KB</td>\
										<td>'+filesformdata[f].type+'</td>\
										<td centertext>\
											<div iconbtn onclick="" title="Remove file">\
												<i class="fa fa-trash"/>\
											</div>\
										</td>\
									</tr>');
		}
	}
	var filesformdata = [];
	function btnaddmodeuploadfilesclicked(){
		$(".flattachfiles").remove();
		$("body").append('<input class="flattachfiles" name="filesformdata[]" displaynone type="file" multiple/>');
		$(".flattachfiles").trigger('click');
		var fdatacount = filesformdata.length;
		// for(var f of filesformdata.entries()){
			// fdatacount++;
		// }
		$(".flattachfiles").on('change',function(event){

			for(var a=0;a<event.target.files.length;a++){
				filesformdata.push(event.target.files[a]);
			}
			refreshfileslist();
		});
	}
</script>