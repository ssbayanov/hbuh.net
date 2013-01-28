<?php
include_once "../config.php";
include_once $inclPath."xml.php";

function delete($from, $id, $where = 'id'){
	global $DB;
	$query = "DELETE FROM `{$from}`
		WHERE {$where} = {$id}";
	$result = $DB->query($query);
	if ($result)
		return 1;
	return 0;
}

function insertCheck($shop, $total, $date, $success, $comment){
global $DB,$USER;
	$query = "INSERT INTO hbuh_checks
        (`id_user`,`shop`,`cost`,`date`,`success`,`comment`)
		VALUES (".$USER->getId().",$shop, $total, '$date', '$success', '$comment')";
	$result = $DB->query($query);
	if ($result){ 
		return $DB->lastInsertId();}
 	return 0;       
}

function updateCheck($id, $shop, $total, $date, $success, $comment){
global $DB,$USER;
	$query = "UPDATE hbuh_checks 
		SET `shop` = {$shop},
			`cost` = {$total},
			`date` = '{$date}',
			`success` = {$success},
			`comment` = '{$comment}'
		WHERE id = {$id}";
	$result = $DB->query($query);
	if ($result){ 
		return $DB->lastInsertId();}
 	return 0;       
}

function insertPosititon($checkId,$position){
	global $DB,$USER;
	$query = "INSERT INTO hbuh_purchases
	(`id_user`,`id_cheсk`,`id_product`,`cost`,`quantum`)
	VALUES (".$USER->getId().",{$checkId},".$position['product'].",".$position['cost'].",".$position['quantum'].")";
	$result = $DB->query($query);
	if ($result)
		return 1;
	return 0;       
}

function insertProduct($name,$category){
	global $DB,$USER;
	$category = getCategory($category);
	$query = "INSERT INTO hbuh_products
	(`id_user`,name,category)
	VALUES (".$USER->getId().",'{$name}',{$category})";
	$result = $DB->query($query);
	if ($result){
		addMessage("Продукт \"{$name}\" добавлен");
		return 1;}
	return 0;       
}


function getShop($shop) {
	global $DB,$USER;
	if (!is_numeric($shop)){
		$name = substr($shop,0,strpos($shop,','));
		$adress = trim(substr($shop,strpos($shop,',')+1));
		//echo $shop." : ".$name." : ".$adress;
		$query = "SELECT `id` 
			FROM `hbuh_shops` 
			WHERE `name` = '$name'
				AND `adress` = '$adress'
				AND `id_user` = ".$USER->getId()."
			LIMIT 1";
		$result = $DB->query($query);
		if(count($result)){
			$shop = $result[0]["id"];
			}
		else {
			$query = "INSERT INTO hbuh_shops".
				"(`id_user`,`name`,`adress`)".
				"VALUES(".$USER->getId().",'$name','$adress')";
		
			$result = $DB->query($query);
			//addMessage("Магазин \"{$name}, {$adress}\" добавлен");
			$shop = $DB->lastInsertId();}
		}
	return $shop;
}

function updateTreeCategory($count, $parentLeft){
	global $DB,$USER;
	$query = "UPDATE `hbuh_categories` SET `left` = `left` + ".($count*2)." WHERE `left` > {$parentLeft} AND `id_user` = ".$USER->getId()."";
	$result = $DB->query($query);
	
	$query = "UPDATE`hbuh_categories` SET `right` = `right` + ".($count*2)." WHERE `right` > {$parentLeft} AND `id_user` = ".$USER->getId()."";
	$result = $DB->query($query);
}

