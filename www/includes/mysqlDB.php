<?php

class mysqlDB{

	private $db = null;
	private $result = null; 

/*Конструктору передаем адрес, имя пользователя, пароль, имя базы данных, порт, а также кодировку для соединения.
По умолчанию используется utf8*/ 

	public function __construct($host, $user, $password, $base, $port = null, $charset = 'utf8')
	{
		$this->db = new mysqli($host, $user, $password, $base, $port);
		$this->db->set_charset($charset);
	} 

/*основная и единственная функция, которая выполняет запрос и возвращает результат его работы*/ 

	public function query($query, $itXML = true)
	{
		global $errors;
		if(!$this->db)
			return false; 

			if(is_object($this->result))/*очищаем предыдущий результат*/ 
			$this->result->free(); 

		$this->result = $this->db->query($query); /*выполняем запрос*/ 

		if($this->db->errno){/*если есть ошибки - выводим их*/
			$parentFunction = debug_backtrace();
			if($itXML && $dom){
				addError(XMLError::MYSQL, 
					'Вызваного из функции : "'.$parentFunction[1]["function"].'()" 
					в файле'.$parentFunction[1]["file"].'<br> Ошибка:'.$this->db->error, 
					$parentFunction[0]["line"]);
				}
			else
				$errors[] = "Строка №".$parentFunction[0]["line"]
				.".Ошибка MySQL №".$this->db->errno.'. Вызваного из функции: '.$parentFunction[1]["function"]
				.'() в файле '.$parentFunction[1]["file"]
				.'<br> Ошибка: '.$this->db->error.'<br>'.$query; 
		}

		if(is_object($this->result)) //если получены данные - возвращаем их.
		{
			while($row = $this->result->fetch_assoc())
				$data[] = $row;
			return $data;
		} 

		else if($this->result == FALSE) //если результат отрицательный - возвращаем false
			return false;
				
		/*если запрос (например UPDATE или INSERT) затронул какие-либо строки - возвращаем их количество*/ 
		else return $this->db->affected_rows;
	}
	public function errno()	{
		return $this->db->errno;
	}
	
	public function error()	{
		return $this->db->error;
	}
	
	public function lastInsertId()	{
		$q = "SELECT LAST_INSERT_ID()";
		$res = $this->query($q);
		return $res[0]["LAST_INSERT_ID()"]; 
	}
	
}

?>