<style>
	.dancebackground{
		//width:200px;
		//height:298px;
		position:fixed;
		//z-index:1000;
		//background:url('widget/dance.png') 0 0;
		width:140px;
		height:109x;
		background:url('widget/miniond.png') 0 0;
	}
</style>
<div class="mywidget dancewidget" style="left:calc(100% - 148px);top:calc(100vh - 129px);">
	<div class="dancebackground widgetdrag" cursormove>
		
	</div>
</div>
<link href="widget/widget.css" rel="stylesheet">
<script src="widget/widget.js"></script>
<script type="text/javascript">
	initwidget(".dancewidget",function(){});
	// var dx;
	// var dy;
	// var isdown=false;
	
	// $(".dancebackground").on('mousedown',function(){
		// var temp = $(this).css("left").replace("px","");
		// if(temp=="auto"){
			// temp = 0;
		// }
		// dx=event.pageX-temp;
		// temp = $(this).css("top").replace("px","");
		// if(temp=="auto"){
			// temp = 0;
		// }
		// dy=event.pageY-temp;
		// isdown=true;
	// }).on('mousemove',function(e){
		// if(isdown){
			// $(".dancebackground").css("left",e.pageX - dx);
			// $(".dancebackground").css("top",e.pageY - dy);
		// }
	// }).on('mouseup',function(){
		// isdown=false;
	// });
	var currentindex=0;
	clearInterval(dancewidget);
	var dancewidget = setInterval(function(){
		if(currentindex % 10 == 0){
			$(".dancebackground").css("background","url('widget/miniond.png') -"+(140*currentindex)+"px 0");
		}
		else{
			$(".dancebackground").css("background","url('widget/miniond.png') -"+(140*currentindex)+"px 109px");
		}
		currentindex=currentindex+1;
	},120)
</script>