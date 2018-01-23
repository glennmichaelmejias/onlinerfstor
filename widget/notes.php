<style>
	.notesbackground{
		position:absolute;
		box-shadow: 0 2px 5px #888888;
		border-radius:10px;
		overflow:hidden;
	}
	.notestitle{
		width:inherit;
		height:28px;
	}
	.notestextarea{
		width:200px;
		border:none;
		background-color:rgba(0,0,0,0);
		font-family:"Century Gothic";
		font-size:13px;
		padding:10px;
	}
	.notesbtn{
		padding:7px;
		padding-left:10px;
		padding-right:10px;
		color:#777;
		cursor:pointer;
		display:inline-block;
	}
	.notesbtn:hover{
		background-color:rgba(0,0,0,.05);
	}
	.notechangecolor{
		font-size:12px;
	}
	[notescolor=yellow] .notesbackground,.notescoloryellow{
		background-color:#F8F7B6;
	}
	[notescolor=yellow] .notestext{
		background-color:#FDFDC9;
	}
	[notescolor=purple] .notesbackground,.notescolorpurple{
		background-color:#D4CDF3;
	}
	[notescolor=purple] .notestext{
		background-color:#DCD7FE;
	}
	[notescolor=pink] .notesbackground,.notescolorpink{
		background-color:#F1C3F1;
	}
	[notescolor=pink] .notestext{
		background-color:#F5D1F5;
	}
	[notescolor=green] .notesbackground,.notescolorgreen{
		background-color:#C5F7C1;
	}
	[notescolor=green] .notestext{
		background-color:#D0FDCA;
	}
	[notescolor=blue] .notesbackground,.notescolorblue{
		background-color:#C9ECF8;
	}
	[notescolor=blue] .notestext{
		background-color:#D7F1FA;
	}
	[notescolor=white] .notesbackground,.notescolorwhite{
		background-color:#F5F5F5;
	}
	[notescolor=white] .notestext{
		background-color:#FEFEFE;
	}
	.notescolors{
		width:15px;
		height:15px;
		border-radius:5px;
		border:1px solid #ddd;
		display:inline-block;
		margin-left:2px;
	}
	.notestextcolortext{
		margin-left: 5px;
		position: absolute;
		height: 12px;
		padding: 3px;
	}
</style>
<script type="text/javascript">
	$.get("widget/notestext.php",function(result){
		result = result.split("<mynotesseparator>");
		var a = 0;
		for(a=0;a < result.length;a++){
			addnewnote(result[a]);
		}
	});
	function addnewnote(strtext){
		var theind = $(".noteswidget").length;
		strtext = strtext.split("<mynotespropsseparator>");
		var thenotecolor = strtext[5];
		var thestrnote = strtext[4];
		if(thestrnote==undefined){
			thestrnote = "New Note";
		}
		if(thenotecolor==undefined){
			thenotecolor = "yellow";
		}
		$(".bodyelements").append('<div class="mywidget noteswidget noteswidgetind'+theind+'" notescolor="'+thenotecolor+'" showsettings="false" style="left:'+strtext[0]+';top:'+strtext[1]+';">'+
			'<div class="notesbackground" title="Your note is visible to everyone.">'+
				'<div class="notestitle widgetdrag" cursormove>'+
					'<div class="notesbtn noteaddnew" noteaddind="'+theind+'" onclick="addnewnote(\'572.453px<mynotespropsseparator>92px<mynotespropsseparator>150px<mynotespropsseparator>150px<mynotespropsseparator>New Note\')"><i class="fa fa-plus"/></div>'+
					'<div class="notesbtn notechangecolor" onclick="changenotecolor(this)"><i class="fa fa-paint-brush"/></div>'+
					'<div class="notesbtn noteremove" onclick="deletenote(this)" floatright><i class="fa fa-remove"/></div>'+
				'</div>'+
				'<div class="notestext">'+
					'<textarea spellcheck="false" nooutline class="notestextarea" style="width:'+strtext[2]+';height:'+strtext[3]+'" onkeyup="notestextareakeyup(this)" textind="'+theind+'">'+thestrnote+'</textarea>'+
				'</div>'+
			'</div>'+
		'</div>');
		initwidget(".noteswidgetind"+theind,function(){notestextareakeyup()});
		$(".notestextarea").on('keyup',function(){
			
		});
		var thetimeout;
		$(".notestextarea").resizable({
			resize: function(){
				clearTimeout(thetimeout);
				thetimeout = setTimeout(function(){
							notestextareakeyup();
						},500);
			}
		});
		notestextareakeyup();
	}
	function changenotecolor(el){
		var options = new Array();
		var functions = new Array();
		options.push('<div class="notescolors notescoloryellow" notescolor="yellow"></div><span class="notestextcolortext">Yellow</span>');
		options.push('<div class="notescolors notescolorblue" notescolor="blue"></div><span class="notestextcolortext">Blue</span>');
		options.push('<div class="notescolors notescolorgreen" notescolor="green"></div><span class="notestextcolortext">Green</span>');
		options.push('<div class="notescolors notescolorwhite" notescolor="white"></div><span class="notestextcolortext">White</span>');
		options.push('<div class="notescolors notescolorpurple" notescolor="purple"></div><span class="notestextcolortext">Purple</span>');
		options.push('<div class="notescolors notescolorpink" notescolor="pink"></div><span class="notestextcolortext">Pink</span>');
		
		functions.push(function(){changethisnotecolor(el,"yellow")});
		functions.push(function(){changethisnotecolor(el,"blue")});
		functions.push(function(){changethisnotecolor(el,"green")});
		functions.push(function(){changethisnotecolor(el,"white")});
		functions.push(function(){changethisnotecolor(el,"purple")});
		functions.push(function(){changethisnotecolor(el,"pink")});
		
		showMyPopupMenu(options,functions,
			function(){
				
			}
		);
	}
	function changethisnotecolor(el,thecolor){
		$(el).parentsUntil("",".noteswidget").attr("notescolor",thecolor);
		notestextareakeyup();
	}
	function deletenote(el){
		if($(".notestextarea").length>1){
			$(el).parentsUntil("",".noteswidget").remove();
			notestextareakeyup();
		}
	}
	function notestextareakeyup(){
		var thenotes = new Array();
		var thebounds = new Array();
		var arr = new Array();
		$(".notestextarea").each(function(){
			var theleft = $(this).parentsUntil("",".noteswidget").css("left");
			var thetop = $(this).parentsUntil("",".noteswidget").css("top");
			var thewidth = $(this).css("width");
			var theheight = $(this).css("height");
			var thenotecolor = $(this).parentsUntil("",".noteswidget").attr("notescolor");
			thenotes.push(theleft + "<mynotespropsseparator>" + thetop + "<mynotespropsseparator>" + thewidth + "<mynotespropsseparator>" + theheight + "<mynotespropsseparator>" + $(this).val() + "<mynotespropsseparator>" + thenotecolor);
		});
		thenotes = thenotes.join("<mynotesseparator>");
		$.post("widget/notesettext.php",{notestext:thenotes},function(result){
			
		});
	}
</script>