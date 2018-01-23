<?php
	//$myfile = fopen("index.php", "r") or die("Unable to open file!");
	//echo fread($myfile,filesize("index.php"));
	//fclose($myfile);
	//die();
	
?>
<!Doctype html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Online TOR & RFS</title>
	<link rel="icon" href="img/icon2.png">
	<link href="jquery-ui/jquery-ui.css" rel="stylesheet">
	<link href="jquery-ui/MonthPicker.min.css" rel="stylesheet">
	<link href="css/css.css" rel="stylesheet">
	<link href="css/mycss.css" rel="stylesheet">
	<link href="css/font-awesome.css" rel="stylesheet">
	<!--<link href="chart/leftbarchart.css" rel="stylesheet">-->
	<script src="jquery-ui/external/jquery/jquery.js"></script>
	<script src="jquery-ui/jquery-ui.js"></script>
	<script src="jquery-ui/MonthPicker.min.js"></script>
	<?php include('myjs/myjs.php') ?>
</head>
<body>
	<?php
		//include 'widget/dance.php';
	?>
	<div class="navbar">
		<div class="navitem thehome" type0 type1 type2 type3 type4 type5 page="home.php">Home</div>
		<div class="navitem" displaynone type2 page="requests.php">Approve</div>
		<!--<div class="navitem" displaynone type4 page="mhoney.php">Group IV Manager</div>-->
		<!--<div class="navitem" displaynone type3 page="execute.php">Execute</div>-->
		<div class="navitem" displaynone type4 page="mhoney.php">Review</div>
		<div class="navitem" displaynone type5 page="iad.php">Verify</div>
		<div class="navitem" displaynone type6 page="sysupdate.php">Requests</div>
		
		<div class="navitem navexecute" displaynone type3 page="execute.php">Execute</div>
		<!--<div class="navitem" displaynone type3 page="executeapproved.php">Approved Requests</div>-->
		<div class="navitem" displaynone type3 page="reports.php">Reports</div>
		<div class="navitem" displaynone type1 page="request.php">RFS</div>
		<div class="navitem" displaynone type1 page="requesttor.php">TOR</div>
		<div class="navitem" displaynone type0 foradmin page="setup.php">Admin Setup</div>
		<div class="pagetoolstime">
			<div class="tableparent">
				<div class="tablecell todaydate" style="text-align:right">
					08:45am &nbsp; Tuesday &nbsp; May 04, 2017 &nbsp;
				</div>
			</div>
		</div>
		<div class="pagetoolstime lblcurrentuser" marginedright cursorpointer title="Current User">
			<div class="tableparent">
				<div class="tablecell" style="text-align:right">
					<span class="currentuesrname" color1>
						
					</span>
				</div>
			</div>
		</div>
		<!--<div class="navitem navtrashcan" style="width:30px;float:right">
			<div class="trashcanparent" style="margin:0px;height:14px">
				<i class="fa fa-trash-o trashcan"></i>
			</div>
		</div>-->
		<div class="zoompercentage">
			zoom: 100%
		</div>
	</div>
<div class="bodyelements" id="bodyelementsid">
	
</div>
<div class="statusbar">
	<div class="statusprogress">
		<div class="tableparent" style="text-align:left">
			<div class="tablecell">
				<div style="height:4px;">
					<span class="progressrow" marginedleft></span>
					<div class="syncprogresscontainer">
						<div class="syncprogress"></div>
					</div>
					<span class="progressrow2" marginedleft color1></span>
				</div>
			</div>
		</div>
	</div>
	<div class="pagetoolszoom">
		<div class="tableparent">
			<div class="tablecell">
				<div class="zoomslidercontainer">
					<div class="zoomslider"></div>
				</div>
			</div>
		</div>
		<div class="pagetoolzoomout">
			<i class="fa fa-search-minus thezoomclass"></i>
		</div>
		<div class="pagetoolzoomin">
			<i class="fa fa-search-plus thezoomclass"></i>
		</div>
	</div>
