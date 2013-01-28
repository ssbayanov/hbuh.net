<?php 
class hbuhUser{
	private $user = array(
		"id" => 0,
		"name" => "Незарегистрированный",
		"login" => "unlogin",
		"hash" => "0",
		"ip" => "0"
	);
	
	public function __construct()
	{
		global $_COOKIE, $DB, $errors, $domain;
		if (isset($_COOKIE['hash'])){   
			$id = strtok($_COOKIE['hash'], ".");
			$hash = strtok(".");
			$query = "SELECT *,INET_NTOA(ip) as ip
				FROM hbuh_users 
				WHERE id = '".intval($id)."' LIMIT 1";
			$result = $DB->query($query);
			/*var_dump($id);*/
			//var_dump($hash);
			if(count($result) > 0){
				$user = $result[0];
				//$error[] = var_export($user,TRUE);
				//$error[] = var_export($_COOKIE,TRUE);
				//var_dump($_SERVER);*/
				if(($user['hash'] !== $hash) or ($user['id'] !== $id)
					or (($user['ip'] !== $_SERVER['REMOTE_ADDR'])  and ($user['ip'] !== "0"))){
					setcookie("hash", "", 0, "/", $domain);
					$errors[] = "Авторизация не удалась";
					//$errors[] = $user['hash']." ".$hash;
				}
				else{
					//$errors[] = 'Привет, '.$user['login'].'!';
					$this->user = $user;
					
					setcookie("hash", $id.".".$hash, time()+60*60*24, "/", $domain);
				}
			}
			else{
				$errors[] = "$id пользователя в системе нет";
				//$errors[] = 
				}
		}
		else{
			$errors[] = "Куки не нашлись";//var_dump($_COOKIE);
			setcookie("hash", "", 0, "/", $domain);
		}
	}
	
	private function generateCode($length = 6) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length) {
			$code .= $chars[mt_rand(0,$clen)];  
		}
		return $code;
	}
	
	public function login($userName,$passWord,$attachIp){
		global $DB, $errors, $messages, $_GET, $domain;
		# Вытаскиваем из БД запись, у которой логин равняеться введенному
		$result = $DB->query("SELECT id, password FROM hbuh_users WHERE login='{$userName}' LIMIT 1");
		# Сравниваем пароли
		//var_dump($result);
		if($result[0]['password'] === md5(md5($passWord)))
		{
			# Генерируем случайное число и шифруем его
			$result = $result[0];
			$hash = md5($this->generateCode(10));
			
				
			if($attachIp)
			{
				# Если пользователя выбрал привязку к IP
				# Переводим IP в строку
				$insip = ", ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
			}
			
			# Записываем в БД новый хеш авторизации и IP
			$DB->query("UPDATE hbuh_users SET hash='".$hash."' ".$insip." WHERE `id` = ".$result['id']);
			
			# Ставим куки
			$domain = $_SERVER['HTTP_HOST'];
			setcookie("hash", $result['id'].".".$hash, time()+60*60*24, "/", $domain);
			
			# Переадресовываем браузер
			if(!empty($_GET["lastPage"]))
				header("Location: ".$_GET["lastPage"]);
			else
				header("Location: /");
			//$messages[] = "Удачный вход";
		}
		else
		{
			$errors[] = "Вы ввели неправильный логин/пароль";
			//$errors[] = "Должен быть ".$result[0]['password']." получилось ".md5(md5($passWord));
		}
	}
	
	public function unlogin($redirect = "user/login.html"){
		global $DB, $domain;
		$hash = md5($this->generateCode(10));
		$DB->query("UPDATE hbuh_users SET hash='".$hash."', ip=0 WHERE `id` = ".$this->getId());
		setcookie("hash", "", 0, "/", $domain);
		header("Location: /{$redirect}");
	}
	
	public function registrate($login,$password,$eMail){
		global $errors, $DB;
		// проверям логин
		if(!preg_match("/^[a-zA-Z0-9]+$/",$login))
		{
			$errors[] = "Логин может состоять только из букв английского алфавита и цифр";
		}
		
		if(strlen($login) < 3 or strlen($login) > 30)
		{
			$errors[] = "Логин должен быть не меньше 3-х символов и не больше 30";
		}
		
		if(strlen($password) < 3)
		{
			$errors[] = "Пароль должен быть не меньше 3-х символов";
		}
		
		// проверяем, не сущестует ли пользователя с таким именем
		$result = $DB->query("SELECT COUNT(id) FROM hbuh_users WHERE login='{$login}'");
		if($result[0]["COUNT(id)"] > 0)
		{
			$errors[] = "Пользователь с таким логином уже существует в базе данных";
		}
		
	   // Если нет ошибок, то добавляем в БД нового пользователя
		if(count($errors) == 0)
		{
			//формируем текст сообщения до отправки пароля
			$message = "Вас приветствуе \"Домашняя бухгалтерия\" \r\n
				На ваш e-mail поступила заявак не регистрацию.\r\n
				Ваш логин: {$login}
				Пароль: {$password} ";
			# Убераем лишние пробелы и делаем двойное шифрование
			$password = md5(md5(trim($password)));
			
			$DB->query("INSERT INTO hbuh_users SET login='{$login}', password='{$password}', email='{$eMail}'");
			
			
			mail($eMail, "the subject", $message, 
				"From: Регистратор Домашней Бухгалтерии <hbuhregistrar@robototeh.com>"); 
			$query = "INSERT 
					INTO `hbuh_categories` (`id_user`,`name`, `left`, `right`, `parent_id`) 
					VALUES (".$DB->lastInsertId().",'Категории:', 1, 2, -1)";
			$DB->query($query);
			header("Location: /");
		}
	}
	
	public function doTask($task){
		switch($task){
			case "login":
				$this->login($_POST["UserName"],$_POST["PassWord"],!empty($_POST["attachIp"]));
				break;
			case "register": 
				$this->registrate($_POST["UserName"],$_POST["PassWord"],$_POST["EmailAddress"]);
				break;
			default:
				$this->unlogin();
				break;
		}
	}
	
	public function getLogin(){
		return $this->user["login"];
	}
	
	public function getId(){
		return $this->user["id"];
	}
	
	public function getName(){
		return $this->user["name"];
	}
	
}
?>
