<?
switch ($_GET['task']){
	case 'login':?><form method="POST">
			Логин <input name="UserName" type="text"><br>
			Пароль <input name="PassWord" type="password"><br>
			Прикрепить к IP (рекомендуется) <input type="checkbox" name="attachIp" checked="true"><br>
			<input name="usertask" type="hidden" value="login">
			<input name="submit" type="submit" value="Войти">
			</form> <?
		break;
	case 'register':
		if($USER->getId() != 0):?> Вы уже зарегестрированны под логином <?=$USER->getLogin()?><br>
				Если вы хотите зарегестрировать нового пользователя нажмите выход.
				<form method="POST">
				<input name="usertask" type="hidden" value="unlogin">
				<input name="submit" type="submit" value="Выход">
				</form> <?
		else :?> <form method="POST">
				Логин <input name="UserName" type="text"><br>
				Пароль <input name="PassWord" type="password"><br>
				Подтверждение пароля <input name="ConfirmPassWord" type="password"><br>
				E-mail <input name="EmailAddress" type="text"><br>
				<input name="usertask" type="hidden" value="register">
				<input name="submit" type="submit" value="Зарегистрироваться">
				</form><?
		endif;
		break;
	case 'unlogin':?>
			Вы действительно хотите выйти?.
			<form method="POST">
			<input name="usertask" type="hidden" value="unlogin">
			<input name="submit" type="submit" value="Да"><button onclick="javascript:history.back(); return false;">Нет</button>
			</form><? 
		break;}
?>
