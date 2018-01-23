$(".mywidget").css("position","absolute");
$(".mywidget").hover(function(){
	if($(this).attr("showsettings")!="false"){
		$(this).find(".mywidgetsettings").remove();
		$(this).append('<div class="mywidgetsettings">'+
							'<i class="fa fa-wrench" style="font-size:18px;position:absolute;left:0;top:0;"/>'+
						'</div>');
	}
	
},function(){
	$(this).find(".mywidgetsettings").remove();
});
var dx;
var dy;
var isdown=false;
var widgetinterval;
function initwidget(strwidget,onmouseup){
	var theel = $(strwidget);
	$(theel).find(".widgetdrag").on('mousedown',function(){
		clearInterval(widgetinterval);
		theel = $(this).parentsUntil("",".mywidget");
		dx = curx - $(theel).css("left").replace("px","");
		dy = cury - $(theel).css("top").replace("px","");
		widgetinterval = setInterval(function(){
			$(theel).css({"left":curx - dx,"top":cury-dy});
		},20);
		isdown=true;
		$(theel).on('mouseup',function(){
			clearInterval(widgetinterval);
			isdown=false;
			onmouseup();
		});	
	});
	
}