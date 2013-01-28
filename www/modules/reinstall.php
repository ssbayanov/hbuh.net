<? if($USER->getLogin == "admin"):?>
	<h2>Переустановка База данных</h2>
	Вы действительно хотите переустановить БД?<br>
	ВНИМАНИЕ! Все данные будут удалены.<br>
	<form method="POST">
	<input name="doReinstall" type="hidden" value="yes">
	<input name="submit" type="submit" value="Да"><button onclick="javascript:history.back(); return false;">Нет</button>
	</form>
<?else:?>
	<script type="text/javascript">
		showError("Вы не являетесь администратором системы");
	</script>
<?endif?>
