<?php
	if(isset($_GET['getcurrentbus'])){
		include 'db/dbconnect.php';
		$bu = initbu();
		$query = mysqlm_query("select buid from buheadrole where userid=".getuserid());
		$arr = [];
		$arr2 = [];
		while($row=mysqlm_fetch_array($query)){
			$arr[] = $row['buid'];
			$arr2[] = $bu[$row['buid']];
		}
		echo my2darrencode($arr,$arr2);
		breakhere($con);
	}
	elseif(isset($_GET['changecurrentbu'])){
		include 'db/dbconnect.php';
		$buid = myGET('buid');
		setcurrentbu($buid);
		breakhere($con);
	}
	elseif(isset($_GET['viewattachment'])){
		include 'db/dbconnect.php';
		$requestnumber = mySTR("RFS-".myGET('requestnumber'));
		$query = mysqlm_query("select filename,filenumber from attachedfiles where requestnumber=".$requestnumber);
		echo '<div mygroup col12>
				<div scrollable style="height:344px">
					<table class="tablefiles" mytable col12>
						<tr>
							<th col6>File name</th>
							<th col2>File size</th>
							<th col2>File type</th>
							<th col1 centertext>Action</th>
						</tr>
						<tbody class="tablefilesdata">';
							while($row=mysqlm_fetch_array($query)){
								echo '<tr trhoverable>
										<td>'.$row['filename'].'</td>
										<td>'.intval(filesize('attachedfiles/'.$row['filenumber'])/1024).'KB</td>
										<td>'.mime_content_type('attachedfiles/'.$row['filenumber']).'</td>
										<td centertext><div iconbtn title="download file" onclick="downloadattachment(\''.$row['filenumber'].'\',\''.$row['filename'].'\')"><i class="fa fa-download"/></div></td>
									</tr>';
							}
					echo'</tbody>
					</table>
				</div>
			</div>';
		echo '<div mygroup col12 nobordertop>
				<div buttongroup col4 floatright>
					<button col12 onclick="btnviewattachmentdownloadaszipclicked(\'RFS-'.myGET('requestnumber').'\')"> <i class="fa fa-file-archive-o"/> Download all as zip</button>
					<!--<button col5 onclick="btnviewattachmentcloseclicked()"> <i class="fa fa-times"/> Close</button>-->
				</div>
			</div>';
		breakhere($con);
	}
	elseif(isset($_GET['downloadallaszip'])){
		include 'db/dbconnect.php';
		$zip = new ZipArchive();
		$requestnumber = myGET('requestnumber');
		$query = mysqlm_query("select filename,filenumber from attachedfiles where requestnumber=".mySTR($requestnumber)." order by filename");
		$filename = "attachedfiles/compressed/".$requestnumber.".zip";
		if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$prevfilename="";
		$filenamecount=0;
		while($row=mysqlm_fetch_array($query)){
			if($row['filename']==$prevfilename){
				$filenamecount++;
				$filename = explode(".",$row['filename']);
				$filename[count($filename)-2] = $filename[count($filename)-2]."_".$filenamecount;
				$filename = implode(".",$filename);
				$zip->addFile("attachedfiles/".$row['filenumber'] ,$filename);
			}
			else{
				$filenamecount=0;
				$zip->addFile("attachedfiles/".$row['filenumber'] ,$row['filename']);
			}
			$prevfilename = $row['filename'];
		}
		$zip->close();
		breakhere($con);
	}
?>