</div>
<script type="text/javascript">
	var selectedbuid=0;
	var days = [
		"Sunday",
		"Monday",
		"Tuesday",
		"Wednesday",
		"Thursday",
		"Friday",
		"Saturday"
	];
	var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
	setInterval(refreshtime = function(){
	//	to_dtime = to_date.toLocaleString('en-US',{hour:'numeric',minute:'numeric',hour12: true}).replace(" ","").toLowerCase();
		var to_date = new Date();
		to_dtime = formatAMPM(to_date);
		to_dday = days[to_date.getDay()];
		to_dmonth = monthNames[to_date.getMonth()];
		to_dyear = to_date.getFullYear();
		$(".todaydate").html(to_dtime + " &nbsp; " + to_dday + " &nbsp; " + to_dmonth + " " + pad(to_date.getDate(),2,0) + ", " + to_dyear + " &nbsp;");
	},1000);
	refreshtime();
	var modalusersettings = new MyModal();
	$(".lblcurrentuser").click(function(){
		if(isloginna){
			var menus=Array();
			var menufunctions=Array();
			menus.push('<i class="fa fa-user"/> User settings');
			menus.push('<i class="fa fa-sign-out"/> Log out');
			menufunctions.push(function(){
				//modalusersettings = ;
				modalusersettings.showcustom("User Settings","55%");
				$.get("login.php?usersettings",function(result){
					modalusersettings.body(result);
				});
			});
			menufunctions.push(function(){
				//window.location.reload();
				$.get("login.php?logout",function(result){
					window.location.reload();
				});
			});
			showMyPopupMenu(menus,menufunctions);
		}
	});
