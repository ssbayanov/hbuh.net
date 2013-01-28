<?php

include_once "../config.php";
include_once $inclPath."xml.php";

function doSuccess($id, $success){
	global $DB;
	$query = "UPDATE `hbuh_checks`
		SET success = $success
		WHERE id = $id";
	$result = $DB->query($query);
	if ($result)
		return 1;
	return 0;
}

function doDelete($id){
	global $DB;
	$query = "DELETE FROM `hbuh_checks` 
		WHERE id = {$id}";
	$result = $DB->query($query);
	$query = "DELETE FROM `hbuh_purchases` 
		WHERE id_cheсk = {$id}";
	$result = $DB->query($query);
	if ($result)
		return 1;
	return 0;
}
switch($_POST["task"]){
	case "doSuccess": 
		if(doSuccess($_POST["id"], 1))
			addMessage("Чек проведён");
		break;
	case "doUnsuccess": 
		if(doSuccess($_POST["id"], 0))
			addMessage("Проведение отменено");
		break;
	case "doDelete": 
		if(doDelete($_POST["id"]))
			addMessage("Чек удалён");
		break;
	default: break;
}

printXML();?>
