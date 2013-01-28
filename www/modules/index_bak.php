<h2>За текущий месяц</h2>
<div id="chart_div" style="width: 900px; height: 500px;"></div>

	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
		<?php
		  $query = "SELECT 
				SUM(IF (cost < 0, cost, 0)) as expens, 
				SUM(IF (cost > 0, cost, 0)) as income,
				date
			FROM hbuh_checks 
			WHERE date  > LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH
				AND date < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
			GROUP BY date";
				
		  $result = $DB->query($query,0);
		  if($result):?>
			
			var data = google.visualization.arrayToDataTable([
			  ['День', 'Доходы', 'Расходы']<?foreach($result as $row):?>,
			  ['<?=$row['date']?>',	<?=$row['expens']?>,	<?=$row['income']?>]	
				<?endforeach;?>
			]);

			var options = {
			  title: 'Производительность компании',
			  curveType: 'function',
			  width: 600
			};

        var chart = new google.visualization.LineChart(ge('chart_div'));
        chart.draw(data, options);
		<?else:?>
			ge('chart_div').textContent = "Нет данных за период";
		<?endif?>
      }
    </script>