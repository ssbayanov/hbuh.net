<?php
function getInterval($range){
	switch($range){
		case "currentMonth": 
			$startDate = date("Y-m-01");
			$endDate = date("Y-m-t");
			break;
		case "lastMonth": 
			$startDate = date("Y-m-01", mktime(0, 0, 0, date("m")-1, 1,   date("Y"))); 
			$endDate = date("Y-m-t", mktime(0, 0, 0, date("m")-1, date("t"),   date("Y")));
			break;
		case "currentYear": 
			$startDate = date("Y-01-01");
			$endDate = date("Y-12-31");
			break;
		case "lastYear": 
			$startDate = date("Y-m-01", mktime(0, 0, 0, 1, 1,   date("Y")-1)); 
			$endDate = date("Y-m-t", mktime(0, 0, 0, 12, 31,   date("Y")-1));
			break;
		default: $startDate = strtok($range, ":");
				$endDate = strtok(":");}
		return array(
			"startDate" => $startDate,
			"endDate" => $endDate);
}

$dayWeek = array("Вс","Пн","Вт","Ср","Чт","Пт","Сб");
$dayWeekNorm = array("Пн","Вт","Ср","Чт","Пт","Сб","Вс");
$months = array(
	'Январь',
	'Февраль',
	'Март',
	'Апрель',
	'Май',
	'Июнь',
	'Июль',
	'Август',
	'Сентябрь',
	'Октябрь',
	'Ноябрь',
	'Декабрь');

function getNameMonth($month = "currentMonth"){
	global $months;
	switch($month){
		case "currentMonth": 
			return $months[date('m')-1];
		case "lastMonth": 
			return $months[date('m',mktime(0, 0, 0, date("m")-1, date("t"),   date("Y")))-1];
		default: 
			return $months[date('m',strtotime($month))-1];}
}

function toRub($value){
	return number_format($value,2,',',' ').'р';
}
?>