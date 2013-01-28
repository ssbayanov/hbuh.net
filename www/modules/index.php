<div id="charts_div" style="width: 800px;"></div>

	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawCharts);
      
		<?php
			require_once $inclPath."dates.php";
			$dates = getInterval("currentMonth");
			function getStatMassiv($startDate,$endDate,$grop){
				global $DB,$USER;
				$gtext = "";
				switch($grop){
					case "day": $gtext = "DAY(date)"; break;
					case "week": $gtext = "WEEK(date)"; break;
					case "month": $gtext = "MONTH(date)"; break;
				}
		
				$query = "SELECT 
					SUM(IF (cost < 0, cost*-1, 0)) as expens, 
					SUM(IF (cost > 0, cost, 0)) as income,
					{$gtext} as dat
				FROM hbuh_checks 
				WHERE date BETWEEN 
							STR_TO_DATE('{$startDate}', '%Y-%m-%d') 
							AND STR_TO_DATE('{$endDate}', '%Y-%m-%d')
							AND id_user = {$USER->getId()}
				GROUP BY {$gtext}";
						
				$result = $DB->query($query,0);
				return $result;
		  }
		  
		function addGraph($header,$axisTitle,$range,$group){
			$dates = getInterval($range);
			$dat = getStatMassiv($dates['startDate'],$dates['endDate'],$group);
			if($dat):
				$expens = 0;
				$income = 0;?>
				var arr = [
				['<?=$axisTitle?>', 'Расходы', 'Доход']<?foreach($dat as $row):	$expens += $row['expens']; $income += $row['income'];?>,
				['<?=$row["dat"]?>',	<?=$expens?>,	<?=$income?>]<?endforeach;?>	
				]
				drawChart("<?=$header?>",arr);<?
			endif;
		}
		
		?>
	function drawCharts() {
		<?
		addGraph("Текущий месяц","День","currentMonth","day");
		addGraph("Прошлый месяц","День","lastMonth","day");
		addGraph("Прошлый год","Неделя","lastYear","week");
		?>
	}
	function drawChart(header,arr) {
		var data = google.visualization.arrayToDataTable(arr);
		var options = {
		  title: header//,
		  //curveType: 'function'
		};
        
		var chartDiv = document.createElement('div');
		chartDiv.style.width = "800px";
		chartDiv.style.heigth = "800px";
		ge("charts_div").appendChild(chartDiv);
		var chart = new google.visualization.LineChart(chartDiv);
        chart.draw(data, options);
		<?/*else:?>
			ge('chart_div').textContent = "Нет данных за период";
		<?endif*/?>
      }
    </script>