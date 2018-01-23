<script type="text/javascript">
	$.fn.myscrollable = function(){
		var thisparent = this;
		$(this).wrapInner('<div class="myscrollablecontent"></div>');
		$(this).css("display","flex");
		var thehtml = $('<div class="popupscrollbarbackground">'+
							'<div class="popupscrollbarscroller" style="display:table">'+
								'<div style="display:table-cell;vertical-align:middle;font-size:11px;color:#eee;text-align:center"><i class="fa fa-bars" style="background-color: #999;height: 9px;width: 6px;overflow: hidden;margin-left: -1px;" aria-hidden="true"></i></div>'+
							'</div>'+
						'</div>');
		$(this).prepend(thehtml);
		var dy=0;
		$(this).find(".popupscrollbarscroller").on('mousedown',function(event){
			//if(event.target != this) return;
			var thisel=this;
			var theparent=$(thisel).parentsUntil("",".popupscrollbarbackground");
			var thetemp=$(theparent).offset().top;
			dy = (event.pageY-thetemp) - $(this).css("top").replace("px","");
			$(this).addClass("active");
			$(document).on('mousemove',function(event){
				var thepercent=0;
				var scrollbarheight = parseInt($(theparent).css("height").replace("px","")) - parseInt($(thisel).css("height").replace("px",""));
				var elementscrollheight = parseInt($(theparent).parentsUntil("","div").find(".myscrollablecontent")[0].scrollHeight) - parseInt($(theparent).css("height").replace("px",""));
				var scrollbarvalue = (event.pageY-dy)-thetemp;
				thepercent = (100/scrollbarheight) * scrollbarvalue;
				elementscrollheight = (elementscrollheight/100);
				scrollbarvalue = elementscrollheight * thepercent;
				if(scrollbarvalue >= 0 && thepercent <= 100){
					$(thisel).css("top",((event.pageY-dy)-thetemp));
					//console.log($(theparent).parentsUntil("",thisparent).find(".myscrollablecontent").html());
					$(theparent).parentsUntil("","div").find(".myscrollablecontent").scrollTop(scrollbarvalue);
				}
			}).on('mouseup',function(e){
				$(this).unbind();
				$(thisel).removeClass("active");
			});
		});
	}
	$.fn.addmaximize = function(){
		var thisparent = this;
		$(this).append('<i class="fa fa-expand" aria-hidden="true"></i>');
	}
	$.fn.maximizable = function(){
		var thissel = this;
		$(thissel).each(function(){
			var thebutton = $('<i class="fa fa-expand maximizebutton" ismaximized="false" style="font-size:15px" aria-hidden="true"></i>');
			var thissel2 = this;
			var prevheight = $(thissel2).css("height");
			var towrap = $('<div style="position:fixed;padding:1%;top:0;left:0;z-index:100;width:98%;height:100%;background-color:white"></div>');
			$(thebutton).on('click',function(){
				if($(thebutton).attr("ismaximized")=="false"){
					$(thissel2).wrap(towrap);
					$(thebutton).attr("ismaximized","true");
					$(thissel2).css("height","98%");
				}
				else{
					$(thissel2).css("height",prevheight);
					$(thissel2).unwrap();
					$(thebutton).attr("ismaximized","false");
				}
			});
			$(this).prepend(thebutton);
		});
	}
	$.fn.filterbar = function(){
		var thisel = this;
		$(thisel).on('keyup',function(){
			
		})
	}
	$.fn.tdeditable = function(thefunction2){
		if(elhasclick(this)) return;
		$(this).attr("title","Click to edit value");
		$(this).css("cursor","text");
		$(this).on('mouseenter',tdordersthevaluesonmouseenter = function(){
			var id = $(this).attr("tdid");
			var thisel = this;
			// $(this).html('<div style="position:relative;height:100%;width:100%">'+
							// '<div style="width:100%;height:100%;display:inline-block">'+
								// $(this).attr("value")+
							// '</div>'+
							// '<div class="theditbutton">'+
								// '<i class="fa fa-pencil tdpencileditor" title="edit value"></i>'+
							// '</div>'+
						// '</div>');
			if(elhasclick(this)) return;
			$(thisel).on('click',function(){
				var strselector = thisel;
				if($(thisel).attr("tdeditabletextboxfalse")==undefined){
					$(strselector).unbind();
					$(strselector).html('<input class="txttdedittext" col12 style="text-align:center;" value="'+($(strselector).attr("value"))+'"/>')
						.css({"padding-top":"3px","padding-bottom":"3px"});
					$(strselector).find(".txttdedittext").focus().select();
					$(strselector).find(".txttdedittext").on('blur',functionmanaugedit = function(){
						var thefunction = $(strselector).attr('functionclick');
						$(strselector).attr("value",$(strselector).find(".txttdedittext").val());
						if(thefunction2!=undefined){
							thefunction2(thisel);
						}
						var theeditedvalue = $(strselector).attr("value");
						$(strselector).html(theeditedvalue);
						$(strselector).css("padding","7px");
						$(strselector).bind('mouseenter',tdordersthevaluesonmouseenter);
						$(strselector).bind('mouseleave',tdordersthevaluesonmouseleave);
						eval(thefunction);
					}).on('keypress',function(event){
						if(event.keyCode==13){
							functionmanaugedit();
						}
					});
				}
				else{
					var thefunction = $(strselector).attr('functionclick');
					eval(thefunction);
				//
				}
			});
		}).on('mouseleave',tdordersthevaluesonmouseleave = function(){
			$(this).html($(this).attr("value"));
		});
	}
	$.fn.checkboxradio = function(functiononclick){
		var thecheckbox = this;
		var thisparent = this;
		$(thisparent).mycheckbox();
		if(elhasclick(this)) return;
		$(thecheckbox).on('click',function(){
			$(thisparent).each(function(){
				$(this).mycheckboxset("unchecked");
			});
			$(this).mycheckboxset("checked");
			if(functiononclick != undefined){
				functiononclick();
			}
		});
	}
	$.fn.mycheckboxset = function(theval){
		if(theval=="checked"){
			$(this).html('<img src="img/mycheckboxchecked.png"/>');
			$(this).attr("value","checked");
		}
		else{
			$(this).html('<img src="img/mycheckboxunchecked.png"/>');
			$(this).attr("value","unchecked");
		}
	}
	function elhasclick(el){
		var ev = $._data(el,'events');
		if(ev && ev.click) return true;
	}
	$.fn.mycheckbox = function(functiononclick){
		var el = this;
		$(el).each(function(){
			var theval = $(this).attr("value");
			if(theval=="checked"){
				$(this).html('<img src="img/mycheckboxchecked.png"/>');
			//	$(this).html('<i class="fa fa-check"/>');
			}
			else{
				$(this).html('<img src="img/mycheckboxunchecked.png"/>');
			}
			var thisparent=this;
			if(elhasclick(this)) return;
			$(this).on('click',function(){
				theval=$(this).attr("value");
				if(theval=="checked"){
					$(this).html('<img src="img/mycheckboxunchecked.png"/>')
					//$(this).html('<img src="img/mycheckboxunchecked.png"/>')
					.attr("value","unchecked");
				}
				else{
					$(this).html('<img src="img/mycheckboxchecked.png"/>')
					//$(this).html('<i class="fa fa-check"/>')
					.attr("value","checked");
				}
				if(functiononclick != undefined){
					functiononclick(this);
				}
			});
		});
	}
	$.fn.mycheckboxonclick = function(thefunction){
		var el = this;
		if(elhasclick(this)) return;
		$(this).on('click',thefunction);
	}
	$.fn.mycheckboxvalue = function(){
		var el = this;
		//console.log($(el).html());
		return $(el).attr("value");
	}
	$.fn.myradiogetchecked=function(){
		var el=this;
		var thevalueid=0;
		$(el).each(function(){
			var thischeckboxvalue = $(this).mycheckboxvalue();
			if(thischeckboxvalue=="checked"){
				thevalueid = $(this).attr("thevalueid");
				return;
			}
		});
		return thevalueid;
	}
	$.fn.myradiogetcheckedvalue=function(){
		var el=this;
		var thevalueid=0;
		$(el).each(function(){
			var thischeckboxvalue = $(this).mycheckboxvalue();
			if(thischeckboxvalue=="checked"){
				thevalueid = $(this).attr("thevalue");
				return;
			}
		});
		return thevalueid;
	}
	$.fn.addloading = function(){
		$(this).load("pageloading2.php");
		//$(".bodyelements").animate({opacity:"1"},200);
		$(".bodyelements").addClass("show");
	}
	$.fn.dateselector = function(thefunction){
		var el = this;
		if(elhasclick(this)) return;
		var dd = today.getDate();
		dd = (today.getMonth()+1)+"/"+dd+"/"+today.getFullYear();
		//$(this).attr("datevalue",dd);
		$(this).on('click',function(){
			var menus = [];
			var functions = [];
			menus.push('<i class="fa fa-calendar"/> Today');
			menus.push('<i class="fa fa-calendar"/> Yesterday');
			menus.push('<i class="fa fa-calendar"/> Select date');
			menus.push('<i class="fa fa-calendar"/> From the beginning of time');
			functions.push(function(){
				$(el).html("Today");
				$(el).attr("value",dd);
				$(el).attr("datevalue",dd);
				thefunction();
			});
			functions.push(function(){
				$(el).html("Yesterday");
				var dd = today.getDate();
				dd = (today.getMonth()+1)+"/"+(dd-1)+"/"+today.getFullYear();
				$(el).attr("value",dd);
				$(el).attr("datevalue",dd);
				thefunction();
			});
			functions.push(function(){
				$("body").append('<input class="dateselectordate" style="left:'+(parseInt(event.pageX)-80)+'px;top:'+(parseInt(event.pageY)-100)+'px" displayhidden type="text"/>');
				$(".dateselectordate").datepicker();
				$(".dateselectordate").trigger('focus');
				$(".dateselectordate").on('change',function(){
					$(el).attr("value",$(this).val());
					$(el).html($(this).val());
					$(".dateselectordate").remove();
					$(el).attr("datevalue",$(this).val());
					thefunction();
				});
			});
			functions.push(function(){
				$(el).html("From the beginning of time");
				$(el).attr("value","all");
				$(el).attr("datevalue","all");
				thefunction();
			});
			showMyPopupMenu(menus,functions,function(){});
		});
		
	}
	// var pagination = '<div fullwidth displaytable>\
							// <div buttongroup class="mypagination" marginedtop floatright>\
								// <button class="paginationprev" paddingedleftright>Prev</button>\
								// <button class="paginationpage active" paddingedleftright>1</button>\
								// <button class="paginationpage" paddingedleftright>2</button>\
								// <button class="paginationpage" paddingedleftright>3</button>\
								// <button class="paginationnext" paddingedleftright>Next</button>\
							// </div>\
						// </div>';
	var pagination = '<button class="paginationprev" paddingedleftright>Prev</button>\
					<button class="paginationpage active" paddingedleftright>1</button>\
					<button class="paginationpage" paddingedleftright>2</button>\
					<button class="paginationpage" paddingedleftright>3</button>\
					<button class="paginationnext" paddingedleftright>Next</button>';
	$.fn.mypagination = function(maxrecords,thefunction){
		var el = this;

		var currentpage = 1;
		$(this).html(pagination);
		$(this).find(".paginationpage").on('click',function(){
		//	alert("asdf");
			//if(elhasclick(this)) return;
			$(el).find(".paginationpage").removeClass("active");
			$(this).addClass("active");
			currentpage = $(this).html();
			thefunction(parseInt(currentpage)-1);
		});
		$(this).find(".paginationnext").on('click',function(){
			//if(elhasclick(this)) return;
			if(currentpage<(maxrecords/12)){
				currentpage++;
				var hasrecord = false;
				$(el).find(".paginationpage").each(function(){
					if($(this).html()==currentpage){
						hasrecord = true;
						$(el).find(".paginationpage").removeClass("active");
						$(this).addClass("active");
					}
				});	
				if(hasrecord==false){
					$(el).find(".paginationpage").each(function(){
						$(this).html(parseInt($(this).html())+1);
					})
				}
				thefunction(parseInt(currentpage)-1);
			}
		});
		$(this).find(".paginationprev").on('click',function(){
			//if(elhasclick(this)) return;
			if(currentpage>1){
				currentpage--;
				var hasrecord = false;
				$(el).find(".paginationpage").each(function(){
					if($(this).html()==currentpage){
						hasrecord = true;
						$(el).find(".paginationpage").removeClass("active");
						$(this).addClass("active");
					}
				});	
				if(hasrecord==false){
					$(el).find(".paginationpage").each(function(){
						$(this).html(parseInt($(this).html())-1);
					})
				}
				thefunction(parseInt(currentpage)-1);
			}
		});
		// $(this).on('click',function(){
			// var menus = [];
			// var functions = [];
			// var a = 0;
			// for(a = 0;a < (maxrecords/12)-1;a++){
				// menus.push('Page '+(a+1));
			// }
			// for(a = 0;a < (maxrecords/12);a++){
				// functions.push(logItRunner(a));
			// }
			// showMyPopupMenu(menus,functions,function(){});
		// });
		// function logItRunner(arg){
			// return function(){
				// thefunction(arg);
				// $(el).html("Page " + (arg+1));
			// };
		// }
	}
	$.fn.monthselector = function(thefunction){
		var el = this;
		if(elhasclick(this)) return;
		var dd = today.getDate();
		dd = "01/"+(today.getMonth()+1)+"/"+today.getFullYear();
		$(this).attr("datevalue",dd);
		$(this).on('click',function(){
			var menus = [];
			var functions = [];
			menus.push('<i class="fa fa-clock-o"/> This month');
			menus.push('<i class="fa fa-clock-o"/> Last month');
			menus.push('<i class="fa fa-clock-o"/> Select month');
			menus.push('<i class="fa fa-clock-o"/> All Months');
			functions.push(function(){
				$(el).html('<i class="fa fa-filter"/> This month');
				$(el).attr("value",dd);
				$(el).attr("datevalue",dd);
				thefunction();
			});
			functions.push(function(){
				$(el).html('<i class="fa fa-filter"/> Last month');
				var dd = today.getDate();
				dd = ("01/"+today.getMonth())+"/"+today.getFullYear();
				$(el).attr("value",dd);
				$(el).attr("datevalue",dd);
				thefunction();
			});
			functions.push(function(){
				$("body").append('<input class="thismonthselector" style="left:'+(parseInt(event.pageX)-80)+'px;top:'+(parseInt(event.pageY)-100)+'px" displayhidden type="text"/>');
				$(".thismonthselector").MonthPicker({ Button: false });
				$(".thismonthselector").trigger('focus');
				$(".thismonthselector").on('blur',function(){
					var theval = "01/"+$(this).val();
					$(el).attr("value",theval);
					$(el).html('<i class="fa fa-filter"/> '+$(this).val());
					$(".thismonthselector").remove();
					$(el).attr("datevalue",theval);
					thefunction();
				});
			});
			functions.push(function(){
				$(el).html('<i class="fa fa-filter"/> All Months');
				$(el).attr("value","all");
				$(el).attr("datevalue","all");
				thefunction();
			});
			showMyPopupMenu(menus,functions,function(){});
		});
		$(this).html('<i class="fa fa-filter"/> All Months');
		$(this).attr("value","all");
		$(this).attr("datevalue","all");
	}
	$.fn.myselect = function(enteraction){
		var el = this;
		var selectoptions=[];
		$(el).find("[myselectoption]").each(function(){
			selectoptions.push('<div class="myselectoption popupmenuitem">'+$(this).html()+'</div>');
		});
		var elinput = $('<input placeholder="Business unit"/>');
		$(el).html(elinput);
		$(elinput).on('focus',function(){
			$(this).select();
			$(".myselectoptions").remove();
			$("body").append('<div class="myselectoptions mypopupmenubackground">\
								'+selectoptions.join("")+'\
							</div>');
			$(".myselectoptions").css({"left":$(el).position().left+"px",
										"top":(parseInt($(el).position().top)+28)+"px"});
			$(".myselectoption").on('click',function(){
				//enteraction($(elinput).val());
				//$(".myselectoptions").remove();
				$(elinput).val($(this).html());
				enteraction($(elinput).val());
				$(this).blur();
			});
		}).on('keyup',function(evt){
			if(evt.keyCode==13){
				enteraction($(elinput).val());
				$(this).blur();
			}
		}).on('blur',function(){
		//
			//enteraction($(elinput).val());
			//$(".myselectoptions").css("visibility","hidden");
			setTimeout(function(){$(".myselectoptions").remove();},200);
		});
	//	$(elinput).trigger('focus');
	//	$(".myselectoptions").find(".myselectoption:first").trigger("click");
	//	$(".myselectoptions").hide();
	//	$(elinput).trigger('blur');
	}
	$.fn.datevalue = function(){
		return $(this).attr("datevalue");
	}
	$.fn.mytab = function(arr){
		var strclass = this;
		$(strclass).find(".mytabheader").remove();
		var theid = 0;
		$(strclass).prepend('<div class="mytabheader"></div>').find(".mytabbody").each(function(){
			theid=theid+1;
			var thecaption = $(this).attr("caption").replace(/\s/gi,"");
			var addtocaption = $(this).attr("addtocaption")==undefined?addtocaption="":addtocaption=addtocaption;
			$(this).parentsUntil("",".mytab").find(".mytabheader").append('<div class="mytabfragmentscaption" id="fragments'+theid+'">'+addtocaption+$(this).attr("caption")+'</div>');
			var thisel = this;
			$(this).parentsUntil("",".mytab").find("#fragments"+theid).on('click',function(){
				$(this).parentsUntil("",".mytab").find(".mytabfragmentscaption").removeClass("active");
				$(strclass).find(".mytabbody").css("display","none");
				$(thisel).css("display","table");
				$(this).addClass("active");
				if(arr!=undefined){
					if(arr[$(this).attr("id")]!=undefined){
						arr[$(this).attr("id")]();
					}	
				}
				
			});
		});
		$(strclass).find(".mytabheader").find("#fragments1").trigger('click');
		
	}
	$.fn.tablesearcheable = function(txtsearch){
		var thetable = this;
		$(txtsearch).on('keyup',function(evt){
			var tosearch = $(txtsearch).val();
			var myPattern = new RegExp('(\\w*'+tosearch+'\\w*)','gi');
			$(thetable).find("tr").not(":first").attr("displaynone","");
			$(thetable).find("tr").each(function(){
				var thistr = this;
				var isnaa = false;
				$(this).find("td").each(function(){
					var matches = $(this).text().match(myPattern);
					if (matches != null){
						isnaa = true;
					}
				});
				if(isnaa){
					$(thistr).removeAttr("displaynone");
				}
				return;
			});
		});
	};
	$.fn.mylefttab = function(){
		var classname = this;
		$(classname).prepend('<div class="tableftheader"></div>').find(".tableftbody").each(function(){
			$(this).wrapInner('<div class="tableftbodywrapper"></div>');
			var thecaption = $(this).attr("caption");
			var theid = thecaption.replace(/\W/gi,"");
			$(this).parentsUntil("",".tableft").find(".tableftheader").append('<div class="tableftheaderitems" leftabitemsid="'+theid+'">'+thecaption+'</div>');
			$(this).attr("leftabbodyid",theid);
		});
		$(classname).find(".tableftheaderitems").on('click',function(){
			$(".tableftheaderitems").removeClass("active");
			$(this).addClass("active");
			var theid = $(this).attr("leftabitemsid");
			$(this).parentsUntil("",".tableft").find(".tableftbody").css("display","none");
			$(this).parentsUntil("",".tableft").find("[leftabbodyid="+theid+"]").css("display","inline-block");
		});
		$(classname).find(".tableftheader").find(".tableftheaderitems:first").trigger('click');
	}
	$.fn.mycollapsible = function(){
		var theparent = this;
		$(this).find(".mycollapsiblelist").each(function(){
			var theparentlist = this;
			$(this).wrap('<div class="mycollapsiblelistparent"></div>');
			$(this).parentsUntil("",".mycollapsiblelistparent").prepend('<div class="mycollapsiblelistlist">'+$(this).attr("caption")+'<span class="mycollapsiblechevron" floatright colorwhite font16><i class="fa fa-chevron-circle-right"/></span></div>');
			$(this).parentsUntil("",".mycollapsiblelistparent").find(".mycollapsiblelistlist").on('click',function(){
				var thethis = $(this).parentsUntil("",".mycollapsiblelistparent").find(".mycollapsiblelist");
				if(thethis.css("height")!="0px"){
					$(thethis).stop().animate({"height":"0px"},500,function(){
						$(thethis).parentsUntil("",".mycollapsiblelistparent").find(".mycollapsiblechevron").html('<i class="fa fa-chevron-circle-right"/>');
					});
				}
				else{
					if($(theparentlist).parentsUntil("",".mycollapsible") != theparent){
						$(theparent).find(".mycollapsiblelist").stop().animate({"height":"0px"},500,function(){
							$(theparent).find(".mycollapsiblechevron").html('<i class="fa fa-chevron-circle-right"/>');
						});
					}
					$(thethis).stop().animate({"height":$(thethis).get(0).scrollHeight},500,function(){
						$(thethis).parentsUntil("",".mycollapsiblelistparent").find(".mycollapsiblechevron").html('<i class="fa fa-chevron-circle-down"/>');
					});
				}
			});
		});
	}
	function myget(filepath,w,v,returnfunction){
		$.get(filepath+"?"+w+"&"+v,returnfunction);
	}
	function escapeRegExp(str) {
		return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
	}
	function pad(n, width, z) {
		z = z || '0';
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
	}
	function PrintElem(elem){
		var mywindow = window.open('', 'PRINT', 'height=400,width=600');
		mywindow.document.write('<html><head><title>' + document.title  + '</title>');
		mywindow.document.write('</head><body>');
		mywindow.document.write('<h1>' + document.title  + '</h1>');
		mywindow.document.write($(elem).html());
		mywindow.document.write('</body></html>');
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/
		mywindow.print();
		mywindow.close();
		return true;
	}
	String.prototype.capitalize = function(){
		return this.charAt(0).toUpperCase() + this.slice(1);
	}
	function formatAMPM(date){
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var ampm = hours >= 12 ? 'pm' : 'am';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		var strTime = pad(hours,2,0) + ':' + pad(minutes,2,0) + ampm;
		return strTime;
	}
	$("<div></div>").load("pageloading2.txt",function(result){
		//$("body").append('<div class="pageloading2">'+result+'</div>');
	});
	function MyModal(){//custom modal
		this.mymodaltitle="";
		this.mymodalsize="width:30%";
		this.thismodal;
		this.ishowna=false;
		//this.theprogressicon;
		var themodal=this;
		this.isdown=false;
		this.strtag="";
		this.title=function(str){
			$(this.thismodal).find(".mymodaltitletext").html(str);
			this.mymodaltitle=str;
		}
		this.body=function(str){
			$(this.thismodal).find(".mymodalbody").html(str);
			refreshthis(themodal.thismodal);
		}
		this.getbody=function(){
			return $(this.thismodal).find(".mymodalbody").html();
		}
		this.showprogressicon = function(imagefile){
			//this.theprogressicon=imagefile;
			$(this.thismodal).find(".pageloadingicon").css("background-image",imagefile);
		}
		this.show=function(strtitle){
			//this.mymodalsize="width:50%";
			this.mymodalsize="width:675.5px";
			if(strtitle!=undefined){
				this.mymodaltitle=strtitle;
			}
			showthemodal();
		}
		this.showsmall=function(strtitle){
			this.mymodalsize="width:30%";
			if(strtitle!=undefined){
				this.mymodaltitle=strtitle;
			}
			showthemodal();
		}
		this.showwide=function(strtitle){
			this.mymodalsize="width:90%";
			if(strtitle!=undefined){
				this.mymodaltitle=strtitle;
			}
			showthemodal();
		}
		this.showcustom=function(strtitle,strsize){
			this.mymodalsize="width:"+strsize;
			if(strtitle!=undefined){
				this.mymodaltitle=strtitle;
			}
			showthemodal();
		}
		
		function showthemodal(){
			if(themodal.ishowna){
				return;
			}
			themodal.ishowna=true;
			switch(themodal.mymodalsize){
				case "width:55%":
					themodal.mymodalsize="width:743.047px";
					break;
				case "width:60%":
					themodal.mymodalsize="width:810.594px";
					break;
				case "width:70%":
					themodal.mymodalsize="width:945.688px";
					break;
			}
			themodal.mymodalbody = $(".pageloadingbackground").html();
			themodal.thismodal = $("<div class='mymodalbackground'>"+
									  "<div class='mymodalinner' style='"+themodal.mymodalsize+"'>"+
										 "<div class='mymodaltitle'><div class='mymodaltitletext'>"+themodal.mymodaltitle+"</div><div class='mymodalclose'><i class='fa fa-times fa-lg'></i></div>"+
										 "</div>"+
										 "<div class='mymodalbody'>"+
											"<div displaytable style='margin-left:auto;margin-right:auto;height:380px'>"+themodal.mymodalbody+"</div>"+
										 "</div>"+
										 "<div class='mymodalfooter'>"+
										 "</div>"+
									  "</div>"+
								  "</div>");
			$("body").append(themodal.thismodal);
			
			var modalinner = $(themodal.thismodal).find(".mymodalinner").css("width");
			$(themodal.thismodal).find(".mymodalinner").css("min-width",modalinner);
			$(themodal.thismodal).find("[firstfocus]").focus();
			
			//$(themodal.thismodal).animate({opacity:"1"},300);
			//$(themodal.thismodal).find(".mymodalinner").animate({"transform-scale":"1"},300);
			
			$(themodal.thismodal).addClass("load");
			var thethis;
			
			$(".mymodalclose").on("click",function(){
				//$(this).parentsUntil("",".mymodalinner").animate({"zoom":"0.8"},300);
				themodal.closefunction($(this).parentsUntil("",".mymodalbackground"));
				// $(this).parentsUntil("",".mymodalbackground").animate({opacity:"0"},300,function(){
																// $(this).remove()
															// });
				
			});
			
			$(".mymodalbackground").css("z-index","100");
			$(themodal.thismodal).css("z-index","101");
			
			$(themodal.thismodal).find(".mymodalinner").on('mousedown',function(){
				$(".mymodalbackground").css("z-index","100");
				$(this).parentsUntil("",".mymodalbackground").css("z-index","101");
			});
			$(themodal.thismodal).find(".mymodaltitle").on('mousedown',function(evt){
				//$(".mymodalinner").css("z-index","");
				
				this.isdown = true;
				this.varleft = $(this).parentsUntil("",".mymodalinner").offset().left - $(window).scrollLeft();
				$(this).css("cursor","-webkit-grabbing");
				this.vartop = $(this).parentsUntil("",".mymodalinner").offset().top - $(window).scrollTop();
				this.dx = curx - this.varleft;
				this.dy = cury - this.vartop;
				
				thethis = this;
				var thedx = this.dx;
				var thedy = this.dy;
				clearInterval(this.dragtimer);
				this.dragtimer = setInterval(function(){
					$(thethis).parentsUntil("",".mymodalinner").css({"margin-left":(curx-thedx)+"px","margin-top":(cury-thedy)});
				},10);
			}).on('mouseup',function(evt){
				$(thethis).css("cursor","-webkit-grab");
				clearInterval(this.dragtimer);
			});
			$(".mymodalbackground").on('mousemove',function(){
				//$(this).find(".mymodaltitle").trigger('mouseup');
			});
		}
		
		this.element=function(){
			return this.thismodal;
		}
		this.showloading=function(){
			//$(this.thismodal).find(".mymodalbody").children().wrap("<div displaynone></div>");
			$(this.thismodal).find(".mymodalbody").html("<div displaytable style='margin-left:auto;margin-right:auto;height:380px'>"
																+$(".pageloadingbackground").html()
														+"</div>");
			
		}
		this.settag=function(strtag){
			this.strtag=strtag;
		}
		this.gettag=function(){
			return this.strtag;
		}
		this.size=function(str){
			if(str=="wide"){
				this.mymodalsize="width:90%";
			}
			else if(str=="small"){
				this.mymodalsize="width:50%";
			}
			else if(str=="xsmall"){
				this.mymodalsize="width:30%";
			}
			else{
				this.mymodalsize="width:"+str;
			}
		}
		this.closefunction=function(el){
			$(el).removeClass("load");
			setTimeout(function(){
				$(el).remove();
				themodal.ishowna=false;
			},300);
		}
		this.close=function(){
			
			this.closefunction(themodal.thismodal);
			// $(this.thismodal).find(".mymodalinner").animate({"zoom":"0.8"},300);
			// $(this.thismodal).animate({opacity:"0"},300,function(){
															// $(this).remove()
														// });
		}
		
	}
	String.prototype.my2darrdecode = function(){
		arr = this;
		var arrsp2 = arr.split("<sp2>");
		var toarr = [];
		arrsp = arrsp2[0].split("<sp>");
		toarr.push(arrsp);
		arrsp = arrsp2[1].split("<sp>");
		toarr.push(arrsp);
		return toarr;
	}
	function echo(obj){
		$("body").html(obj + "<br/>" + obj.toHtmlEntities());
	}
	String.prototype.toHtmlEntities = function() {
		return this.replace(/./gm, function(s) {
			return "&#" + s.charCodeAt(0) + ";";
		});
	};
	document.addEventListener('DOMContentLoaded', function(){
		if(!Notification) {
			alert('Desktop notifications not available in your browser. Try Chromium.'); 
			return;
		}
		if (Notification.permission !== "granted"){
			Notification.requestPermission();
		}
	});
	//notifyMe("asdf");
	function notifyMe(msg){
		if(Notification.permission !== "granted"){
			Notification.requestPermission();
		}
		else{
			var notification = new Notification('Online TOR & RFS',{
				icon: 'http://172.16.161.37/onlinetorrfs/index2/img/notifylogo.png',
				body: msg,
			});
			// notification.onclick = function(){
				// window.open("http://stackoverflow.com/a/13328397/1269037");
			// }
		}
	}
	$.fn.textfield = function(){
		var thissel = this;
		$(this).each(function(){
			var thelabel = $(this).attr("label");
			var colval = $(this).attr("colval");
			$(this).wrap('<div class="textfieldframe" '+colval+'></div>');
			$(this).parentsUntil("",".textfieldframe").prepend('<span>'+thelabel+'</span>');
		});
	}
	function showMyConfirm(confirmessage,functionok,functioncancel){
		var thisconfirm = $('<div class="myconfirmmessage">'+
								'<div class="myconfirmmessageinner">'+
									'<p>\
										<span class="ui-icon ui-icon-info" style="margin-right: .3em;display:inline "></span>'+
										'<strong></strong> \
										<span>'
											+confirmessage+
										'</span>\
										<span col6 style="margin-left:10px;margin-top:-6px">\
											<button class="btnconfirmmessageok"> <i class="fa fa-check"/> Ok </button>\
											<button class="btnconfirmmessagecancel" style="margin-left:5px"> <i class="fa fa-times"/> Cancel </button>\
										</span>\
									</p>'+
								'</div>'+
							'</div>');
		$("body").append(thisconfirm);
		var thisx = $(thisconfirm).find(".myconfirmmessageinner").css("width").replace("px","");
		var thisy = $(thisconfirm).find(".myconfirmmessageinner").css("height").replace("px","");
		thisx = curx - (thisx/2);
		thisy = cury - (thisy);
		$(thisconfirm).find(".myconfirmmessageinner").css({"left":thisx+"px","top":thisy+"px"});
		refreshthis(thisconfirm);
		//$(".myconfirmmessage").animate({opacity:"1"},200);
		$(".myconfirmmessage").addClass("show");
		$(".myconfirmmessageinner").addClass("show");
		//setTimeout(function(){
			var mana = function(){
				$(".myconfirmmessage").removeClass("show");
				$(".myconfirmmessageinner").removeClass("show");
				//$(".myconfirmmessage").animate({opacity:"0"},200,function(){
				//	$(".myconfirmmessage").remove();
				//});
				setTimeout(function(){
					$(".myconfirmmessage").remove();
				},300);
			};
			$(".btnconfirmmessageok").on('click',function(){
				functionok();
				mana();
			});
			$(".btnconfirmmessagecancel").on('click',function(){
				functioncancel();
				mana();
			});
		//},200);
	}
	function showMyAlert(strtitle,strstring){
		$(".myalertmessage").stop().remove();
		clearTimeout(MyAlertTimeout);
		$("body").append('<div class="myalertmessage" style="background-color:#eeeeee;border:1px solid white; box-shadow: 0 0 15px 0px rgba(0,0,0,0.5);top:90vh;z-index:200;position:fixed;left:1%;opacity:0;padding-right:10px">'+
								'<p><span style="float: left; margin-right: .3em;padding-left:10px;padding-right:10px;color:#007fff"><i class="fa fa-info-circle"></i></span>'+
								'<strong>'+strtitle+'</strong> '+strstring+'</p>'+
							'</div>');
		$(".myalertmessage").animate({opacity:"1",marginTop:"-20px"},300);
		MyAlertTimeout = setTimeout(function(){
			$(".myalertmessage").animate({opacity:"0"},1000,function(){
				$(".myalertmessage").remove();
			});
		},3500);
	}
	function showMyAlertError(strtitle,strstring){
		$(".myalertmessage").stop().remove();
		clearTimeout(MyAlertTimeout);
		$("body").append('<div class="myalertmessage" style="background-color:#eeeeee;border:1px solid white; box-shadow: 0 0 15px 0px rgba(0,0,0,0.5);top:90vh;z-index:200;position:fixed;left:1%;opacity:0;padding-right:10px">'+
								'<p><span style="float: left; margin-right: .3em;padding-left:10px;padding-right:10px;color:#cc0000"><i class="fa fa-times-circle"></i></span>'+
								'<strong>'+strtitle+'</strong> '+strstring+'</p>'+
							'</div>');
		$(".myalertmessage").animate({opacity:"1",marginTop:"-20px"},300);
		MyAlertTimeout = setTimeout(function(){
			$(".myalertmessage").animate({opacity:"0"},1000,function(){
				$(".myalertmessage").remove();
			});
		},3500);
	}
	function showMyPopupMenu(strmenus,strfunctions,callback){
		$(".mypopupmenubackground").remove();
		var a;
		var popupmenuitemshtml="";
		for(a in strmenus){
			popupmenuitemshtml += '<div class="popupmenuitem" popupmenuitemid="'+a+'">' + strmenus[a] + '</div>\n';
		}
		$("body").append('<div class="mypopupmenubackground">'+
							'<div class="mypopupmenupointer" style="border-right:10px solid #656565;margin-top:7px;left:-10px">'+
								'<div class="mypopupmenupointer" style="position:absolute;top:-10px;left:0px;">'+
								'</div>'+
							'</div>'+
							'<div class="popupmenuitems">'+
								popupmenuitemshtml+
							'</div>'+
						'</div>');
		var onmouseleaved = function(){
			var thisel = $(".mypopupmenubackground");
			if(typeof callback == 'function'){
				callback();
			}
			$(thisel).fadeOut(200,function(){
				$(thisel).remove();
			});
		};
		//alert(event.clientX);
		$(".mypopupmenubackground").css({"left":curx-15,"top":cury-10})
		.on('mouseleave',onmouseleaved);
		$(".popupmenuitem").each(function(){
			$(this).on('click',function(){onmouseleaved();
			strfunctions[($(this).attr("popupmenuitemid"))]()});
		});
	}
	function showMyColorPicker(el){
		var prevcolor=$(el).css("background-color");
		var prevfontcolor=$(el).css("color");
		var arrayofcolors = new Array(
									 "#FFFFFF","#464646","#EEECE1","#1F497D","#4F81BD","#C0504D","#9BBB59","#8064A2","#4BACC6","#F79646",
									  "#FCFCFC","#DADADA","#DDD9C3","#C6D9F0","#DBE5F1","#F2DCDB","#EBF1DD","#E5E0EC","#DBEEF3","#FDEADA",
									  "#F5F5F5","#B5B5B5","#C4BD97","#8DB3E2","#B8CCE4","#E5B9B7","#D7E3BC","#CCC1D9","#B7DDE8","#FBD5B5",
									  "#BFBFBF","#909090","#938953","#548DD4","#95B3D7","#D99694","#C3D69B","#B2A2C7","#92CDDC","#FAC08F",
									  "#A5A5A5","#343434","#494429","#17365D","#366092","#953734","#76923C","#5F497A","#31859B","#E36C09",
									  "#7F7F7F","#232323","#1D1B10","#1D1B10","#0F243E","#632423","#4F6128","#3F3151","#205867","#974806",
									  "#C00000","#FF0000","#FFC000","#FFFF00","#92D050","#00B050","#00B0F0","#0070C0","#002060","#7030A0"
									);
		var divofcolors="";
		var a;
		var morestyle="";
		var colorpickertimeout;
		for(a in arrayofcolors){
			if(a%10==0 && a>=20 && a<60){
				var thetop=((a-10)/10)*5;
				morestyle=";top:-"+thetop+"px;";
			}
			if(a%10==0 && a!=0){
				divofcolors = divofcolors + '<div class="colorpickercolor" style="background-color:'+arrayofcolors[a]+morestyle+'"></div>';
			}
			else{
				divofcolors = divofcolors + '<div class="colorpickercolor" style="background-color:'+arrayofcolors[a]+morestyle+'"></div>';
			}
		}
		$("body").append('<div class="mycolorpickerbackground">'+
							divofcolors+
						'</div>');
		$(".mycolorpickerbackground").css({"left":(event.pageX-(140/2))+"px","top":(event.pageY-10)+"px"})
		.animate({opacity:"1"},200)
		.on("mouseleave",function(){
			colorpickertimeout = setTimeout(colorpickermouseleave(),500);
		}).on('mouseenter',function(){
			clearTimeout(colorpickertimeout);
		});
		var colorpickermouseleave = function(){
			$(".mycolorpickerbackground").animate({opacity:"0"},100,function(){
				$(el).css("background-color",prevcolor);
				$(el).css("color",prevfontcolor);
				$(".mycolorpickerbackground").remove();
			});
		};
		$(".colorpickercolor").on("mouseenter",function(){
			var thecolor = $(this).css("background-color");
			thecolor = thecolor.replace(/(rgb\()|\)|\s/g,"");
			thecolor = thecolor.split(",");
			var a;
			var howmany=0;
			for(a in thecolor){
				if(parseInt(thecolor[a])<155){
					howmany++;
				}
			}
			if(howmany>=2){
				$(el).css("color","white");
				$(this).css("color","white");
			}
			else{
				$(el).css("color","black");
				$(this).css("color","black");
			}
			$(el).css("background-color",$(this).css("background-color"));
		}).on('click',function(){
			prevcolor=$(this).css("background-color");
			prevfontcolor=$(this).css("color");
			
			colorpickermouseleave();
		});
	}
	function myProgress(){
		this.myprogressid = $(".myprogressbar").length;
		this.myprogressmessage="Please wait...";
		this.show = function(str){
			$(".bodyelements").append('<div class="myprogressbar tableparent" id="myprogressid'+this.myprogressid+'">'+
										'<div class="tablecell" style="width:100%">'+
											'<div class="myprogressbarinner">'+
												'<div class="myprogressbarthebarcontainer">'+
													'<div class="myprogressbarthebarinner">'+
														'<div class="myprogressbarthebartoleft">'+
															'\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\'+
														'</div>'+
													'</div>'+
												'</div>'+
												'<div class="myprogressbarthetext">'+
													this.myprogressmessage+
												'</div>'+
											'</div>'+
										'</div>'+
									+'</div>');
			$(".myprogressbar").animate({opacity:"1"},200);
		}
		this.title = function(str){
			this.myprogressmessage = str;
		}
		this.close = function(){
			$("#myprogressid"+this.myprogressid).fadeOut(500,function(){$(this).remove()});
		}
	}
	function mymodalclose(thismodal){
		$(thismodal).find(".mymodalinner").animate({marginTop:"0px"},200);
		$(thismodal).animate({opacity:"0"},200,function(){
																					$(this).remove()
																				});
	}
	function mymodalcloseclicked(el){
		var mymodalinner = $(el).parentsUntil("",".mymodalinner");
		mymodalinner.animate({"zoom":"0.8"},300);
		$(el).parentsUntil("",".mymodalbackground").animate({opacity:"0"},300,function(){
																					$(this).remove()
																				});
	}
	function mymodalcloseclicked2(el){
		$(el).find(".mymodalinner").animate({marginTop:"0px"},200);
		$(el).animate({opacity:"0"},200,function(){
													$(this).remove()
												});
	}
	function showMyAlertProgress(el){
		$(el).parentsUntil("",".mymodalinner").append('<div class="mymodalprogress">'+
															'<div class="pageloadingbackground tableparent" style="position:relative;opacity:1">'+
																'<div class="tablecell">'+
																	'<div class="pageloadingcontents2 circleradius">'+
																		'<div class="tableparent">'+
																			'<div class="tablecell">'+
																				'<div class="pageloadingcircle1 circleradius">'+
																					'<div class="tableparent">'+
																						'<div class="tablecell">'+
																							'<div class="pageloadingcircle2 circleradius">'+
																								
																							'</div>'+
																						'</div>'+
																					'</div>'+
																				'</div>'+
																			'</div>'+
																		'</div>'+
																	'</div>'+
																'</div>'+
															'</div>'+
														'</div>');
		$(".mymodalprogress").animate({opacity:"1"},200);
	}
	function refreshthis(el){
		$(el).find("[filterbar]").focus().filterbar();
		//$(el).find("select").selectmenu().addClass("optionoverflow");
		$(el).find(".spinner").spinner();
		$(el).find("#dialog-link, #icons li" ).hover(
			function(){
				$(this).addClass( "ui-state-hover");
			},
			function(){
				$(this).removeClass( "ui-state-hover");
			}
		);
		$(el).find(".maximizable").maximizable();
		//console.log($(el).find("[trhoverable]").html());
		$(el).find("[trhoverable]").on('click',function(){
			$(this).addClass("active");
		});
	}
	function refreshbuttons(){
		$(".btntoggle").button();
	}
	function mydebugger(str){
		if($(".mydebugger").length==0){
			$("body").append('<div class="mydebugger">'+str+'</div>');
		}
		else{
			$(".mydebugger").html(str);
		}
	}
	function showstatusprogress(){
		$(".statusprogress").css("visibility","visible");
	}
	function hidestatusprogress(){
		$(".statusprogress").css("visibility","hidden");
	}
	function setstatusprogress(progressname, progressvalue, progressname2){
		$(".syncprogress").css("width",progressvalue+"%");
		$(".statusprogress").find(".progressrow").html(progressname);
		$(".statusprogress").find(".progressrow2").html(progressname2);
	}
	function goFullscreen(){
		var element = document.documentElement;
		if(element.mozRequestFullScreen){
			element.mozRequestFullScreen();
		}
		else if(element.webkitRequestFullScreen){
			element.webkitRequestFullScreen();
		}
	}
	function exitFullscreen(){
		if(document.exitFullscreen){
			document.exitFullscreen();
		}
		else if(document.mozCancelFullScreen){
			document.mozCancelFullScreen();
		}
		else if(document.webkitExitFullscreen){
			document.webkitExitFullscreen();
		}
	}
	
</script>