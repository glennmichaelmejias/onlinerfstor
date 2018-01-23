<style>
	.clockbackground{
		background-color:#D9D9D9;
		width:126px;
		height:126px;
		border-radius:10px;
		position:absolute;
		//z-index:1000;
		box-shadow: 0 2px 5px #888888;
	}
	.clockcircle{
		background-image:url("widget/clockcircle.png");
		width:126px;
		height:126px;
		background-repeat:no-repeat;
		background-position:center;
	}
	.clocksecondhand{
		background-image:url("widget/clocksecondhand.png");
		width:126px;
		height:126px;
		background-repeat:no-repeat;
		background-position:center;
		position:absolute;
		top:0;
		left:0;
	}
	.clockminutehand{
		background-image:url("widget/clockminutehand.png");
		width:126px;
		height:126px;
		background-repeat:no-repeat;
		background-position:center;
		position:absolute;
		top:0;
		left:0;
	}
	.clockhourhand{
		background-image:url("widget/clockhourhand.png");
		width:126px;
		height:126px;
		background-repeat:no-repeat;
		background-position:center;
		position:absolute;
		top:0;
		left:0;
	}
</style>
<div class="mywidget clockwidget" style="left:86.5%;top:80px;">
	<div class="clockbackground widgetdrag" cursormove>
		<div class="clockcircle">
			
			<div class="clockminutehand">
				
			</div>
			<div class="clockhourhand">
			
			</div>
			<div class="clocksecondhand">
				
			</div>
		</div>
	</div>
</div>
<link href="widget/widget.css" rel="stylesheet">
<script src="widget/widget.js"></script>
<script type="text/javascript">
	var thehour=0;
	var thehouradd=0;
	initwidget(".clockwidget",function(){});
	setInterval(function(){
		var curtime = new Date();
		$(".clocksecondhand").css("transform","rotate("+(6*curtime.getSeconds())+"deg)");
		$(".clockminutehand").css("transform","rotate("+(6*curtime.getMinutes())+"deg)");
		thehour = curtime.getHours();
		if(thehour>=12){
			thehour=thehour-12;
		}
		thehouradd = ((30/60) * curtime.getMinutes());
		$(".clockhourhand").css("transform","rotate("+((30*thehour)+(thehouradd))+"deg)");
	},500);
</script>