<style>
	.calendarbackground{
		width:126px;
		height:126px;
		border-radius:10px;
		position:absolute;
		//z-index:1000;
		box-shadow: 0 2px 5px #888888;
		background-image:url("widget/calendarbackground.png");
	}
	.calendardayname{
		color:white;
		font-size:18px;
		font-family:"segoe ui";
		margin-top:13px;
		cursor:default;
		text-align:center;
	}
	.calendardayval{
		color:#555;
		font-family:"segoe ui";
		font-size: 70px;
		margin-top: -10px;
		text-align:center;
	}
</style>
<div class="mywidget widgetcalendar" style="left:86.6%;top:230px;">
	<div class="calendarbackground widgetdrag" cursormove>
		<div class="calendardayname">WEDNESDAY</div>
		<div class="calendardayval">28</div>
	</div>
</div>
<link href="widget/widget.css" rel="stylesheet">
<script src="widget/widget.js"></script>
<script type="text/javascript">
//	var dx;
//	var dy;
//	var isdown=false;
	initwidget(".widgetcalendar",function(){});
	var thedate = new Date();
	var daynames = new Array("SUNDAY","MONDAY","TUESDAY","WEDNESDAY","THURSDAY","FRIDAY","SATURDAY");
	$(".calendardayname").html(daynames[thedate.getDay()]);
	var theday=parseInt(thedate.getDate());
	$(".calendardayval").html((theday.toString()<10)?'0'+theday.toString():theday.toString());
	// $(".calendarbackground").on('mousedown',function(){
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
			// $(".calendarbackground").css("left",e.pageX - dx);
			// $(".calendarbackground").css("top",e.pageY - dy);
		// }
	// }).on('mouseup',function(){
		// isdown=false;
	// });
</script>