function getCategory($category){
	global $DB,$USER;
	if (!is_numeric($category)){
		$cat = trim(strtok($category, ","));
		$result = $DB->query('SELECT id FROM `hbuh_category` WHERE id_user = '.$USER->getId().' AND `name` = "category" ORDER BY id LIMIT 1');
		$parent = $result[0]['id'] || 1;
		$parentLeft = 1;
		
		while($cat){
			$query = "SELECT `left`, `id` 
				FROM `hbuh_categories` 
				WHERE `name` = '{$cat}' 
					AND `parent_id` = {$parent}
					AND `id_user` = ".$USER->getId()."";
			$result = $DB->query($query);
			if(count($result)){
				$row = $result[0];
				$parent = $row["id"];
				$parentLeft = $row["left"];
				$count--;
				}
			else {
				updateTreeCategory(1, $parentLeft);
				$query = "INSERT INTO `hbuh_categories` (`id_user`,`name`, `left`, `right`, `parent_id`) 
					VALUES (".$USER->getId().",'{$cat}', ".($parentLeft+1).", ".($parentLeft+2).", {$parent})";
				$result = $DB->query($query);
				//var_dump($result);
				//echo $query;
				if(!$DB->errno || $result != false)
					addMessage("Категория \"{$cat}\" добавлена");
				else
					addError(XMLError::TEXT,"Ошибка создания категории \"{$cat}\"",__LINE__);
				$parent = $DB->lastInsertId();
				$parentLeft++;
				}
			$cat = trim(strtok(","));
			}
		}
	else
		$parent = $category;
	return $parent;
}

function modifyCheck(){
	global $_POST;
	$positions = loadPositions();
	if(count($positions)){
		if(!empty($_POST["id"]) && !empty($_POST["shop"]) && !empty($_POST["total"]) && !empty($_POST["date"])){
			$shop = getShop($_POST["shop"]);
			$checkId = $_POST["id"];
			updateCheck($_POST["id"],$shop, $_POST["total"], $_POST["date"], $_POST["success"], $_POST["comment"]);
			delete('hbuh_purchases', $checkId, 'id_cheсk');
			if(addPositions($checkId,$positions))
				return 1;
			else{
				addError(XMLError::TEXT,"Ошибка добавления позиций",__LINE__);
				delete('hbuh_checks',$checkId);
				delete('hbuh_purchases', $checkId, 'id_cheсk');
				return 0;}
		}
		else{ 
			addError(XMLError::POST_DATA,"",__LINE__);
			return 0;}}
	else
		addError(XMLError::TEXT,"Не удалось получить позиции",__LINE__);
}

function addCheck(){
	global $_POST;
	if($positions = loadPositions())
		if(!empty($_POST["shop"]) && !empty($_POST["total"]) && !empty($_POST["date"])){
			$shop = getShop($_POST["shop"]);
			$checkId = insertCheck($shop, $_POST["total"], $_POST["date"], $_POST["success"], $_POST["comment"]);
			//var_dump($checkId);
			if(addPositions($checkId,$positions))
				return $checkId;
			else{
				addError(XMLError::TEXT,"Ошибка добавления позиций",__LINE__);
				delete('hbuh_checks',$checkId);
				delete('hbuh_purchases', $checkId, 'id_cheсk');
				return 0;}
		}
		else{ 
			addError(XMLError::POST_DATA,"",__LINE__);
			return 0;}
}

function loadPositions(){
	global $_POST;
	$positions = array();
	$i = 1;
	while(!empty($_POST["product$i"]))
		if(!empty($_POST["cost$i"]) && !empty($_POST["quantum$i"])){
			$position = array(
				"product" => $_POST["product$i"],
				"cost" => $_POST["cost$i"],
				"quantum" => $_POST["quantum$i"]);
			$positions[] = $position;
			$i++;}
		else {
			addError(XMLError::POST_DATA,"",__LINE__);
			return 0;}
	return $positions;
}

function addPositions($checkId,$positions) {
	foreach($positions as $position){							
		if(!insertPosititon($checkId,$position)){
			addError(XMLError::MYSQL,mysql_error() ,__LINE__);
			return 0;}}
		return 1;
}

if ($USER->getId() != 0)
	if (!empty($_POST["task"]) || !empty($_GET["task"])) {
		$task = !empty($_POST["task"])? $_POST["task"] : $_GET["task"];
		switch($task){
			case "add":
				if($id = addCheck()){
					addMessage("success");
					addMessage($id);}
				else
					addError(XMLError::TEXT,"Ошибка добавления чека",__LINE__);
				break;
			case "addProduct":
				insertProduct($_POST["name"],$_POST["category"]);
				break;
			case "modify":
				if(modifyCheck())
					addMessage("Чек изменён");
				break;
			default: 
				addError(XMLError::TASK, "Задача: ".$_POST["task"] ,__LINE__);
				}
	}
else
	addError(XMLError::NOLOGIN, "" ,__LINE__);
	
printXML();
