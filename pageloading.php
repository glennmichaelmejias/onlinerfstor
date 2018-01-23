<style>
	.pageloadingbackground{
		width:100%;
		height:100%;
		position:absolute;
		left:0;
		opacity:0;
		top:0;
		background-color: argb(0,0,0,0);
		display:table;
	}
	.circleradius{
		border-radius:50%;
		border-right:1px solid #3498db;
		border-left:1px solid #3498db;
		border-top:1px solid #3498db;
		border-bottom:1px solid #3498db;
	//	background-color:rgba(0, 127, 255,0.05);
	
		// animation-name: example;
		// animation-duration: 1.6s;
		// animation-iteration-count: infinite;
		// animation-timing-function: linear;
	}
	@-webkit-keyframes example {
		from {-webkit-transform:rotate(0deg)}
		to {-webkit-transform:rotate(360deg)}
	}
	.tableparent{
		display:table;
		text-align:center;
		width:100%;
		height:100%;
	}
	.tablecell{
		display:table-cell;
		vertical-align:middle;
	}
	.pageloadingcontents2{
		display:inline-block;
		height:100px;
		width:100px;
		border-top-color:#3498db;
		
		-webkit-animation-name: example;
		-webkit-animation-duration: 1.7s;
		-webkit-animation-iteration-count: infinite;
		-webkit-animation-timing-function: linear;
	}
	.pageloadingcircle1{
		margin-top:130px;
		width:50px;
		height:50px;
		display:inline-block;
		
		
		-webkit-animation-name: example;
		-webkit-animation-duration: 1.5s;
		-webkit-animation-iteration-count: infinite;
		-webkit-animation-timing-function: linear;
		
		
		//border-top-color:#e74c3c;
		//visibility:hidden;
	}
	.pageloadingcircle2{
		width:20px;
		height:20px;
		margin-top:55px;
		display:inline-block;
		border-bottom:1px solid #3498db;
		
		//border-top-color:#f9c922;
		//visibility:hidden;
	}
	.pageloadingcircle3{
		width:40px;
		height:40px;
		margin-top:60px;
		display:inline-block;
		//border-top-color:#f9c922;
		//visibility:hidden;
	}
	.pageloadingcircle4{
		width:20px;
		height:20px;
		margin-top:40px;
		display:inline-block;
		//border-top-color:#f9c922;
		//visibility:hidden;
	}
	.pageloadingcircle5{
		width:20px;
		height:20px;
		margin-top:20px;
		display:inline-block;
		//border-top-color:#f9c922;
		//visibility:hidden;
	}
	.pageloadingicon{
		vertical-align:middle;
		height:100px;
		width:100px;
		display:inline-block;
		background-repeat:no-repeat;
		background-position:50%;
	}
</style>
<script type="text/javascript">
	var deg=0;
	var deg2=0;
	// clearInterval(loadingtimer);
	// var loadingtimer = setInterval(function(){
		// deg=deg+8;
		// deg2=deg2+12;
		// $(".circleradius").css("transform","rotate("+deg+"deg)");
		// $(".pageloadingcircle1").css("transform","rotate("+deg2+"deg)")
	// },40);
	$(".pageloadingbackground").animate({opacity:"1"},200);
</script>
<div class="pageloadingbackground tableparent">
	<div class="tablecell">
		<div class="pageloadingicon">
			<div class="pageloadingcontents2 circleradius">
				<div class="tableparent">
					<div class="tablecell">
						<div class="pageloadingcircle1 circleradius">
							<div class="tableparent">
								<div class="tablecell">
									<div class="pageloadingcircle2 circleradius">
										<!-- <div class="tableparent">
											<div class="tablecell">
												<div class="pageloadingcircle3 circleradius">
													<div class="tableparent">
														<div class="tablecell">
															<div class="pageloadingcircle4 circleradius">
																<div class="tableparent">
																	<div class="tablecell">
																		<div class="pageloadingcircle5 circleradius">
																			
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div> -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>