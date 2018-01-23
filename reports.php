<?php
	if(isset($_GET['loadtable'])){
		include 'db/dbconnect.php';
		$fromdate = mySTRget('fromdate');
		$todate = mySTRget('todate');
		$filterbu = myGET('filterbu');
		$users = initusers();
		$requesttype=(myGET('showrfs')=="checked"?1:2);
		//echo $requesttype;
		echo '<table mytable fullwidth>
				<tr>
					<th col1>Transaction Date</th>
					<th centertext col1>Control Number</th>
					<th col2>Request Type</th>
					<th col2>Requested By</th>
					<th col2>BU Head</th>
					<th col2>Approved By</th>
					<th col2>Executed</th>
					<th col1 centertext>Status</th>
				</tr>';
		$query = mysqlm_query("select
								DATE_FORMAT(datetoday,'%b %d, %Y') as tdate,
								if(IFNULL(`approvedid`,'Pending')='Pending','Pending',
									if(IFNULL(`executed`,'Pending')='Pending','Pending',
										if(IFNULL(a.buheadid,'Pending')='Pending','Pending','Approved')
									)
								  ) as rfsstatus,
								".($requesttype==1?"concat(themode,' ',requesttype) as themode,":"b.tortype as themode,")."
								LPAD(requestnumber,4,0) as controlnumber,
								bu.businessunit as thebu,
								if(ifnull(a.softsysstatus,'Pending')='Pending','Pending',
									if(ifnull(a.executed,'Pending')='Pending','Pending','Approved')
								) as softsysstatus,
								a.typeofrequest as typeofrequest,
								a.userid as requestedby,
								a.iadstatus as iad,
								a.approvedid as approvedby,
								a.buheadid as buhead,
								a.executed as executedby
								from requests a,".($requesttype==1?"typeofrequest b, requestmode c,":"tortypes b,").
												"tblcompany com,tblbusinessunit bu,users u
								where (".($requesttype==1?"(a.typeofrequest = b.id and a.requestmode = c.id and a.thetype=1)":
														"(a.tortype = b.id)").
										") and u.businessunitid = bu.id and com.id = bu.companyid and u.id = a.userid
								and (bu.businessunit like '%".$filterbu."%' and LENGTH('".$filterbu."') > 0)
								and (DATE_FORMAT(datetoday,'%Y-%m-%d') between STR_TO_DATE(".$fromdate.",'%m/%d/%Y') and STR_TO_DATE(".$todate.",'%m/%d/%Y'))
								order by a.datetoday asc;");
		while($row=mysqlm_fetch_array($query)){
			$thestatus;
			$requestedby = $users[$row['requestedby']];
			$buhead = ($row['buhead']==""?"":$users[$row['buhead']]);
			$approvedby = ($row['approvedby']==""?"":$users[$row['approvedby']]);
			$executedby = ($row['executedby']==""?"":$users[$row['executedby']]);
			($row['typeofrequest']==7?$thestatus=$row['softsysstatus']:$thestatus=$row['rfsstatus']);
			echo '<tr trhoverable>
					<td>'.$row['tdate'].'</td>
					<td centertext colorred fontbold>'.$row['controlnumber'].'</td>
					<td>'.$row['themode'].'</td>
					<td>'.$requestedby.'</td>
					<td>'.$buhead.'</td>
					<td>'.$approvedby.'</td>
					<td>'.$executedby.'</td>
					<td class="tdrequestsstatus" '.$thestatus.' centertext>'.$thestatus.'</td>
					<!--<td centertext><div iconbtn onclick="iconrequestopenclicked(\''.'\')" title="open"><i class="fa fa-list"/></div></td>-->
				</tr>';
		}
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
<div class="myframe">
	<h3 fontbold><span color1>Reports</span></h3>
	<div coli col12>
		<div mytoolsgroup col6 displaytablecell nopadding>
			<div mytoolsgroup displaytablecell noborder col4>
				<div coli fontbold>Date From:</div>
				<input title="Press Enter" id="from" type="text" col4/>
				<div coli marginedleft fontbold>To:</div>
				<input title="Press Enter" id="to" type="text" col4/>
			</div>
			<div mytoolsgroup displaytablecell noborder hasborderleft col3>
				<div class="selectbusinessunit" myselect col6 placeholder="Business unit">
					<?php
						$query = mysqlm_query("select businessunit,id from tblbusinessunit where active='1' order by businessunit");
						echo '<div myselectoption value="">All Business Unit</div>';
						while($row=mysqlm_fetch_array($query)){
							echo '<div myselectoption value="'.$row['id'].'">'.$row[0].'</div>';
						}
					?>
				</div>
			</div>
		</div>
		<div mytoolsgroup col5 displaytablecell noborderleft>
			<!--<div coli col3 marginedleft><div class="mycheckbox chkshowapproved" value="checked" coli></div><div coli marginedleft> Show approved</div></div>-->
			<div coli col2 marginedleft><div class="mycheckbox chkshowrfs checkboxrequesttype" value="checked" coli></div><div coli marginedleft> RFS</div></div>
			<div coli col2 marginedleft><div class="mycheckbox chkshowtor checkboxrequesttype" coli></div><div coli marginedleft> TOR</div></div>
		</div>
	</div>
	<div style="height:60vh;overflow-y:scroll">
		<div class="requeststable" mygroup col12 bordertopwhite>

		</div>
	</div>
	<div displaytable fullwidth><button marginedall floatright paddingedleft paddingedright>Print</button></div>
</div>
<script type="text/javascript">
	var filterbu = "";
	$(".selectbusinessunit").myselect(function(elinputval){
		filterbu = elinputval;
		loadrequests();
	});
	$(".monthselector").monthselector(function(){
		loadrequests();
	});
	$(".checkboxrequesttype").checkboxradio(function(){
		//	$(".chkshowrfs").mycheckboxvalue();
			loadrequests();
			
		}
	);
//	$(".chkshowrfs").mycheckbox();
//	$(".chkshowtor").mycheckbox();
	loadrequests();
	function loadrequests(){
		var fromdate = $("#from").val();
		var todate = $("#to").val();
		$.get("reports.php?loadtable&fromdate="+fromdate+
							"&todate="+todate+
							"&filterbu="+filterbu+
							"&showrfs="+$(".chkshowrfs").mycheckboxvalue()+
							"&showtor="+$(".chkshowtor").mycheckboxvalue(),function(result){
			$(".requeststable").html(result);
		});
	}
</script>
<script type="text/javascript">
	var dateFormat = "mm/dd/yy",
	from = $("#from").datepicker({
			defaultDate: "-0m",
			changeMonth: true,
			numberOfMonths: 1
		})
	.on( "change", function(){
		to.datepicker( "option", "minDate", getDate( this ) );
		loadrequests();
	}),
	to = $("#to").datepicker({
		defaultDate: "+0m",
		changeMonth: true,
		numberOfMonths: 1
	})
	.on( "change", function(){
		from.datepicker( "option", "maxDate", getDate( this ));
		loadrequests();
	});
    function getDate(element){
		var date;
		try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		}
		catch(error){
			date = null;
		}
		return date;
    }
  </script>