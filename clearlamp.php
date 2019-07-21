<?php
	error_reporting(E_ALL & ~E_NOTICE);
	require('misc.php');
	require('canvas_data.php');
	require('create_table.php');

	$file = "tables.json";
	$json = json_decode(file_get_contents($file), true);
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:site" content="@xxyzzzzz" />
		<meta name="twitter:title" content="BMS ClearLamp" />
		<meta name="twitter:description" content="<?php echo $tablename." ".strtoupper($mode)." LAMP"; if(!empty($playername)) echo " (".$playername.")";?>" />
		<meta name="robots" content="noindex,nofollow,noarchive">

		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<!--<script type="text/javascript" src="js/classie.js"></script>-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
		<script type="text/javascript" src="js/range.js"></script>
		<script type="text/javascript" src="js/jquery.stickybar.min.js"></script>

		<link href="style.css" rel="stylesheet" type="text/css">

		<title><?php echo $tablename." ".strtoupper($mode)." LAMP"; if(!empty($playername)) echo " (".$playername.")"; ?></title>

		<style>
			<?php
			$defcol = randomcolor();
			$rancol = rgbcss($defcol);
			$highcol = rgbcss(highsaturation($defcol));
			?>

			.lamp_header {
				background-color: <?php echo $rancol; ?>
			}
			.ha-header-front form select > *{
				background-color: <?php echo $rancol; ?>
			}
			.ha-header-front form select option[selected]{
				font-weight: bold;
				background-color: <?php echo $highcol; ?> !impotant
			}
			#formbutton:hover{
				color: <?php echo $rancol; ?>
			}
			#imageexport a:hover{
				color: <?php echo $rancol; ?>
			}
			#modeselect input[type="radio"]:checked + label {
				background: white;
				color: <?php echo $rancol; ?>
			}
			#modeselect label:hover {
				background: white;
				color: <?php echo $rancol; ?>
			}
		</style>
	</head>

	<body>
		<header id="lamp_header" class="lamp_header">
			<div class="ha-header-front">
				<div class="ha-header-top">
					<h1 id='tablename'><span><?php echo $tablename; ?> ClearLamp</span></h1>

					<?php
						if(!empty($playername))
							echo "<h2 id='playername'>Player: <a target='_blank' href='http://www.dream-pro.info/~lavalse/LR2IR/search.cgi?mode=mypage&playerid=".$lr2ID."'>".$playername." (".$lr2ID.")"."</a></h2>";
						if(empty($lr2ID)===FALSE && $html !== FALSE) {
							echo '<div id="imageexport">
									<a class="shrinkbutton" id="download" href="#" download="'.$tablename." ".strtoupper($mode)." LAMP (Player:".$playername.').png">Save as PNG</a>
								</div>';
						}
					?>
				</div>

				<form name="LR2IDForm" method="GET" action="clearlamp.php">
					<button id="formbutton">OK</button>
					<div class="leftdiv">
						<label for="lr2ID">
							LR2ID:
						</label>
						<input type="text" name="lr2ID" id="lr2ID" pattern="[0-9]{0,6}" value="<?php echo $lr2ID; ?>">
						<div id="modeselect" class="">
							<input type="radio" id="clear" name="mode" value="clear" <?php if(strcmp($mode, "clear") === 0) echo "checked"; ?>><label for="clear" class="toggle-btn">Clear</label>
							<input type="radio" id="judge" name="mode" value="judge" <?php if(strcmp($mode, "judge") === 0) echo "checked"; ?>><label for="judge" class="toggle-btn">Judgement</label>
						</div>
					</div>
					<div class="leftdiv">
						<label for="urlselect">	URL:</label>
						<select id="urlselect" class="urlselect" onchange="this.nextElementSibling.value=this.value, this.form.submit()">
							<option value="" <?php if(empty($tablename)!==FALSE){echo 'selected';} ?> disabled>Select Table...</option>
							<?php
								$table_included = FALSE;
								foreach($json as $key => $value){
									if(empty($value['fullname'])===FALSE){
							?>
										<optgroup label="<?php echo $value['fullname']; ?>">
											<?php
												foreach($value['tables'] as $tables_key => $tables_value){
													if($tables_value['url']===$table_url){
														$table_included = TRUE;
											?>
														<option value="<?php echo $tables_value['url']; ?>" selected><?php echo $tables_value['name']; ?> (Currently selected)</option>
											<?php
													}else{
											?>
														<option value="<?php echo $tables_value['url']; ?>"><?php echo $tables_value['name']; ?></option>
											<?php
													}
												}
											?>
										</optgroup>
							<?php
									}
								}
							?>
							<optgroup label="Custom URL">
								<?php
									if($table_included!==TRUE && empty($tablename)===FALSE){
										echo '<option value="'.$table_url.'" selected>'.$tablename.' <small>(Currently selected)</small></option>';
									}
								?>
								<option value="">Custom URL</option>
							</optgroup>
						</select>
						<input type="text" id="table_url" name="table_url" class="urlinput" value="<?php echo $table_url;?>" />
					</div>
				</form>
			</div>
		</header>
		<main class="wrapper">
			<div id="chartContainer" class="chartdiv"></div>

			<div id="bottomContainer">
				<div id="sidebar">
					<div id="filter">
						<div id="level-filter">
							<h3>LEVEL</h3>
							<div id="level-range" class="range filter-div" data-min="<?php echo min($level_int_arr); ?>" data-max="<?php echo max($level_int_arr); ?>" data-step="1">
								<input type="hidden" name="min-level" value="<?php echo min($level_int_arr);?>" />
								<input type="hidden" name="max-level" value="<?php echo max($level_int_arr);?>" />
								<div id="range-show"></div>
							</div>
							<div class="ck-button"><label>
								<input type="checkbox" name="char-level" value="charlv" checked/><span>+Char LV</span>
							</label></div>
						</div>
						<div id="rank-filter" class="filter-div">
							<h3>RANK</h3>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="ALL-RANK" value="ALL-RANK" checked/><span>ALL</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="MAX" value="MAX" checked/><span>MAX</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="AAA" value="AAA" checked/><span>AAA</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="AA" value="AA" checked/><span>AA</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="A" value="A" checked/><span>A</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="B" value="B" checked/><span>B</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="C-F" value="C-F" checked/><span>C~F</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="rank-checkbox" type="checkbox" name="noplay" value="noplay" checked/><span>NO PLAY</span>
							</label></div>
						</div>
						<div id="clear-filter" class="filter-div">
							<h3>CLEAR</h3>
							<div class="ck-button"><label>
								<input class="clear-checkbox" type="checkbox" name="ALL-CLEAR" value="ALL-CLEAR" checked/><span>ALL</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="clear-checkbox" type="checkbox" name="FC" value="FULL-COMBO" checked/><span>FC</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="clear-checkbox" type="checkbox" name="HARD" value="HARD-CLEAR" checked/><span>HARD</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="clear-checkbox" type="checkbox" name="CLEAR" value="CLEAR" checked/><span>CLEAR</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="clear-checkbox" type="checkbox" name="EASY" value="EASY-CLEAR" checked/><span>EASY</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="clear-checkbox" type="checkbox" name="FAILED" value="FAILED" checked/><span>FAILED</span>
							</label></div>
							<div class="ck-button"><label>
								<input class="clear-checkbox" type="checkbox" name="noplay" value="NOT-PLAYED" checked/><span>NO PLAY</span>
							</label></div>
						</div>
					</div>
				</div>

				<div id="tableContainer" class="tablediv">
					<?php
					//make table
					if(count($songdata) > 0) {
						$clear_counter = array(0,0,0,0,0);
						$rank_counter = array(0,0,0,0,0,0);
						$table_string =  make_table($songdata, $clear_counter, $rank_counter);
						echo make_sum_table($mode, $clear_counter, $rank_counter);
						echo $table_string;
					}
					?>
				</div>
			</div>

			<!--
			<div id="twitbuttondiv">
				<a href="https://twitter.com/share" class="twitter-share-button" data-size="large">Tweet</a>
				<script>
				!function(d,s,id){
					var js,fjs=d.getElementsByTagName(s)[0], p=/^http:/.test(d.location)?'http':'https';
					if(!d.getElementById(id)) {
						js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
						fjs.parentNode.insertBefore(js,fjs);
					}
				}(document, 'script', 'twitter-wjs');
				</script>
			</div>
			-->
		</main>
		<script>
			window.onload = function () {
				<?php
					if($mode === "clear")
						echo 'CanvasJS.addColorSet("pastel", ["#FFC000", "#D9534F", "#FF8C00", "#40C000", "#606060", "#F0F0F0",]);';
					else
						echo 'CanvasJS.addColorSet("pastel", ["#CC0000", "#ffd040", "#BFC1C2", "#CD7F32", "#B0E57C", "#ACD1E9", "#F0F0F0"]);';
				?>

				if(<?php echo empty($datafullstring) ? "false" : "true" ; ?>){
					var chart = new CanvasJS.Chart("chartContainer", <?php echo $datafullstring;?>);
					chart.render();
				}
				// imagefiledownload();
				// resizeh1();

				//tablesorter setting
				$.tablesorter.addParser({
						id: 'Clear',
						is: function(s) {
							return false;
						},
						format: function(s) {
							return s.replace(/NOT-PLAYED/,0).replace(/FAILED/,1).replace(/EASY-CLEAR/,2).replace(/HARD-CLEAR/,4).replace(/FULL-COMBO/,5).replace(/CLEAR/,3);
						},
						type: 'numeric'
				});
				$.tablesorter.addParser({
					id: 'BP',
					is: function(s) {
						return false;
					},
					format: function(s) {
						return s.replace(/　/, 999999);
					},
					type: 'numeric'
				});
				$.tablesorter.addParser({
					id: 'LV',
					is: function(s) {
						return false;
					},
					format: function(s) {
						if(!($.isNumeric(s))) {
							return s.charCodeAt(0);
						}
						return s;
					},
					type: 'numeric'
				});
				$("#ScoreTable").tablesorter({
					headers: {
						0 : {sorter: false},
						1 : {sorter: 'LV'},
						4 : {sorter: 'Clear'},
						6 : {sorter: 'BP'}
					}
				});

				range_show();

				//animate sidebar
				$('#sidebar').stickyBar({
					top: 50
				});
				cbpAnimatedHeader();
			}

			//chart image export
			$("#download").click(function() {
				var image = $(".canvasjs-chart-canvas")[0].toDataURL("imgae/png").replace("image/png", "image/octet-stream");
				var filename = '<?php echo $tablename;?> CLEAR LAMP (Player：<?php echo $playername;?>).png';
				$(this).attr("href", image).attr("download", filename);
			});

			//filter tds
			$("#filter").change(function() {
				$(".song-tr").show();

				var min_lv = parseInt($('input[name="min-level"]').attr('value'));
				var max_lv = parseInt($('input[name="max-level"]').attr('value'));
				var char_lv = $('input[name="char-level"]').prop("checked");
				if(max_lv < min_lv)
					[min_lv, max_lv] = [max_lv, min_lv];
				$(".level-td").each(function() {
					var currnum = $(this).html();
					var isnum = $.isNumeric(currnum);
					var currnum_int = parseInt(currnum);
					if((char_lv==false) && (isnum == false) ) {
						$(this).closest("tr").hide();
					} else if(isnum && (currnum_int < min_lv || currnum_int > max_lv) ) {
						$(this).closest("tr").hide();
					}
				})
				range_show();

				$('#rank-filter').find('input:not(:checked)').each(function() {
					var rank = $(this).attr('value');
					if(rank === "C-F"){
						$(".td-C").closest("tr").hide();
						$(".td-D").closest("tr").hide();
						$(".td-E").closest("tr").hide();
						$(".td-F").closest("tr").hide();
					} else {
						$(".td-".concat(rank)).closest("tr").hide();
					}
				});

				$('#clear-filter').find('input:not(:checked)').each(function() {
					var clear = $(this).attr('value');
					$(".".concat(clear)).closest("tr").hide();
				});
			});

			//checkbox all
			$("input[value='ALL-RANK']").change(function() {
				$(".rank-checkbox").prop("checked", $("input[value='ALL-RANK']").prop("checked"));
			});
			$("input[value='ALL-CLEAR']").change(function() {
				$(".clear-checkbox").prop("checked", $("input[value='ALL-CLEAR']").prop("checked"));
			});
			$(".rank-checkbox").change(function() {
				if($(this).attr("value") !== 'ALL-RANK')
					$("input[value='ALL-RANK']").prop("checked", false);
			});
			$(".clear-checkbox").change(function() {
				if($(this).attr("value") !== 'ALL-CLEAR')
					$("input[value='ALL-CLEAR']").prop("checked", false);
			});
			//checkbox noplay
			$("input[name='noplay']").change(function() {
				var no_check = $(this).prop("checked");
				$("input[name='noplay']").prop("checked", no_check);
			});

			$('input[type=radio]').on('change', function() {
				$(this).closest("form").submit();
			});

			//level range show
			function range_show() {
				$('#range-show').html(function() {
					var min = $('[data-name="min-level"]').attr('data-value');
					var max = $('[data-name="max-level"]').attr('data-value');
					if(parseInt(max) < parseInt(min))
						[min, max] = [max, min];
					return min.concat("~", max);
				});
			}

			//animate header
			function cbpAnimatedHeader(){
				var docElem = document.documentElement,
					header = $('.lamp_header'),
					button = $('.shrinkbutton')
					didScroll = false,
					changeHeaderOn = 10;

				function init() {
					window.addEventListener( 'scroll', function( event ) {
						console.log("scrolled");
						if( !didScroll ) {
							didScroll = true;
							setTimeout( scrollPage, 250 );
						}
					}, false );
				}

				function scrollPage() {
					var sy = scrollY();
					if ( sy >= changeHeaderOn ) {
						header.addClass('lamp_header-shrink');
						button.addClass('shrinkbutton-shrink');
					}
					else {
						header.removeClass('lamp_header-shrink');
						button.removeClass('shrinkbutton-shrink');
					}
					didScroll = false;
				}

				function scrollY() {
					return window.pageYOffset || docElem.scrollTop;
				}

				init();

			}

		</script>
	</body>
</html>