<?php

$HOST = localhost;
$LOGIN = root;
$PASS = "";
$DBname = "hbuh";


//Direcotries:
$workPath = dirname(__FILE__); //Work
$modPath = dirname(__FILE__)."/modules/"; //Modules
$tplPath = dirname(__FILE__)."/template/"; //Temlate
$inclPath = dirname(__FILE__)."/includes/"; //Includes
$domain = $_SERVER['HTTP_HOST'];

$errors = null; //massiv for errors;
$messages = null; //massiv for messages;

require_once "includes/mysqlDB.php";
require_once "includes/user.php";

$DB = new mysqlDB($HOST,$LOGIN,$PASS,$DBname) or die ("Ошибка создания обёртки MySQL");
$USER = new hbuhUser();

function reinstall(){
	global $DB,$HOST,$LOGIN,$PASS,$DBname,$USER,$errors,$messages;
	$query = "CREATE DATABASE 
	IF NOT EXISTS {$DBname} 
	CHARACTER SET utf8 
	COLLATE utf8_general_ci;";
	$DB->query($query,0);
	if(!$DB->errno())
	{
		$messages[] = "База создана или существует существует<br>";
		$query = "USE {$DBname};";
		$result = $DB->query($query,0);
		$query = "DROP TABLE IF EXISTS `hbuh_purchases`;";
		$DB->query($query);
		$query = "CREATE TABLE `hbuh_purchases` (
			`id` int(11) NOT NULL auto_increment,
			`id_user` int(11) NOT NULL,
			`id_cheсk` int(11) NOT NULL,
			`id_product` int(11) NOT NULL,
			`cost` DOUBLE NOT NULL,
			`quantum` DOUBLE NOT NULL,
			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$DB->query($query,0);
		if(!$DB->errno()){
			$messages[] =  "Таблица покупок создана<br>";
			}
		else {
			$errors[] =  "Ошибка создания таблицы покупок<br>".$DB->error();
			return;}
		$query = "DROP TABLE IF EXISTS `hbuh_products`;";
		$DB->query($query,0);
		$query = "CREATE TABLE `hbuh_products` (
			`id` int(11) NOT NULL auto_increment,
			`id_user` int(11) NOT NULL,
			`name` varchar(100) NOT NULL,
			`category` int(11) NOT NULL,
			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$DB->query($query,0);
		if(!$DB->errno()){
			$messages[] =  "Таблица товаров создана<br>";
			}
		else {
			$errors[] =  "Ошибка создания таблицы товаров<br>".$DB->error();
			return;}
		$query = "DROP TABLE IF EXISTS `hbuh_checks`;";
		$DB->query($query,0);
		$query = "CREATE TABLE `hbuh_checks` (
			`id` int(11) NOT NULL auto_increment,
			`id_user` int(11) NOT NULL,
			`date` DATE NOT NULL,
			`shop` int(11) NOT NULL,
			`cost` DOUBLE NOT NULL,
			`success` TINYINT(1) NOT NULL,
			`comment` TEXT,
			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$DB->query($query,0);
		if(!$DB->errno()){
			$messages[] =  "Таблица чеков создана<br>";
			}
		else {
			$errors[] =  "Ошибка создания таблицы чеков<br>".$DB->error();
			return;}
		$query = "DROP TABLE IF EXISTS `hbuh_shops`;";
		$DB->query($query,0);
		$query = "CREATE TABLE `hbuh_shops` (
			`id` int(11) NOT NULL auto_increment,
			`id_user` int(11) NOT NULL,
			`name` TEXT NOT NULL,
			`adress` TEXT,
			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$DB->query($query,0);
		if(!$DB->errno()){
			$messages[] =  "Таблица магазинов создана<br>";
			}
		else {
			$errors[] =  "Ошибка создания таблицы магазинов<br>".$DB->error();
			return;}
		$query = "DROP TABLE IF EXISTS `hbuh_categories`;";
		$DB->query($query,0);
		$query = "CREATE TABLE `hbuh_categories` (
			`id` int(11) NOT NULL auto_increment,
			`id_user` int(11) NOT NULL,
			`left` int(11) NOT NULL,
			`right` int(11) NOT NULL,
			`parent_id` int(11) NOT NULL,
			`name` TEXT NOT NULL,
			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$DB->query($query,0);
		if(!$DB->errno()){
			$messages[] =  "Таблица категорий создана<br>";
			}
		else {
			$errors[] =  "Ошибка создания таблицы категорий<br>".$DB->error();
			return;}
			
			
		$query = "DROP TABLE IF EXISTS `hbuh_users`;";
		$DB->query($query,0);
		$query = "CREATE TABLE `hbuh_users` (
			`id` int(11) NOT NULL auto_increment,
			`id_user` int(11) NOT NULL,
			`login` TEXT NOT NULL,
			`password` TEXT,
			`email` TEXT,
			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$DB->query($query,0);
		if(!$DB->errno()){
			$messages[] =  "Таблица магазинов создана<br>";
			}
		else {
			$errors[] =  "Ошибка создания таблицы магазинов<br>".$DB->error();
			return;}
		$query = "DROP TABLE IF EXISTS `hbuh_users`;";
		$DB->query($query,0);
		$query = "CREATE TABLE `hbuh_users` (
			`id` int(11) NOT NULL auto_increment,
			`login` TEXT NOT NULL,
			`email` TEXT NOT NULL,
			`password` CHAR(32) NOT NULL,
			`ip` INT UNSIGNED,
			`hash` CHAR(32) NOT NULL,
			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$DB->query($query,0);
		if(!$DB->errno()){
			$messages[] =  "Таблица пользователей создана<br>";
			}
		else {
			$errors[] =  "Ошибка создания таблицы пользователей<br>$result";
			return;}
			
		$USER->registrate("admin","19*35*7*quit","ssbayanov@gmail.com");
	}
}
?>
