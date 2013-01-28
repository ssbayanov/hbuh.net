<?php 
header("Content-Type: text/xml");
$dom = new DomDocument('1.0');
$dom->encoding = "UTF-8";
$errorsXML = $dom->createElement('errors');
$items = $dom->createElement('items');

class XMLError{
	const NO_CONNECTED = 0;
	const MYSQL = 1;
	const POST_DATA = 2;
	const TASK = 3;
	const UNCOMPLETE = 4;
	const TEXT = 5;
	const NOLOGIN = 6;
}

function addAttr($elm,$nameAttr,$value){
	global $dom;
	$attr = $dom->createAttribute($nameAttr);
	$attr->value = $value;
	$elm->appendChild($attr);
	return $attr;
}

function addError($type, $text = "", $line = 0){
	global $errorsXML,$dom;
	$error = $errorsXML->appendChild($dom->createElement('error'));
	addAttr($error, 'errorType', $type);
	addAttr($error, 'errorText', $text);
	addAttr($error, 'errorLine', $line);
}

function addMessage($text){
	$item = addItem("message");
	addAttr($item, 'text', $text);
}

function addItem($name = 'item',$parent = 0) {
	global $items,$dom;
	if(!$parent)
		$parent = $items;
	return $parent->appendChild($dom->createElement($name));
}

function printXML() {
	global $errorsXML,$dom,$items;
	if($errorsXML->childNodes->length > 0)
		$dom->appendChild($errorsXML);
	else
		$dom->appendChild($items);
		
	echo $dom->saveXML();
}
?>