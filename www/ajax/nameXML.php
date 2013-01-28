<?php
include_once "../config.php"; /*Файл настроек подключения*/
include_once $inclPath."xml.php";

function addPosition($p){
	$positon = addItem("position");
	addAttr($positon, 'posId', $p["id"]);
	addAttr($positon, 'name', $p["name"]);
}

function addProduct($p){
	$product = addItem("product");
	addAttr($product, 'id', $p["id"]);
}

function addShop($s){
	$shop = addItem("shop");
	addAttr($shop, 'shopId', $s["id"]);
	addAttr($shop, 'name', $s["name"]);
	addAttr($shop, 'adress', $s["adress"]);
}

function addCategory($c){
	global $dom;
	if(!$dom->getElementById($c["id"])){
		$category = addItem("category", $dom->getElementById($c["parent_id"]));
		addAttr($category, 'id', $c["id"]);
		$category->setIdAttribute("id",true);
		addAttr($category, 'name', $c["name"]);
		addAttr($category, 'left', $c["left"]);
		addAttr($category, 'right', $c["right"]);
		addAttr($category, 'parentId', $c["parent_id"]);}
}

function getName($name) {
	global $DB,$USER;
	
	$query = "SELECT id, name
			FROM `hbuh_products`
			WHERE name LIKE '$name%'
				AND `id_user` = ".$USER->getId()."
			LIMIT 5";
	$result = $DB->query($query);
	if($result)
		foreach($result as $row)
			addPosition($row);
}

function getProductId($name) {
	global $DB,$USER;
	
	$query = "SELECT id
			FROM `hbuh_products`
			WHERE name = '{$name}'
				AND `id_user` = ".$USER->getId()."
			LIMIT 1";
	$result = $DB->query($query);
	if($result)
		foreach ($result as $row)
			addProduct($row);

}

function getShop($name) {
	global $DB,$USER;
	
	$query = "SELECT *
			FROM `hbuh_shops`
			WHERE name LIKE '$name%'
				AND `id_user` = ".$USER->getId();
	$result = $DB->query($query);
	if($result)
		foreach ($result as $row)
			addShop($row);
}

function getCategory($name) {
	global $DB,$USER;
	
	$query = "SELECT `left`, `right`
			FROM `hbuh_categories`
			WHERE `name` LIKE '$name%'
				AND `id_user` = ".$USER->getId()."
			ORDER BY `left` ASC";
	$result = $DB->query($query);
	if($result)
		foreach ($result as $row){
			$left =  $row["left"];
			$right =  $row["right"];
				$query = "SELECT * FROM `hbuh_categories` 
						WHERE `left` <= {$left} 
							AND `right` >= {$right} 
							AND `id_user` = ".$USER->getId()."
						ORDER BY `left` ASC";
				$res = $DB->query($query);
				foreach ($res as $r){
					addCategory($r);}}
}

function getCategories() {
	global $DB,$USER;
	
	$query = "SELECT *
			FROM `hbuh_categories`
			WHERE `id_user` = ".$USER->getId()."
			ORDER BY `parent_id`, `name` ASC";
	$result = $DB->query($query);
	if($result)
		foreach ($result as $row){
			addCategory($row);}
}

if (!empty($_POST["task"]) || !empty($_GET["task"])) {
	$task = !empty($_POST["task"])? $_POST["task"] : $_GET["task"];
	switch($task){
		case "product":
			getName($_POST["name"]);
			break;
		case "productId":
			getProductId($_POST["name"]);
			break;
		case "shop":
			getShop($_POST["name"]);
			break;
		case "category":
				getCategory($_POST["name"]);
			break;
		case "categories":
				getCategories();
			break;
		default: 
			addError(XMLError::TASK, 'Неизвестная задача "'.$_POST["task"].'" файл "'.__FILE__.'"');
			}
}
else
	addError(XMLError::POST_DATA);
	
printXML();?>