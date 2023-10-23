<style type="text/css">
.left_icons { position: absolute; font-size:20px; background: gray; width: 50px; height: 50px; border-radius: 50%; text-align: center;}
.right_text { position: relative; left: 70px; content:""; top:0px; }
.left_icons { display: table; }
.left_icons i { display: table-cell; vertical-align: middle; }

.left_panel_tab .nav-tabs li.active a, .nav-tabs li.active a:focus{ background-color: #465b74; color:#fff; }
.left_panel_tab .nav li a:focus, .nav li a:hover { border:none; }
.left_panel_tab .nav-tabs li.active a:after{position: absolute; left: 50%; bottom: 50px; border-style: solid; border-width: 7px 7px 0 7px; border-color: #007bff transparent transparent transparent;}
.left_panel_tab .nav-tabs { border-bottom:none; }
</style>
<div class="panel panel-default panel-block">
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="left_panel_tab">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#left_public" data-toggle="tab">Public Reply</a></li>
						<li><a href="#internal_notes" data-toggle="tab">Internal Notes</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="left_public">123</div>
						<div class="tab-pane" id="internal_notes">456</div>
					</div>
				</div>
				<div>
					<textarea rows="7" class="form-control"></textarea>
					<div class="bg_light_primary" style="padding:10px;">
						<a href="#"><i class="fa fa-calendar"></i></a>
						<a href="#" class="pull-right">Send</a>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<h5 class="m-b-20">Conversation</h5>
				<div class="right_panel_tab">
					<ul class="nav nav-tabs customtab">
						<li class="active"><a href="#top_tab_one" data-toggle="tab">All(4)</a></li>
						<li><a href="#internal_notes" data-toggle="tab">Public(2)</a></li>
						<li><a href="#Notes" data-toggle="tab">Notes(2)</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="top_tab_one">
							<div class="media_panel">
								<div class="media_center">
									<div class="left_icons">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<div class="right_text">
									<p>Jeffrey Canfield - M12345678 <em>Thu., Feb. 23, 2019 @ 2:35 PM</em>	</p>
									<p>Certe, inquam, pertinax non intellegamus, tu tam crudelis fuisse, nihil est.</p>
								</div>
							</div>
							<hr>
							<div class="media_panel">
								<div class="media_center">
									<div class="left_icons">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<div class="right_text">
									<p>Jeffrey Canfield - M12345678 <em>Thu., Feb. 23, 2019 @ 2:35 PM</em>	</p>
									<p>Certe, inquam, pertinax non intellegamus, tu tam crudelis fuisse, nihil est.</p>
								</div>
							</div>
							<hr>
							<div class="media_panel">
								<div class="media_center">
									<div class="left_icons">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<div class="right_text">
									<p>Jeffrey Canfield - M12345678 <em>Thu., Feb. 23, 2019 @ 2:35 PM</em>	</p>
									<p>Certe, inquam, pertinax non intellegamus, tu tam crudelis fuisse, nihil est.</p>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="public">
							<p>Tab two</p>
						</div>
						<div class="tab-pane" id="Notes">
							<p>Tab Three</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>