</script>
<script type="text/javascript">
	var currentzoom=100;
	var isloginna=false;
	var currentpage="";
	var isloadna=true;
	
	var MyAlertTimeout;
	var emptyval = [];
	var mymodal = new MyModal();
	emptyval.push("");
	var today = new Date();
	
	var curx;
	var cury;
	$("body").on("mousemove",function(evt){
		curx = evt.clientX * (100/currentzoom);
		cury = evt.clientY * (100/currentzoom);
	});
	
	$(document).ready(function(){
		var zoomtimeout;
		var refreshslider=function(){
			$(".zoompercentage").css("display","block");
			var thezoom = parseFloat($(".zoomslider").slider('option','value'));
			if(thezoom > 92 && thezoom < 107){
				currentzoom=100;
			}
			else{
				currentzoom=parseFloat($(".zoomslider").slider('option','value'));
			}
			$(".bodyelements").css("zoom",currentzoom/100);
			$(".zoompercentage").html("zoom: "+currentzoom+"%");
		};
		$(".zoomslider").slider({
			min:50,
			max:200,
			value:100,
			change:function(event,ui){
				$(".zoompercentage").clearQueue();
				$(".zoompercentage").animate({opacity:1},100);
				clearTimeout(zoomtimeout);
				zoomtimeout=setTimeout(function(){
					$(".zoompercentage").animate({opacity:0},100);
					$(".zoompercentage").css("display","none");
				},500);
				refreshslider();
			},
			slide:function(event,ui){
				refreshslider();
			},
			start:function(event,ui){
				$(".zoompercentage").animate({opacity:1},100);
			}
		});
		$(".pagetoolzoomin").on('click',function(){
			currentzoom=parseFloat($(".zoomslider").slider('option','value'))+5;
			$(".zoomslider").slider("option","value",currentzoom);
		});
		$(".pagetoolzoomout").on('click',function(){
			currentzoom=parseFloat($(".zoomslider").slider('option','value'))-5;
			$(".zoomslider").slider("option","value",currentzoom);
		});
		$(".navitem").click(function(){
			if(isloadna==true){
				isloadna = false;
				if($(this).hasClass("navtrashcan")){
					$(this).find(".trashcan").addClass("active");
				}
				else{
					$(".trashcan").removeClass("active");
				}
				//$(".bodyelements").css("opacity","0");
				//$(".bodyelements").animate({opacity:"0"},200);
				$(".bodyelements").removeClass("show");
				
				$(".navitem").removeClass("active");
				
				$(this).addClass("active");
				var el = this;
				deg=0;
			//	$(".bodyelements").html("");
				$(".pageloadingbackground").clearQueue();
				$(".pageloadingbackground").animate({opacity:"1"},100,function(){$(this).css("visibility","visible")});
				setTimeout(function(){
					if(isloginna==false){
						currentpage = $(el).attr("page");
						$(".bodyelements").load("login.php",function(){
							//$(".bodyelements").animate({opacity:"1"},200);
							$(".bodyelements").addClass("show");
							$(".pageloadingbackground").animate({opacity:"0"},300,function(){$(this).css("visibility","hidden")});
							isloadna=true;
							refreshthis($("body"));
						});
					}
					else{
						loadbodyelements($(el).attr("page"));
					}
				},300);
			}
			else{
				showMyAlert("Still loading page. Please wait...","");
			}
		});
		$(".thehome").trigger("click");
	});
	function loadbodyelements(bodyelements){
		
		$(".bodyelements").load(bodyelements,function(){
			//$(".bodyelements").animate({opacity:"1"},200);
			setTimeout(function(){
				$(".bodyelements").addClass("show");
			},100);
			$(".pageloadingbackground").animate({opacity:"0"},300,function(){$(this).css("visibility","hidden")});
			refreshthis($("body"));
			isloadna=true;
		});
	}
	function downloadfile(filename,tosave){
		var link = document.createElement("a");
		link.download = tosave;
		link.href = window.location.href + filename;
		link.click();
	}
	function filterthedata(el,therows){
		var theval = new String($(el).val().replace(/\s/gi,""));
		var theval = escapeRegExp(theval);
		var pat = new RegExp(theval,"gi");
		$(therows).each(function(){
			$(this).parentsUntil("","tr").addClass("trhidden");
			if($(this).html().replace(/\s/gi,"").match(pat) != null){
				$(this).parentsUntil("","tr").removeClass("trhidden");
			}
		});
		var thecount=0;
		$(therows).each(function(){
			var thiscss = $(this).parentsUntil("","tr").css("display");
			if(thiscss=="table-row"){
				if(thecount % 2 == 0){
					$(this).parentsUntil("","tr").css("background-color","white");
				}
				else{
					$(this).parentsUntil("","tr").css("background-color","rgb(245,245,245)");
				}
				thecount=thecount+1;
			}
		});
	}
	function clickheretologinclicked(){
		window.location.reload();
	}
	function checkifthis(){
		alert("ok");
	}
	if(!window.console) window.console = {};
	var methods = ["log", "debug", "warn", "info", "dir", "dirxml", "trace", "profile"];
	for(var i=0;i<methods.length;i++){
		//console[methods[i]] = function(){};
	}
	var maxpendingrecords = 0;
	var tempmaxpendingrecords = 0;
	var currentpendingrecords = 0;
	setInterval(function(){
		progmisnotification();
	},5000);
	progmisnotification();
	var doctitle = "Online TOR & RFS";
	function progmisnotification(){
		$.get("execute.php?getmaxrecords",function(result){
		//	console.log(parseInt(result) + "          " + maxpendingrecords);
			tempmaxpendingrecords = parseInt(result);
			if(currentpendingrecords < parseInt(result) && currentpendingrecords > 0){
				$(".navexecute").html('Execute <span class="notifcircle" fontbold>'+(parseInt(result)-maxpendingrecords)+'</span>');
				$(".navprogmispending").html('Pending Requests <span class="notifcircle" fontbold>'+(parseInt(result)-maxpendingrecords)+'</span>');
				doctitle="Online TOR & RFS ("+(parseInt(result)-maxpendingrecords)+" New Requests)";
				//maxpendingrecords = parseInt(result);
				currentpendingrecords = parseInt(result);
				starttitleanimation();
				//alert("asdf");
				notifyMe((parseInt(result)-maxpendingrecords) + " new pending requests.");
			}
			//alert("asdf");
		});
	}
	var titlecurrentindex=0;
	var titleinterval;
	var titleanimationisstartna = false;
	function starttitleanimation(){
		if(titleanimationisstartna==false){
			titleinterval = setInterval(function(){
				if(titlecurrentindex >= doctitle.length-1){
					titlecurrentindex=0;
				}
				if(doctitle.substring(titlecurrentindex,titlecurrentindex+1)==" "){
					titlecurrentindex++;
				}
				document.title = doctitle.substring(titlecurrentindex);
				//console.log(doctitle.substring(titlecurrentindex) + "    " + titlecurrentindex);
				titlecurrentindex++;
				
			},400);
			titleanimationisstartna = true;
		}
	}
	function stoptitleanimation(){
		clearInterval(titleinterval);
		doctitle = "Online TOR & RFS";
		document.title = doctitle;
		titleanimationisstartna = false;
		titlecurrentindex=0;
	}
	// function progmisnotification(){
		// $.get("execute.php?getnotification",function(result){
			// if(parseInt(result)>0){
				
			// }
			// else{
				// $(".navprogmispending").html('Pending Requests');
			// }
		// });
	// }
	//setInterval(function(){
	//	progmisnotification();
	//},5000);
	// $(".navprogmispending").on('click',function(){
		// readnotifications();
	// });
	// function readnotifications(){
		// $.get("execute.php?readnotifications",function(result){
			// setTimeout(function(){
				// progmisnotification();
			// },500);
		// });
	// }
	//progmisnotification();
</script>
<?php include('myjs/globaljs.php')?>
<?php include('css/globalcss.css')?>
<?php include('pageloading.php');?>
</body>
</html>