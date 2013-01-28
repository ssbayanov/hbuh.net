<?php

include_once "../config.php";
include_once $inclPath."xml.php";

$cheks = $dom->createElement('cheks');

function addChek($c) {
	$check = addItem("check");
	addAttr($check, 'checkId', $c["id"]);
	addAttr($check, 'date', date("d.m.Y",strtotime($c["date"])));
	addAttr($check, 'cost', number_format($c["cost"],2,',',' '));
	addAttr($check, 'shopName', $c["shopname"]);
	addAttr($check, 'shopAdress', $c["shopadress"]);
	addAttr($check, 'success', $c["success"]);
}
	
function loadCheks($startDate,$endDate){
	global $DB,$USER;
	$query = "SELECT c.*, s.name as shopname, s.adress as shopadress
			FROM `hbuh_checks` c
			LEFT OUTER JOIN `hbuh_shops` s on c.shop = s.id ".(!(empty($startDate) || empty($startDate)) ? "
			WHERE c.id_user = ".$USER->getId()."  
				AND c.date BETWEEN STR_TO_DATE('$startDate', '%Y-%m-%d') 
					AND STR_TO_DATE('$endDate', '%Y-%m-%d')" : "")."
			ORDER BY c.date";
	$result = $DB->query($query);
	if($result)
		foreach ($result as $row)
			addChek($row);
}

loadCheks($_POST["startDate"],$_POST["endDate"]);

printXML();?>
