<?php
	if(isset($_GET['loaduserstable'])){//load main table
		include 'db/dbconnect.php';
		//$searchuser = myGET('searchuser');
		$query = mysqlm_query("select a.id as userid,
								concat(firstname,' ',lastname) as fullname,
								username,
								b.usertype,
								b.id as typeid 
								from users a,usertype b 
								where a.usertype = b.id and a.usergroupid = ".getcurrentusergroupid()." order by a.id desc");
									//$searchuser."%' and a.lastname like '%".$searchuser."%') and a.usertype = b.id order by fullname");
		echo '<table mytable fullwidth class="tblusers">';
			echo '<tr>
					<th col2>Uesr id</th>
					<th>Fullname</th>
					<th>Username</th>
					<th>Main Task</th>
					<th col1 centertext>Action</th>
				</tr>';
			while($row=mysqlm_fetch_array($query)){
				echo '<tr trhoverable>
						<td>'.$row[0].'</td>
						<td>'.ucwords(strtolower($row[1])).'</td>
						<td>'.strtolower($row[2]).'</td>
						<td>'.$row[3].'</td>
						<td>
							<div iconbtn paddingedleft onclick="viewuseraccount('.$row['userid'].')" title="View Account"><i class="fa fa-list"/></div>';
							$utype=$row['typeid'];
							//if($utype==2||$utype==3||$utype==4||$utype==5){
							if($utype != 0){
								echo'<div iconbtn marginedleft onclick="viewburole('.$row['userid'].')" title="View User role"><i class="fa fa-cog"/></div>';
							}
					echo '</td>
					</tr>';
			}
		echo '</table>';
		breakhere($con);
	}
	elseif(isset($_GET['addnewuser'])){//add new user
		include 'db/dbconnect.php';
		$query = mysqlm_query("select usertype,id from usertype");
		echo'<div mygroup col12>
				<div fontbold marginedbottom>User Account</div>
				<div col2 coli colctrl marginedbottom>First name</div><input class="txtfirstname" title="Firstname" type="text" col3/>
				<div col1 coli></div>
				<div col2 coli colctrl>Last name</div><input class="txtlastname" title="Last name" type="text" col3/>
				<div col2 coli colctrl marginedtop>Main Task</div>';
			echo'<select class="cbousertype" colctrl col3 marginedbottom>';
				while($row=mysqlm_fetch_array($query)){
					echo '<option value="'.$row[1].'">'.$row[0].'</option>';
				}
			echo'</select>';
			echo'<div col7 coli></div>
				<div col2 coli colctrl>Username</div><input class="txtusername" title="Username" type="text" col3/>
				<div col1 coli></div>
				<div col2 coli colctrl marginedbottom>Password</div><input class="txtpassword" title="Password" type="password" col3/>
			</div>';
		$query2 = mysqlm_query("select companyname,id from tblcompany");
		$query3 = mysqlm_query("select businessunit,companyid,id from tblbusinessunit");
		echo'<div class="userdetails" mygroup col12 nobordertop displaynone>
				<div fontbold marginedbottom>User Details</div>
				<div col2 coli colctrl marginedbottom>Company name</div>';
				echo '<select class="cbocompanyname" colctrl col3 marginedbottom>';
				while($row2=mysqlm_fetch_array($query2)){
					echo '<option value="'.$row2[1].'">'.$row2[0].'</option>';
				}
				echo '</select>';
			echo'<div col1 coli></div>
				<div col2 coli colctrl>Business unit</div>';
				echo '<select class="cbobusinessunit" colctrl col3 >';
				while($row3=mysqlm_fetch_array($query3)){
					echo '<option value="'.$row3[2].'">'.$row3[0].'</option>';
				}
				echo '</select>';
			echo'<div col2 coli colctrl>Contact number</div><input readonly class="txtcontactnumber" type="text" col3/>
				<div col1 coli></div>
				<div col2 coli colctrl marginedbottom>Address</div><input readonly class="txtaddress" type="text" col3/>
			</div>';
		echo'<div mygroup col12 nobordertop>
				<button col2 floatright marginedbottom marginedtop onclick="createaccountclicked()">Create Account</button>
			</div>';
		breakhere($con);
	}
	elseif(isset($_POST['createaccount'])){//create account save
		include 'db/dbconnect.php';
		$firstname=mySTRpost('firstname');
		$lastname=mySTRpost('lastname');
		$usertype=mySTRpost('usertype');
		$username=mySTRpost('username');
		$password=mySTRpost('password');
		$companyname=mySTRpost('companyname');
		$businessunit=mySTRpost('businessunit');
		$contactnumber=mySTRpost('contactnumber');
		$address=mySTRpost('address');
		$usergroup=getcurrentusergroupid();
		
		$checkusername = mysqlm_query("select id from users where username = ".$username);
		if(mysqlm_rowcount($checkusername)>0){
			echo 'exists';
		}
		else{
			$query = mysqlm_query("insert into users(firstname,lastname,usertype,username,password,businessunitid,usergroupid)
								         values($firstname,$lastname,$usertype,$username,$password,$businessunit,$usergroup)");
		}
		breakhere($con);
	}
	elseif(isset($_GET['getcontactnumber'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("select contactnumber from businessunit where id=".getuserid());
		echo mysqlm_fetch_array($query)[0];
		breakhere($con);
	}
	elseif(isset($_GET['updatebuactive'])){
		include 'db/dbconnect.php';
		$thevalue = myGET('thevalue');
		$theid = myGET('id');
		mysqlm_query("update tblbusinessunit set active=".($thevalue=="checked"?1:0)." where id=".$theid);
		breakhere($con);
	}
	elseif(isset($_GET['updatebucontacnumber'])){
		include 'db/dbconnect.php';
		$thevalue = myGET('value');
		$theid = myGET('id');
		mysqlm_query("update tblbusinessunit set contactnumber=".strdouble($thevalue)." where id=".$theid);
		breakhere($con);
	}
	elseif(isset($_GET['updatebuaddress'])){
		include 'db/dbconnect.php';
		$thevalue = myGET('value');
		$theid = myGET('id');
		mysqlm_query("update tblbusinessunit set address=".strdouble($thevalue)." where id=".$theid);
		breakhere($con);
	}
	elseif(isset($_GET['updatebuaddress'])){
		include 'db/dbconnect.php';
		$thevalue = myGET('value');
		$theid = myGET('id');
		mysqlm_query("update tblbusinessunit set address=".strdouble($thevalue)." where id=".$theid);
		breakhere($con);
	}
	elseif(isset($_GET['updatebuname'])){
		include 'db/dbconnect.php';
		$thevalue = myGET('value');
		$theid = myGET('id');
		mysqlm_query("update tblbusinessunit set businessunit=".strdouble($thevalue)." where id=".$theid);
		breakhere($con);
	}
	elseif(isset($_GET['viewuser'])){//view user details
		include 'db/dbconnect.php';
		$userid = myGET('userid');
		$query = mysqlm_query("select username,password,firstname,lastname,usertype,businessunitid,id from users where id=".$userid);
		$row=mysqlm_fetch_array($query);
		echo'<div mygroup col12>
				<div fontbold marginedbottom>User Account</div>
				<div col2 coli colctrl marginedbottom >First name</div><input class="txtfirstname" title="Firstname" value="'.ucfirst($row['firstname']).'" type="text" col3/>
				<div col1 coli></div>
				<div col2 coli colctrl>Last name</div><input class="txtlastname" title="Last name" value="'.ucfirst($row['lastname']).'" type="text" col3/>
				<div col2 coli colctrl marginedtop>Main Task</div>';
			$query = mysqlm_query("select usertype,id from usertype");
			echo'<select class="cbousertype" colctrl col3 marginedbottom>';
				while($row2=mysqlm_fetch_array($query)){
					if($row['usertype']==$row2['id']){
						echo '<option selected value="'.$row2[1].'">'.$row2[0].'</option>';
					}
					else{
						echo '<option value="'.$row2[1].'">'.$row2[0].'</option>';
					}
				}
			echo'</select>';
			echo'<div col7 coli></div>
				<div col2 coli colctrl>Username</div><input class="txtusername" title="Username" value="'.$row['username'].'" type="text" col3/>
				<div col1 coli></div>
				<div col2 coli colctrl marginedbottom>Password</div><input class="txtpassword" value="'.$row['password'].'" title="Password" type="password" col3/>
			</div>';
		$query2 = mysqlm_query("select companyname,id from tblcompany");
		$row4 = mysqlm_fetch_array(mysqlm_query("select companyid,id from tblbusinessunit where id=".$row['id']));
		$query3 = mysqlm_query("select businessunit,companyid,id from tblbusinessunit");
		echo'<div class="userdetails" mygroup col12 nobordertop>
				<div fontbold marginedbottom>User Details</div>
				<div col2 coli colctrl marginedbottom>Company name</div>';
				echo '<select class="cbocompanyname" colctrl col3 marginedbottom>';
				while($row2=mysqlm_fetch_array($query2)){
					if($row4['companyid']==$row2['id']){
						echo '<option selected value="'.$row2[1].'">'.$row2[0].'</option>';
					}
					else{
						echo '<option value="'.$row2[1].'">'.$row2[0].'</option>';
					}
					
				}
				echo '</select>';
			echo'<div col1 coli></div>
				<div col2 coli colctrl>Business unit</div>';
				echo '<select class="cbobusinessunit" colctrl col3 >';
				while($row3=mysqlm_fetch_array($query3)){
					if($row['businessunitid']==$row3['id']){
						echo '<option selected value="'.$row3[2].'">'.$row3[0].'</option>';
					}
					else{
						echo '<option value="'.$row3[2].'">'.$row3[0].'</option>';
					}
					
				}
				echo '</select>';
			echo'<div col2 coli colctrl>Contact number</div><input readonly class="txtcontactnumber" type="text" col3/>
				<div col1 coli></div>
				<div col2 coli colctrl marginedbottom>Address</div><input readonly class="txtaddress" type="text" col3/>
			</div>';
		echo'<div mygroup col12 nobordertop>
				<div buttongroup col6 marginedbottom marginedtop floatright>
					<button col4 onclick="usersettingssavechanges(this,'.strsing($userid).')">Save Changes</button>
					<button col4 onclick="usersettingsdelteaccount(this,'.strsing($userid).')">Delete Account</button>
					<button col4 onclick="usersettingscancel()">Cancel</button>
				</div>
			</div>';
		breakhere($con);
	}
	elseif(isset($_POST['updateusersettings'])){
		include 'db/dbconnect.php';
		$userid = myPOST('userid');
		$firstname=mySTRpost('firstname');
		$lastname=mySTRpost('lastname');
		$usertype=mySTRpost('usertype');
		$username=mySTRpost('username');
		$password=mySTRpost('password');
		$companyname=mySTRpost('companyname');
		$businessunit=mySTRpost('businessunit');
		
		$checkusername = mysqlm_query("select id from users where username = ".$username. " and id !=$userid");
		if(mysqlm_rowcount($checkusername)>0){
			echo 'exists';
		}
		else{
			//$query = mysqlm_query("insert into users(firstname,lastname,usertype,username,password,businessunitid)
								        // values($firstname,$lastname,$usertype,$username,$password,$businessunit)");
			$query = mysqlm_query("update users set firstname=$firstname,lastname=$lastname,usertype=$usertype,username=$username,password=$password,businessunitid=$businessunit
									where id=$userid");
		}
		breakhere($con);
	}
	elseif(isset($_GET['deleteuser'])){
		include 'db/dbconnect.php';
		$userid = myGET('userid');
		$query = mysqlm_query("delete from users where id=".$userid);
		breakhere($con);
	}
	elseif(isset($_GET['viewuserrole'])){//view user role modal
		include 'db/dbconnect.php';
		$userid = myGET('userid');
		$roles = initroles();
		$db = new Mysqlm();
		$db->connect();
		$query = $db->mysqlm_fetch_array($db->mquery("select usertype from users where id=".$userid));
		$tbname=$roles[$query['usertype']][0];
		$users = initusers();
		echo '<h4>Selected User: <span color1>'.$users[$userid].'</span></h4>';
		echo '<div class="mytab tabs2">';//business unit role tab
			//view business unit role
			if($query['usertype'] != 1 and $query['usertype'] != 0){
				echo '<div class="mytabbody" fullwidth caption="Business Unit">';
					$query2 = $db->mquery("select id,companyname2,companyname from tblcompany");
					echo '<div class="mycollapsible">';
						while($row2=$db->mysqlm_fetch_array($query2)){
							$query4 = $db->mquery("select bu.businessunit as buname,
													if(IFNULL(role.userid,'asdf')='asdf','unchecked','checked') as checked,
													bu.id as buid 
													from tblbusinessunit bu 
													left join ".$tbname." role 
													on role.buid = bu.id and role.userid=".$userid." 
													where bu.companyid=".$row2['id']." and bu.active=1 
													order by bu.businessunit");
															
							echo '<div class="mycollapsiblelist" caption="'.$row2['companyname'].' - '.$row2['companyname2'].'">';
								echo '<table mytable fullwidth noborder>
										<!--<tr>
											<th col7>Business Unit</th>
											<th col5 centertext>Active</th>
										</tr>-->';
								while($row=$db->mysqlm_fetch_array($query4)){
									echo '<tr>
											<td title="Business Unit" paddingedleft>'.$row['buname'].'</td>
											<td centertext col2><div title="Status Check/Uncheck" class="chkuserrole mycheckbox" buid="'.$row['buid'].'" userid="'.$userid.'" value="'.$row['checked'].'"></div></td>
										</tr>';
								}
								echo '</table>';
							echo '</div>';
						}
					echo '</div>';
				echo '</div>';
			}
			echo '<div class="mytabbody" fullwidth caption="Requests">';//view user requests role RFS/TOR
				echo '<div class="mycollapsible">';
					echo '<div class="mycollapsiblelist" caption="RFS - Request For Setup">';
						$query3 = $db->mquery("select a.requesttype as requesttype,
													a.id as aid,
													if(IFNULL(b.requesttypeid,'asdf')='asdf','unchecked','checked') as checked ,
													a.thecolumns as thecolumns
												from typeofrequest a 
												left join userrequesttyperole b on a.id=b.requesttypeid 
													and b.userid=".$userid." 
												");
						echo '<table mytable fullwidth noborder>';
							while($row3 = $db->mysqlm_fetch_array($query3)){
								echo '<tr>
										<td title="Request Type">'.$row3['requesttype'].'<span colorblue fontbold title="Columns"> '.$row3['thecolumns'].'</span></td>
										<td col3><div title="Status Check/Uncheck"  class="chkrfsrole mycheckbox" rfstypeid="'.$row3['aid'].'" userid="'.$userid.'" value="'.$row3['checked'].'"></div></td>
									</tr>';
							}
						echo '</table>';
					echo '</div>';
					echo '<div class="mycollapsiblelist" caption="TOR - Transaction Override Request">';
						$query4 = $db->mquery("select a.tortype as requesttype,
													a.id as aid,
													if(IFNULL(b.requesttypeid,'asdf')='asdf','unchecked','checked') as checked
												from tortypes a
												left join userrequesttyperoletor b on a.id=b.requesttypeid 
													and b.userid=".$userid."
												");
						echo '<table mytable fullwidth noborder>';
							while($row4 = $db->mysqlm_fetch_array($query4)){
								echo '<tr>
										<td>'.$row4['requesttype'].'</td>
										<td col3><div title="Status Check/Uncheck"  class="chktorrole mycheckbox" tortypeid="'.$row4['aid'].'" userid="'.$userid.'" value="'.$row4['checked'].'"></div></td>
									</tr>';
							}
						echo '</table>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			//view user task role
			echo '<div class="mytabbody" fullwidth caption="Tasks">';
				echo '<table mytable fullwidth>';
					echo '<tr>
							<th>Task</th>
							<th centertext>Active</th>
							<th centertext>RFS</th>
							<th centertext>TOR</th>
							<th centertext>Request Types</th>
						</tr>';
					$query5 = $db->mquery("select a.usertype as usertype,
												a.id as aid,
												if(IFNULL(b.usertypeid,'asdf')='asdf','unchecked','checked') as checked,
												b.rfs as rfs, b.tor as tor
											from usertype a
											left join taskrole b on a.id=b.usertypeid
												and b.userid=".$userid."
											order by a.usertype");
					while($row=$db->mysqlm_fetch_array($query5)){
						echo '<tr>
								<td col3>'.$row['usertype'].'</td>';
								$theusertype = $db->mget("select usertype from users where id=".$userid);
							echo '<td col2 centertext>';
								if($row['aid']==$theusertype[0]){
									echo '<div class="mycheckbox chktaskrole" usertypeid="'.$row['aid'].'" userid="'.$userid.'" value="checked"></div>';
								}
								else{
									echo '<div class="mycheckbox chktaskrole" usertypeid="'.$row['aid'].'" userid="'.$userid.'" value="'.$row['checked'].'"></div>';
								}
							echo '</td>';	
							echo '<td col2 centertext>
									<div class="mycheckbox chktaskrolerfs" colib usertypeid="'.$row['aid'].'" userid="'.$userid.'" value="'.$row['rfs'].'"></div>
								</td>
								<td col2 centertext>
									<div class="mycheckbox chktaskroletor" colib usertypeid="'.$row['aid'].'" userid="'.$userid.'" value="'.$row['tor'].'"></div>
								</td>
								<td col2 centertext tdhoverable title="View Request Types">
									<div iconbtn onclick="viewrequesttyperoleclicked('.$row['aid'].',\''.$userid.'\')">
										<i class="fa fa-list"/>
									</div>
								</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['saverole'])){//save business unit role
		include 'db/dbconnect.php';
		$buid = myGET('buid');
		$userid = myGET('userid');
		$value = myGET('value');
		$roles = initroles();
		$query = mysqlm_fetch_array(mysqlm_query("select usertype from users where id=".$userid));
		$tbname=$roles[$query['usertype']][0];
		mysqlm_query("delete from ".$tbname." where userid=".$userid." and buid=".$buid);
		if($value=="checked"){
			mysqlm_query("insert into ".$tbname."(userid,buid) values(".$userid.",".$buid.")");
		}
		breakhere($con);
	}
	elseif(isset($_GET['saverfsrole'])){//saveuserrequesttyperole RFS
		include 'db/dbconnect.php';
		$rfstypeid = myGET('rfstypeid');
		$userid = myGET('userid');
		$value = myGET('value');
		//showquery();
		mysqlm_query("delete from userrequesttyperole where userid=".$userid." and requesttypeid=".$rfstypeid);
		if($value=="checked"){
			mysqlm_query("insert into userrequesttyperole(userid,requesttypeid) values(".$userid.",".$rfstypeid.")");
		}
		breakhere($con);
	}
	elseif(isset($_GET['savetorrole'])){//saveuserrequesttyperole TOR
		include 'db/dbconnect.php';
		$tortypeid = myGET('tortypeid');
		$userid = myGET('userid');
		$value = myGET('value');
		mysqlm_query("delete from userrequesttyperoletor where userid=".$userid." and requesttypeid=".$tortypeid);
		if($value=="checked"){
			mysqlm_query("insert into userrequesttyperoletor(userid,requesttypeid) values(".$userid.",".$tortypeid.")");
		}
		breakhere($con);
	}
	elseif(isset($_GET['savetaskrole'])){//save tasks role
		include 'db/dbconnect.php';
		$usertypeid = myGET('usertypeid');
		$userid = myGET('userid');
		$value = myGET('value');
		mysqlm_query("delete from taskrole where userid=".$userid." and usertypeid=".$usertypeid);
		if($value=="checked"){
			mysqlm_query("insert into taskrole(userid,usertypeid,rfs,tor) values(".$userid.",".$usertypeid.",'unchecked','unchecked')");
		}
		breakhere($con);
	}
	elseif(isset($_GET['savetaskrolerfs'])){//save tasks role rfs
		include 'db/dbconnect.php';
		$usertypeid = myGET('usertypeid');
		$userid = myGET('userid');
		$value = myGET('value');
		$query = mysqlm_query("update taskrole set rfs='".$value."' where userid=".$userid." and usertypeid=".$usertypeid);
		if(mysqlm_rowcount($query)==0){
			mysqlm_query("insert into taskrole(userid,usertypeid,rfs,tor) values(".$userid.",".$usertypeid.",'".$value."','unchecked')");
		}
		breakhere($con);
	}
	elseif(isset($_GET['savetaskroletor'])){//save tasks role tor
		include 'db/dbconnect.php';
		$usertypeid = myGET('usertypeid');
		$userid = myGET('userid');
		$value = myGET('value');
		$query = mysqlm_query("update taskrole set tor='".$value."' where userid=".$userid." and usertypeid=".$usertypeid);
		if(mysqlm_rowcount($query)==0){
			mysqlm_query("insert into taskrole(userid,usertypeid,rfs,tor) values(".$userid.",".$usertypeid.",'unchecked','".$value."')");
		}
		breakhere($con);
	}
	elseif(isset($_GET['viewtaskburole'])){//view bu task role
		include 'db/dbconnect.php';
		$buid=myGET('buid');
		$bus = initbu();
		echo '<h4>Selected BU: <span color1>'.$bus[$buid].'</span></h4>';
		echo '<div class="mytab tabs3">';
			echo '<div class="mytabbody" caption="RFS">';
				echo '<table mytable fullwidth>';
					//showquery();
					$query = mysqlm_query("select a.taskrfs as taskcompletedrfs,
													a.id as usertypeid,
													if(IFNULL(b.buid,'asdf')='asdf','unchecked','checked') as checked
											from usertype a
											left join butaskrole b
												on a.id = b.taskid and b.requesttype=1 and b.buid=".$buid."
											where a.taskcompletedrfs !='' order by a.theorder");
					while($row=mysqlm_fetch_array($query)){
						echo '<tr>';
							echo '<td col9>'.$row['taskcompletedrfs'].'</td>';
							echo '<td><div class="mycheckbox chkrfsapprover" buid="'.$buid.'" taskid="'.$row['usertypeid'].'" value="'.$row['checked'].'"></div></td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';
			echo '<div class="mytabbody" caption="TOR">';
				echo '<table mytable fullwidth>';
					$query = mysqlm_query("select a.tasktor as taskcompletedtor,
												a.id as usertypeid,
												if(IFNULL(b.buid,'asdf')='asdf','unchecked','checked') as checked
											from usertype a
											left join butaskrole b
												on a.id = b.taskid and b.requesttype=2  and b.buid=".$buid."
											where a.taskcompletedtor !='' order by a.theorder");
					while($row=mysqlm_fetch_array($query)){
						echo '<tr>';
							echo '<td col9>'.$row['taskcompletedtor'].'</td>';
							echo '<td><div class="mycheckbox chktorapprover" buid="'.$buid.'" taskid="'.$row['usertypeid'].'" value="'.$row['checked'].'"></div></td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';
		echo '</div>';
		breakhere($con);
	}
	elseif(isset($_GET['savebutaskrole'])){//save bu task role
		include 'db/dbconnect.php';
		$buid = myGET('buid');
		$taskid=myGET('taskid');
		$value=myGET('value');
		$requesttype=myGET('requesttype');
		//showquery();
		mysqlm_query("delete from butaskrole where buid=".$buid." and taskid=".$taskid." and requesttype=".$requesttype);
		if($value=="checked"){
			mysqlm_query("insert into butaskrole(buid,taskid,requesttype) values($buid,$taskid,$requesttype)");
		}
		breakhere($con);
	}
	elseif(isset($_GET['getusergroups'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("select groupname,id from usergroups");
		$arr = [];
		$arr2 = [];
		while($row=mysqlm_fetch_array($query)){
			$arr[] = $row['id'];
			$arr2[] = $row['groupname'];
		}
		echo my2darrencode($arr,$arr2);
		breakhere($con);
	}
	elseif(isset($_GET['changecurrentusergroup'])){
		include 'db/dbconnect.php';
		$ugid = myGET('ugid');
		$_SESSION['currentusergroupid'] = $ugid;
		breakhere($con);
	}
	elseif(isset($_GET['viewrequesttyperole'])){//modal user requesttype role
		include 'db/dbconnect.php';
		$users = initusers();
		$userid = myGET('userid');
		$usertype = initusertype();
		$usertypeid = myGET('usertypeid');
		echo '<h4>
				Selected User: 
				<span color1>'.$users[$userid].'</span><br>
				</h4>
			<div marginedbottom>
				Allow only Type of request for <b>'.$usertype[$usertypeid].'.</b>.<br/>
				Note: Leave all unchecked for no restriction.
			</div>';
		echo '<div class="collapsiblerequesttype" >
				<div class="mycollapsiblelist" caption="RFS">';
					$query = mysqlm_query("select a.requesttype,a.id,
											if(ifnull(b.id,'asdf')='asdf','unchecked','checked') as value 
											from typeofrequest a left join typeofrequestrole b on a.id = b.typeofrequestid 
												and b.userid=$userid
												and b.requesttype='RFS'
												and b.usertypeid=$usertypeid");
					echo '<table mytable fullwidth noborder>';
						while($row = mysqlm_fetch_array($query)){
							echo '<tr>
									<td>'.$row['requesttype'].'</td>
									<td col3><div title="Status Check/Uncheck"  class="chkrfsrequeststyperole mycheckbox" rfstypeid="'.$row['id'].'" value="'.$row['value'].'"></div></td>
								</tr>';
						}
					echo '</table>';
			echo'</div>
				<div class="mycollapsiblelist" caption="TOR">';
					$query = mysqlm_query("select a.tortype,a.id,
											if(ifnull(b.id,'asdf')='asdf','unchecked','checked') as value 
											from tortypes a left join typeofrequestrole b on a.id = b.typeofrequestid 
												and b.userid=$userid
												and b.requesttype='TOR'
												and b.usertypeid=$usertypeid");
					echo '<table mytable fullwidth noborder>';
						while($row = mysqlm_fetch_array($query)){
							echo '<tr>
									<td>'.$row['tortype'].'</td>
									<td col3><div title="Status Check/Uncheck"  class="chktorrequeststyperole mycheckbox" tortypeid="'.$row['id'].'" value="'.$row['value'].'"></div></td>
								</tr>';
						}
					echo '</table>';
			echo'</div>
			</div>';
		breakhere($con);
	}
	elseif(isset($_GET['saverequesttyperole'])){
		include 'db/dbconnect.php';
		$value = myGET('value');
		$requesttype = mySTRget('requesttype');
		$requesttypeid = myGET('requesttypeid');
		$userid = myGET('userid');
		$usertypeid = myGET('usertypeid');
		if($requesttype==mySTR("RFS")){
			mysqlm_query("delete from typeofrequestrole where userid=$userid 
															and typeofrequestid=$requesttypeid 
															and requesttype=$requesttype
															and usertypeid=$usertypeid");
			if($value=="checked"){
				//mysqlm_query("insert into butaskrole(buid,taskid,requesttype) values($buid,$taskid,$requesttype)");
				mysqlm_query("insert into typeofrequestrole(userid,typeofrequestid,requesttype,usertypeid) values($userid,$requesttypeid,$requesttype,$usertypeid)");
			}	
		}
		elseif($requesttype==mySTR("TOR")){
			mysqlm_query("delete from typeofrequestrole where userid=$userid 
															and typeofrequestid=$requesttypeid 
															and requesttype=$requesttype
															and usertypeid=$usertypeid");
			if($value=="checked"){
				//mysqlm_query("insert into butaskrole(buid,taskid,requesttype) values($buid,$taskid,$requesttype)");
				mysqlm_query("insert into typeofrequestrole(userid,typeofrequestid,requesttype,usertypeid) values($userid,$requesttypeid,$requesttype,$usertypeid)");
			}	

		}
		
		
		breakhere($con);
	}
?>	
<br/>
<?php
	include 'db/dbconnect.php';
	checksession();
	// if(getusertype()!=0){
		// echo '<br/>You should be an admin to access this page.';
		// breakhere($con);
	// }
?>
<div class="myframe">
	<h3> Current User's Group: 
		<?php 
			echo '<span color1 class="currentusergroup">'.getcurrentusergroup().'</span>';
			if(getusertype()==0){
				echo '<span class="editcurrentbu" onclick="editcurrentusergroup()" title="Change Current User Group">
					&nbsp;<i class="fa fa-pencil"/>
				</span>';
			}
		?>
	</h3>
	<div class="mytab tabs1">
		<div class="mytabbody" fullwidth caption="Users">
			<div coli col12>
				<div mytoolsgroup col2 displaytablecell>
					<button onclick="addnewuserclicked()" marginedright>Add new user</button>
				</div>
				<div mytoolsgroup col10 displaytablecell noborderleft>
					<input class="filterusername" title="Press Enter" type="text" col3 placeholder="Search User, User type"/>
					<!--<div coli col3 marginedleft><div class="mycheckbox chkshowapproved" value="checked" coli></div><div coli marginedleft> Show approved</div></div>-->
				</div>
			</div>
			<div class="requeststable" mygroup col12 bordertopwhite>
				<div class="tabusers" fullwidth>
					
				</div>
			</div>
		</div>
		<div class="mytabbody" fullwidth caption="Business Unit">
			<div class="tableft tableft1">
				<?php
					$query = mysqlm_query("select companyname,id from tblcompany");
					while($row = mysqlm_fetch_array($query)){
						echo '<div class="tableftbody" caption="'.$row['companyname'].'" fullwidth>
							<div class="bucontainer">';
								$query2 = mysqlm_query("select businessunit,companyid,contactnumber,address,id,active from tblbusinessunit where companyid = ".$row['id'].' order by businessunit');
								echo '<table mytable fullwidth>
										<tr>
											<th col4>Business Unit</th>
											<th col2>Contact Number</th>
											<th col3>Address</th>
											<th col2 centertext>Active</th>
											<th col1 centertext >Action</th>
										</tr>
								';
								while($row2=mysqlm_fetch_array($query2)){
									echo '<tr>
											<td class="tdeditablebuname" value="'.$row2['businessunit'].'" buid="'.$row2['id'].'">'.$row2['businessunit'].'</td>
											<td class="tdeditablebucontactnumber" value="'.$row2['contactnumber'].'" buid="'.$row2['id'].'">'.$row2['contactnumber'].'</td>
											<td class="tdeditablebuaddress" value="'.$row2['address'].'" buid="'.$row2['id'].'">'.$row2['address'].'</td>
											<td centertext style="padding:5px !important"><div class="mycheckbox buisactive" value="'.($row2['active']==1?"checked":"unchecked").'" thevalueid="'.$row2['id'].'"></div></td>
											<td centertext tdhoverable><div iconbtn title="View BU roles" onclick="viewburoleonclicked(\''.$row2['id'].'\')"><i class="fa fa-cog"/></div></td>
										</tr>';
								}
								echo '</table>';
						echo'</div>
						</div>';
					}
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(".tabs1").mytab();
	$(".tableft1").mylefttab();
	$(".tdeditablebucontactnumber").tdeditable(function(el){
		var buid = $(el).attr("buid");
		var value = $(el).attr("value");
		$.get("setup.php?updatebucontacnumber&id="+buid+"&value="+value,function(result){
			showMyAlert("BU Contact Number Updated!","");
		});
	});
	$(".tdeditablebuaddress").tdeditable(function(el){
		var buid = $(el).attr("buid");
		var value = $(el).attr("value");
		$.get("setup.php?updatebuaddress&id="+buid+"&value="+value,function(result){
			showMyAlert("BU Address Updated!","");
		});
	});
	$(".tdeditablebuname").tdeditable(function(el){
		var buid = $(el).attr("buid");
		var value = $(el).attr("value");
		$.get("setup.php?updatebuname&id="+buid+"&value="+value,function(result){
			showMyAlert("BU Name Updated!","");
		});
	});
	// $(".filterusername").on('keyup',function(evt){
		// if(evt.keyCode==13){
			// refreshusers();
		// }
	// });
	refreshusers();
	var usermodal;
	$(".buisactive").mycheckbox(function(el){
		var thevalueid = $(el).attr("thevalueid");
		var thevalue = $(el).mycheckboxvalue();
		$.get("setup.php?updatebuactive&id="+thevalueid+"&thevalue="+thevalue,function(result){
			showMyAlert("BU Status Active Updated!","");
		});
	});
	function createaccountclicked(){
		//addusermodal.showloading();
		var firstname = $(".txtfirstname").val();
		var lastname = $(".txtlastname").val();
		var usertype = $(".cbousertype").val();
		var username = $(".txtusername").val();
		var password = $(".txtpassword").val();
		var companyname = $(".cbocompanyname").val();
		var businessunit = $(".cbobusinessunit").val();
		var contactnumber = $(".txtcontactnumber").val();
		var address = $(".txtaddress").val();
		$.post("setup.php",{
			createaccount:emptyval,
			firstname:firstname,
			lastname:lastname,
			usertype:usertype,
			username:username,
			password:password,
			companyname:companyname,
			businessunit:businessunit,
			contactnumber:contactnumber,
			address:address
		},function(result){
			//mymodalclose(usermodal);
			//addusermodal.close();
			if(result=="exists"){
				showMyAlertError("Username Already Exists!","");
			}
			else{
				showMyAlert("New Account Created!","");
				$(".txtfirstname").val("");
				$(".txtlastname").val("");
				$(".cbousertype").val("");
				$(".txtusername").val("");
				$(".txtpassword").val("");
				$(".cbocompanyname").val("");
				$(".cbobusinessunit").val("");
				$(".txtcontactnumber").val("");
				$(".txtaddress").val("");
			}
		});
	}
	function refreshusers(){
		//$.get("setup.php?loaduserstable&searchuser="+$(".filterusername").val(),function(result){
		$.get("setup.php?loaduserstable",function(result){
			$(".tabusers").html(result);
			$(".tblusers").tablesearcheable($(".filterusername"));
			refreshthis($(".bodyelements"));
		});
	}
	var addusermodal = new MyModal();
	function addnewuserclicked(){
		//usermodal = showMyModalSmall("Add new user","asdf");
		//$(usermodal).find(".mymodalbody").addloading();
		addusermodal.title("Add new user");
		addusermodal.show();
		$.get("setup.php?addnewuser",function(result){
			addusermodal.body(result);
			$(".cbousertype").change(function(){
				if($(this).val()=='1'){
					$(".userdetails").removeAttr("displaynone");
				}
				else{
					$(".userdetails").attr("displaynone","");
				}
			});
			$(".cbobusinessunit").change(function(){
				
			});
		});
	}
	function usersettingsdelteaccount(thisel,userid){
		showMyConfirm("Are you sure you want to delete Account?",
					function(){
						$.get("setup.php?deleteuser&userid="+userid,function(result){
							showMyAlert("User deleted!","");
							viewusermodal.close();
							refreshusers();
						});
					},
					function(){});
		
	}
	function usersettingssavechanges(thisel,userid){
		var firstname = $(".txtfirstname").val();
		var lastname = $(".txtlastname").val();
		var usertype = $(".cbousertype").val();
		var username = $(".txtusername").val();
		var password = $(".txtpassword").val();
		var companyname = $(".cbocompanyname").val();
		var businessunit = $(".cbobusinessunit").val();
		var contactnumber = $(".txtcontactnumber").val();
		showMyConfirm("Save Changes?",
		function(){
			$.post("setup.php",{
				updateusersettings:emptyval,
				firstname:firstname,
				lastname:lastname,
				usertype:usertype,
				username:username,
				password:password,
				companyname:companyname,
				businessunit:businessunit,
				userid:userid
			},function(result){
				//mymodalclose(usermodal);
				//addusermodal.close();
				if(result=="exists"){
					showMyAlertError("Username Already Exists!","");
				}
				else{
					showMyAlert("Account Changes Saved!","");
					viewusermodal.close();
				}
			});
		},function(){
			
		});
	}
	var viewusermodal = new MyModal();
	function viewuseraccount(userid){
	//var modalviewuser = new MyModal();
		viewusermodal.showcustom("View User","55%");
		$.get("setup.php?viewuser&userid="+userid,function(result){
			viewusermodal.body(result);
		});
	}
	var modalburole = new MyModal();
	function viewburole(userid){
		modalburole.show("User Roles");
		$.get("setup.php?viewuserrole&userid="+userid,function(result){
			modalburole.body(result);
			$(".tabs2").mytab();
			$(".mycollapsible").mycollapsible();
			$(".chkuserrole").mycheckbox(function(thisel){
				var buid = $(thisel).attr("buid");
				var userid = $(thisel).attr("userid");
				var value = $(thisel).attr("value");
				$.get("setup.php?saverole&userid="+userid+"&buid="+buid+"&value="+value,function(result){
					showMyAlert("User role updated!","");
				});
			});
			$(".chkrfsrole").mycheckbox(function(thisel){
				var rfstypeid = $(thisel).attr("rfstypeid");
				var userid = $(thisel).attr("userid");
				var value = $(thisel).attr("value");
				$.get("setup.php?saverfsrole&userid="+userid+"&rfstypeid="+rfstypeid+"&value="+value,function(result){
					showMyAlert("User RFS role updated!","");
				});
			});
			$(".chktorrole").mycheckbox(function(thisel){
				var tortypeid = $(thisel).attr("tortypeid");
				var userid = $(thisel).attr("userid");
				var value = $(thisel).attr("value");
				$.get("setup.php?savetorrole&userid="+userid+"&tortypeid="+tortypeid+"&value="+value,function(result){
					showMyAlert("User TOR role updated!","");
				});
			});
			$(".chktaskrole").mycheckbox(function(thisel){
				var usertypeid = $(thisel).attr("usertypeid");
				var userid = $(thisel).attr("userid");
				var value = $(thisel).attr("value");
				$.get("setup.php?savetaskrole&userid="+userid+"&usertypeid="+usertypeid+"&value="+value,function(result){
					showMyAlert("User Task role updated!","");
					//echo(result);
				});
			});
			$(".chktaskrolerfs").mycheckbox(function(thisel){
				var usertypeid = $(thisel).attr("usertypeid");
				var userid = $(thisel).attr("userid");
				var value = $(thisel).attr("value");
				$.get("setup.php?savetaskrolerfs&userid="+userid+"&usertypeid="+usertypeid+"&value="+value,function(result){
					showMyAlert("User Task role rfs updated!","");
				});
			});
			$(".chktaskroletor").mycheckbox(function(thisel){
				var usertypeid = $(thisel).attr("usertypeid");
				var userid = $(thisel).attr("userid");
				var value = $(thisel).attr("value");
				$.get("setup.php?savetaskroletor&userid="+userid+"&usertypeid="+usertypeid+"&value="+value,function(result){
					showMyAlert("User Task role tor updated!","");
					//echo(result);
				});
			});
			
		});
	}
	function viewburoleonclicked(buid){//view bu task role on click function
		var modalviewburole = new MyModal();
		modalviewburole.show("BU Task role");
		$.get("setup.php?viewtaskburole&buid="+buid,function(result){
			modalviewburole.body(result);
			$(".tabs3").mytab();
			$(".chkrfsapprover").mycheckbox(function(thisel){
				var taskid = $(thisel).attr("taskid");
				var buid = $(thisel).attr("buid");
				var value = $(thisel).attr("value");
				$.get("setup.php?savebutaskrole&buid="+buid+"&taskid="+taskid+"&value="+value+"&requesttype=1",function(result){
					showMyAlert("User BU Task role updated!","");
					//console.log(result);
				});
			});
			$(".chktorapprover").mycheckbox(function(thisel){
				var taskid = $(thisel).attr("taskid");
				var buid = $(thisel).attr("buid");
				var value = $(thisel).attr("value");
				$.get("setup.php?savebutaskrole&buid="+buid+"&taskid="+taskid+"&value="+value+"&requesttype=2",function(result){
					showMyAlert("User BU Task role updated!","");
					//console.log(result);
				});
			});
		});
	}
	function editcurrentusergroup(){
		$.get("setup.php?getusergroups",function(result){
			result = result.my2darrdecode();
			var ids = result[0];
			var grps = result[1];
			var menus = [];
			var menufunctions = [];
			var a = 0;
			for(a = 0;a < ids.length;a++){
				menus.push(grps[a]);
				menufunctions.push(editcurrentbuchange(ids[a]));
			}
			showMyPopupMenu(menus,menufunctions);
		});
	}
	function editcurrentbuchange(ugid){
		return function(){
			$.get("setup.php?changecurrentusergroup&ugid="+ugid,function(result){
				loadbodyelements("setup.php");
			});
		}
	}
	var modalrequesttyperole = new MyModal();
	function viewrequesttyperoleclicked(usertypeid,userid){
		modalrequesttyperole.title("Request Type Role");
		modalrequesttyperole.show();
		$.get("setup.php?viewrequesttyperole&userid="+userid+"&usertypeid="+usertypeid,function(result){
			modalrequesttyperole.body(result);
			$(".collapsiblerequesttype").mycollapsible();
			$(".chkrfsrequeststyperole").mycheckbox(function(el){
				var rfstypeid = $(el).attr("rfstypeid");
				var thevalue = $(el).mycheckboxvalue();
				$.get("setup.php?saverequesttyperole&requesttype=RFS&value="+thevalue+
													"&requesttypeid="+rfstypeid+
													"&userid="+userid+
													"&usertypeid="+usertypeid,function(result){
					showMyAlert("Request Type Role updated!","");
				});
			});
			$(".chktorrequeststyperole").mycheckbox(function(el){
				var tortypeid = $(el).attr("tortypeid");
				var thevalue = $(el).mycheckboxvalue();
				$.get("setup.php?saverequesttyperole&requesttype=TOR&value="+thevalue+
													"&requesttypeid="+tortypeid+
													"&userid="+userid+
													"&usertypeid="+usertypeid,function(result){
					showMyAlert("Request Type Role updated!","");
					//echo(result);
				});
			});
		});
	}
</script>