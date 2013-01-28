<?php 
	require_once "config.php";
	require_once $inclPath."dates.php";
	if(!empty($_POST["usertask"])){
		$USER->doTask($_POST["usertask"]);
	}
	
	if(!empty($_POST["doReinstall"]))
		if($_POST["doReinstall"] == "yes")
		reinstall();
		
	if(!empty($_POST["mess"]))
		$messages[] = $_POST["mess"];
	
	if (!empty($_GET["module"])){
		switch($_GET["module"]){
			case "check":
			case "viewChecks": 
			case "viewCheck":
			case "index": 
			case "products": 
			case "categories": 
				if($USER->getId() === 0){
					header("Location: /user/login.html?lastPage=".$_SERVER['REQUEST_URI']);}
			case "user":					
			case "404":
			case "reinstall": $tpl = $_GET["module"]; break;
			case "install":
				reinstall();
				break;
			default: $tpl = "404";
			}
		$pagetitle = $_GET["module"];
	}
	else
		$tpl = "404";
	
	//appand errors messages
	$errorText = "";
	/*var_dump($errors);
	var_dump($USER);*/
	$errorText = '<script type="text/javascript">';
	if(count($errors) > 0){
		foreach($errors as $error){
			$errorText .= "showError(\"".str_replace("\n","<br>",$error)."\");\n";
		}
	}
	else 
		if(count($messages) > 0){
			foreach($messages as $message){
				$errorText .= "showMessage(\"{$message}\");\n";
			}
		}
		
	$errorText .= '</script>';
	include $tplPath."index.html";
?>
