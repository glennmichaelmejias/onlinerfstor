<?php
	// $thefile = file_get_contents("notestext.php");
	// if(strlen($thefile) == 0){
		// file_put_contents("notestext.php",$_POST['notestext']);
		// die();
	// }
	// $thefile = explode($thefile,"<mynotesseparator>");
	// $fileid = $_POST['id'];
	// $thenote = $_POST['notestext'];
	// $arr = array();
	// $arr[] = "asdf";
	// $arr[] = "qwer";
	// echo implode(",",$arr);
	// if(count($thefile)>0){
		// $thefile[] = $thenote;
		// $thestring = implode("<mynotesseparator>",$thefile);
	// }
	// else{
		// $thestring = $thenote;
	// }
	
	
	// //for($a=0;$a<count($thefile);$a++){
		
	// //}
	$thestring = $_POST['notestext'];
	file_put_contents("notestext.php",$thestring);
	
?>