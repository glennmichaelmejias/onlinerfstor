<?php
	if(isset($_GET['logout'])){
		session_start();
		//unset($_SESSION['currentuserid']);
		session_unset();
		echo 'asdf';
		die();
	}
	elseif(isset($_GET['checkiflogined'])){
		session_start();
		if(isset($_SESSION['currentuserid'])){
			echo 'success';
		}
		die();
	}
	elseif(isset($_GET['checklogin'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("select id,usertype from users where trim(username) = '".trim($_GET['username'])."' and trim(password)='".trim($_GET['password'])."'");
		if(mysqlm_rowcount($query)==1){
			echo 'success';
			$_SESSION['currentuserid'] = mysqlm_fetch_array($query)[0];
			setthesessions();
		}
		else{
			echo 'invalid';
		}
		breakhere($con);
	}
	elseif(isset($_GET['getusertype'])){
		include 'db/dbconnect.php';
		$query = mysqlm_fetch_array(mysqlm_query("select ifnull(isadmin,0) from users where id = ".getuserid()));
		$query2 = mysqlm_query("select usertypeid from taskrole where userid=".getuserid());
		echo getusertype().",";
		while($row = mysqlm_fetch_array($query2)){
			echo $row['usertypeid'].",";
		}
		//echo getusertype().",".$query[0];
		breakhere($con);
	}
	elseif(isset($_GET['getuserfullname'])){
		include 'db/dbconnect.php';
		echo getuserfullname();
		breakhere($con);
	}
	elseif(isset($_GET['getrfsonly'])){
		include 'db/dbconnect.php';
		echo getrfsonly();
		breakhere($con);
	}
	elseif(isset($_GET['usersettings'])){
		include 'db/dbconnect.php';
		$query = mysqlm_query("select username,password,firstname,lastname from users where id=".getuserid());
		$row = mysqlm_fetch_array($query);
		echo '<div mygroup col12>
				<div fontbold marginedbottom>User Details</div>
				<div col2 coli colctrl marginedbottom marginedleft>First name</div><input class="txtfirstname" title="Firstname" value="'.ucfirst($row['firstname']).'" type="text" col3/>
				<div col1 coli></div>
				<div col2 coli colctrl>Last name</div><input class="txtlastname" title="Last name" value="'.ucfirst($row['lastname']).'" type="text" col3/>
			</div>';
		
		echo'<div mygroup col12 nobordertop>
				<div fontbold marginedbottom>User Security</div>
				<div col2 coli colctrl marginedleft>Username</div><input class="txtusername" title="Username" value="'.$row['username'].'" type="text" col3/>
				<div col1 marginedtop></div>
				<div col2 coli colctrl  marginedleft>Current Password</div><input class="txtcurrentpassword" title="Password" value="" type="password" col3/>
				<div class="invalidcurrentpassword" marginedleft coli colctrl color1>Enter your current password to change it.</div>
				<div col1 marginedtop></div>
				<div col2 coli colctrl marginedleft>New Password</div><input class="txtnewpassword" title="Password" value="" type="password" col3/>
				<div col1 marginedtop></div>
				<div col2 coli colctrl marginedleft>Confirm Password</div><input class="txtconfirmpassword" title="Password" value="" type="password" col3/>
				<div class="mismatchpassword" col6 coli colctrl marginedleft colorred>&nbsp;</div>
			</div>';
		echo'<div mygroup col12 nobordertop>
				<div buttongroup col4 marginedbottom marginedtop floatright>
					<button col6 onclick="usersettingssavechanges(this)">Save Changes</button>
					<button col6 onclick="usersettingscancel()">Cancel</button>
				</div>
			</div>';
		breakhere($con);
	}
	elseif(isset($_GET['getcurrentpassword'])){
		include 'db/dbconnect.php';
		$currentpassword = myGET('currentpassword');
		$query = mysqlm_query("select password from users where id=".getuserid());
		if(mysqlm_fetch_array($query)[0]==$currentpassword){
			echo 'success';
		}
		else{
			echo 'invalidpassword';
		}
		breakhere($con);
	}
	elseif(isset($_POST['updateusersettings'])){
		include 'db/dbconnect.php';
		$firstname = mySTRpost('firstname');
		$lastname = mySTRpost('lastname');
		$username = mySTRpost('username');
		$password = mySTRpost('password');
		$userid=getuserid();
		$checkusername = mysqlm_query("select id from users where username = ".$username. " and id !=$userid");
		if(mysqlm_rowcount($checkusername)>0){
			echo 'exists';
		}
		else{
			if($_POST['password']==""){
				mysqlm_query("update users set firstname=$firstname,lastname=$lastname,username=$username where id=$userid");
			}
			else{
				mysqlm_query("update users set firstname=$firstname,lastname=$lastname,username=$username,password=$password where id=$userid");
			}
			
		}
		
		breakhere($con);
	}
?>
<style>
	.logincontainer{
		//background-color:#FFF;
		width:310px;
		height:310px;
		display:inline-block;
		//border:solid 1px #AAA;
		margin-top:-8px;
	}
	.textwithimage{
		border:1px solid #C0C2C3;
		width:200px;
		padding:5px;
		font-size:15px;
		margin-left:auto;
		margin-right:auto;
		background-color:white;
		margin-top:2px;
		
	}
	.textwithimagetext{
		border:none;
		background-color:rgba(0,0,0,0);
		margin-left:5px;
	}
	.thelogo{
		background-image:url('img/logo.png');
		width:70px;
		height:70px;
		background-size:contain;
		margin-left:auto;
		margin-right:auto;
		margin-top:30px;
	}
	.thetitle{
		font-size:15px;
		padding:20px;
		color:#1175B0;
	}
</style>
<div class="tableparent" style="background-image:url('img/back6.jpg');background-size:cover;//background-position-y:-50px;//background-position-x:-150px;;background-repeat:no-repeat;//background:linear-gradient(#FEFEFE,#F0F0F0);width:100%;height:100%;position:fixed;left:0;top:0;">
	<?php
		include 'widget/clock.php';
		include 'widget/calendar.php';
		//include 'widget/dance.php';
	?>
	<div class="tablecell">
		<div class="logincontainer">
			<div style="//border:1px solid white;height:100%">
				<div class="thelogo">
				
				</div>
				<div class="thetitle" fontbold>
					Online RFS & TOR
				</div>
				<div class="textwithimage" style="">
					<div class="fa fa-user" color2 style="display:inline-block;//color:#C0C2C3;"></div>
					<input type="text" nooutline value="" name="txtusername" class="textwithimagetext logintxtusername" onkeypress="txusernamekeypressed(event)" onfocus="txtusernameonfocus()" placeholder="Username"/>
				</div>
				<div class="textwithimage" style="">
					<div class="fa fa-lock" color2 style="display:inline-block"></div>
					<input type="password" nooutline  value="" class="textwithimagetext logintxtpassword" onkeypress="txusernamekeypressed(event)" onfocus="txtusernameonfocus()" placeholder="Password"/>
				</div>
				<button onClick="btnloginclicked()" style="width:210px;margin-top:2px;height:30px;margin-left:1px;">Login</button>
				<div style="color:red;margin-top:10px;visibility:hidden" class="lblinvalidlogin">Invalid Username/Password</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$(".logintxtusername").focus();
	});
	function txusernamekeypressed(event){
		if(event.keyCode==13){
			btnloginclicked();
		}
		$(".lblinvalidlogin").css("visibility","hidden");
		//txtusernameonfocus();
	}
	function txtusernameonfocus(){
		//$(".lblinvalidlogin").css("visibility","hidden");
	}
	checkstatus();
	function checkstatus(){
		$.get("login.php?checkiflogined",function(result){
			//alert(result);
			if(result=="success"){
				loginsuccess();
			}
		})
	}
	function loginsuccess(){
		isloginna = true;
		isloadna=false;
		$(".bodyelements").html("");
		$(".pageloadingbackground").clearQueue();
		$(".pageloadingbackground").animate({opacity:"1"},100,function(){$(this).css("visibility","visible")});
		$.get("login.php?getusertype",function(result){
			result=result.split(",");
			//alert(result);
		//	$("body").find(".navitem").removeAttr("displaynone");
			for(a in result){
				$(".navbar").find("[type"+result[a]+"]").removeAttr("displaynone");
			}
			//$("body").find(".navitem:not([type"+result[0]+"])").remove();
		});
		// $.get("login.php?getrfsonly",function(result){
			// if(result=="1"){
				// $("body").find(".navitem[rfsonly]").remove();
			// }
		// });
		$.get("login.php?getuserfullname",function(result){
			$(".currentuesrname").html(result);
		});
		$(".bodyelements").load(currentpage,function(){
			isloadna=true;
			//$(".bodyelements").animate({opacity:"1"},200);
			$(".bodyelements").addClass("show");
			$(".pageloadingbackground").animate({opacity:"0"},300,function(){$(this).css("visibility","hidden")});
			refreshthis($(".bodyelements"));
		});
	}
	function btnloginclicked(){
		var username = $(".logintxtusername").val();
		var password = $(".logintxtpassword").val();
		$(".pageloadingbackground").clearQueue();
		$(".pageloadingbackground").animate({opacity:"1"},100,function(){$(this).css("visibility","visible")});
		$.get("login.php?checklogin&username="+username+"&password="+password,function(result){
			console.log(result);
			if(result=="success"){
				loginsuccess();
			}
			else{
				$(".lblinvalidlogin").css("visibility","visible");
				$(".pageloadingbackground").animate({opacity:"0"},300,function(){$(this).css("visibility","hidden")});
				$(".logintxtusername").focus().select();
			}
		});
	} 
	function usersettingssavechanges(thisel){
		var thisel = $(thisel).parentsUntil("",".mymodalbody");
		var firstname = $(thisel).find(".txtfirstname").val();
		var lastname = $(thisel).find(".txtlastname").val();
		var username = $(thisel).find(".txtusername").val();
		var currentpassword = $(thisel).find(".txtcurrentpassword").val();
		var newpassword = $(thisel).find(".txtnewpassword").val();
		var confirmpassword = $(thisel).find(".txtconfirmpassword").val();
		$(thisel).find(".txtcurrentpassword").on('keydown',function(){
			$(thisel).find(".invalidcurrentpassword").html("To change your password. Please enter your current password.");
			$(thisel).find(".mismatchpassword").html("&nbsp;");
		});
		$(thisel).find(".txtnewpassword").on('keydown',function(){
			$(thisel).find(".mismatchpassword").html("&nbsp;");
			$(thisel).find(".invalidcurrentpassword").html("To change your password. Please enter your current password.");
		});
		$(thisel).find(".txtconfirmpassword").on('keydown',function(){
			$(thisel).find(".mismatchpassword").html("&nbsp;");
			$(thisel).find(".invalidcurrentpassword").html("To change your password. Please enter your current password.");
		});
		if(currentpassword!=""){
			$.get("login.php?getcurrentpassword&currentpassword="+currentpassword,function(result){
				if(result=="invalidpassword"){
					$(thisel).find(".invalidcurrentpassword").html("<span colorred>Invalid Password! Please enter your current password.</span>");
				}
				else{
					if(newpassword!=confirmpassword){
						$(".mismatchpassword").html("Password mismatch!");
					}
					else{
						$.post("login.php",
							{
								updateusersettings:emptyval,
								firstname:firstname,
								lastname:lastname,
								username:username,
								password:newpassword
							},
							function(result){
								if(result=="exists"){
									showMyAlertError("Username already exists! Try another username.","");
								}
								else{
									showMyAlert("Useraccount successfully updated!","");
									modalusersettings.close();
								}
							}
						)
					}
				}
			});
		}
		else{
			$.post("login.php",
				{
					updateusersettings:emptyval,
					firstname:firstname,
					lastname:lastname,
					username:username,
					password:newpassword
				},
				function(result){
					if(result=="exists"){
						showMyAlertError("Username already exists! Try another username.","");
					}
					else{
						showMyAlert("Useraccount successfully updated!","");
						modalusersettings.close();
					}
				}
			)
		}
	}
	function usersettingscancel(){
		modalusersettings.close();
	}
</script>