<?php
include_once "../config.php";
require_once $inclPath."dates.php";

function getCost($startDate,$endDate, $direction){
	global $DB,$USER;
		$query = "SELECT SUM(cost) as total
				FROM `hbuh_checks`
				WHERE cost $direction 0 
					AND id_user = ".$USER->getId()."
					AND success > 0
					AND date BETWEEN 
						STR_TO_DATE('$startDate', '%Y-%m-%d') 
						AND STR_TO_DATE('$endDate', '%Y-%m-%d')";
		$result = $DB->query($query);
		if($result)
		{
			foreach($result as $row);
				return $row['total'];
		}
		else
			echo "<br>Ошибка.<br>";
}

	
function getStat($dates){
	return array(
		"consumption" => getCost($dates['startDate'],$dates['endDate'],"<"),
		"income" => getCost($dates['startDate'],$dates['endDate'],">"));
}
$stat = getStat(getInterval("currentMonth"));?>
﻿<div class="header">Статистика</div>
<div class="statDetail">
	<div class="header">За <?=getNameMonth()?> месяц</div>
	Расход:	<?=toRub(-$stat["consumption"])?><br>
	Доход:	<?=toRub($stat["income"])?><br>
</div>
<?$stat = getStat(getInterval("lastMonth"));?>
<div class="statDetail">
	<div class="header">За <?=getNameMonth("lastMonth")?> месяц</div>
	Расход:	<?=toRub(-$stat["consumption"])?><br>
	Доход:	<?=toRub($stat["income"])?><br>
</div>
<?$stat = getStat(getInterval("currentYear"));?>
<div class="statDetail"><div class="header">
	За <?=date('Y')?> год</div>
	Расход:	<?=toRub(-$stat["consumption"])?><br>
	Доход:	<?=toRub($stat["income"])?><br>
</div>
<?$stat = getStat(getInterval("lastYear"));?>
<div class="statDetail"><div class="header">
	За <?=date('Y',strtotime("Last Year"))?> год</div>
	Расход:	<?=toRub(-$stat["consumption"])?><br>
	Доход:	<?=toRub($stat["income"])?><br>
</div>
