<script type="text/javascript">
	function editcurrentbuclicked(requesttype){
		$.get("globalphp.php?getcurrentbus",function(result){
			result = result.my2darrdecode();
			var ids = result[0];
			var bus = result[1];
			var menus = [];
			var menufunctions = [];
			var a = 0;
			for(a = 0;a < ids.length;a++){
				menus.push(bus[a]);
				menufunctions.push(editcurrentbuchange(ids[a],requesttype));
			}
			showMyPopupMenu(menus,menufunctions);
		});
	}
	function editcurrentbuchange(buid,requesttype){
		return function(){
			$.get("globalphp.php?changecurrentbu&buid="+buid,function(result){
				if(requesttype=="RFS"){
					loadbodyelements("request.php");
				}
				else if(requesttype=="TOR"){
					loadbodyelements("requesttor.php");
				}
			});
		}
	}
	var modalshowapprove = new MyModal();
	function showapproveclicked(requestid){
		modalshowapprove.show("Approved Requests");
		$.get("iad.php?showapprovetor&requestid="+requestid,function(result){
			modalshowapprove.body(result);
		});
	}
	function showapprovecloseclicked(){
		modalshowapprove.close();
	}
	var modalprint = new MyModal();
	function showprint(formtype,requestid,requestnumber){
		modalprint.show("Print");
		modalprint.showprogressicon('url("img/printing.gif")');
		if(formtype=="RFS"){
			$.get("request.php?printthisform&requestid="+requestid,function(result){
				setTimeout(function(){
					modalprint.body(result);
					downloadfile("printing\\tempxlsx\\"+requestid+".xlsx","RFS Form ("+pad(requestnumber,4,0)+")");
					modalprint.close();
				},1500);
			});
		}
		else if(formtype=="TOR"){
			$.get("requesttor.php?printthisform&requestid="+requestid,function(result){
				setTimeout(function(){
					modalprint.body(result);
					downloadfile("printing\\tempxlsx\\"+requestid+".xlsx","TOR Form ("+pad(requestnumber,4,0)+")");
					modalprint.close();
				},1500);
			});
		}
	}
	var modalviewattachment = new MyModal();
	function btnviewattachmentclicked(requestnumber){
		modalviewattachment.showcustom("Attachment","60%");
		$.get("globalphp.php?viewattachment&requestnumber="+requestnumber,function(result){
			modalviewattachment.body(result);
		});
	}
	function downloadattachment(filenumber,filename){
		var prevbody = modalviewattachment.getbody();
		modalviewattachment.showloading();
		downloadfile("attachedfiles\\"+filenumber,filename);
		modalviewattachment.body(prevbody);
	}
	function btnviewattachmentcloseclicked(){
		modalviewattachment.close();
	}
	function btnviewattachmentdownloadaszipclicked(requestnumber){
		var prevbody = modalviewattachment.getbody();
		modalviewattachment.showloading();
		$.get("globalphp.php?downloadallaszip&requestnumber="+requestnumber,function(result){
			downloadfile("attachedfiles\\compressed\\"+requestnumber+".zip",requestnumber+".zip");
			modalviewattachment.body(prevbody);
		});
	}
</script>