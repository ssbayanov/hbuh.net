<?php
switch($_GET["task"]){
	case "modify":?>
		<script type="text/javascript">
			function tableLoad(){
				<?$query = 'SELECT c.*, s.name as shopname, s.adress as shopadress
					FROM `hbuh_checks` c
					LEFT OUTER JOIN `hbuh_shops` s on c.shop = s.id
					WHERE c.id = '.$_GET["id"]." 
						AND c.id_user = ".$USER->getId()."
					LIMIT 1";
				$result = $DB->query($query);
				if(count($result)):?>
					ge("date").value = "<?=$result[0]["date"]?>";
					ge("comments").value = "<?=$result[0]["comment"]?>";
					ge("shop").value = "<?=$result[0]["shopname"].", ".$result[0]["shopadress"]?>";
					<?if($result[0]["cost"] > 0):?>
						ge("direction").selectedIndex = 1;
					<?endif;?>
					ge("success").selectedIndex = <?=$result[0]["success"]?>;
					<?
					$query = "SELECT n.*, p.name as name
						FROM `hbuh_purchases` n
						LEFT OUTER JOIN `hbuh_products` p on n.id_product = p.id
							WHERE `id_cheсk` = ".$_GET["id"];
					$result = $DB->query($query, false);
					if($result[0]["cost"] > 0):
						$i = 1;
						foreach($result as $row):?>
							p.insrtRow('<?=$row["name"]?>;<?=(fmod($row["quantum"],1) == 0?$row["quantum"]:number_format($row["quantum"],3,',',' '))?>;<?=number_format($row["cost"],2,',',' ')?> р;<?=number_format($row["quantum"]*$row["cost"],2,',',' ')?> р');
							p.table.rows[<?=$i?>].cells[tCells.NAME].product = <?=$row["id_product"]?>;
							<?$i++;
						endforeach;?>
						updCost();
					<?endif;
				endif;?>}
		</script><?
	case "add":?>
		<div class="ss-form-container">
			<div class="entryBox">
				<label class="inputLabel" for="date">Дата</label>
				<input name="date" id="date" type="date" tabIndex="1" class="entryInput" value="<?php echo date("Y-m-d");?>">
			</div>
			
			<div class="entryBox">
				<label class="inputLabel" for="direction">Направление</label>
				<select name="direction" id="direction" tabIndex="2" class="entryInput">
					<option selected="selected" value="Расход">Расход</option>
					<option value="Доход">Доход</option>
				</select>
			</div>
			
			<div class="entryBox">
				<div id="divTable"></div>
			</div>
			
			<div class="entryBox">
				<label class="inputLabel" for="total">Итог</label>
				<input name="total" id="total" type="text" tabIndex="5" class="entryInput">
			</div>

			<div class="entryBox">
				<label class="inputLabel" for="shop">Место покупки</label>
				<input name="shop" id="shop" type="text" tabIndex="6" class="entryInput">
			</div>

			<div class="entryBox">
				<label class="inputLabel" for="entry_6">Подтверждено?</label>
				<select name="success" id="success" tabIndex="7" class="entryInput">
					<option value="0">Нет</option>
					<option selected="selected" value="1">Да</option>
				</select>
			</div>

			<div class="entryBox">
				<label class="inputLabel" for="comments">Комментарий</label>
				<textarea name="comments" id="comments" tabIndex="8" class="entryInput"></textarea>
			</div>
			<div class="entryBox">
				<input name="button" id="add" value="<?=$_GET["task"] == 'modify'?"Сохранить":"Добавить"?>" type="button" tabIndex="9" class="entryInput">
			</div>

		</div>
		<div id='debug'></div>
		<script type="text/javascript">
		//<![CDATA[
			showPreload(1);
			GetScript("/scripts/check.js");
			task = '<?=$_GET["task"]?>';
			id = <?=empty($_GET["id"])?"null":$_GET["id"]?>;
		</script>
		<? break;
	case "view":
			if(!empty($_GET["id"])):
			$check = $_GET["id"];
			$query = "SELECT c.*, s.name as shopname, s.adress as shopadress
				FROM `hbuh_checks` c
				LEFT OUTER JOIN `hbuh_shops` s on c.shop = s.id
				WHERE c.id = {$check}
					AND c.id_user = ".$USER->getId()."
				LIMIT 1";
			
			$row = 0;
			$result = $DB->query($query);
			foreach($result as $row):?>
			<div class="check"><?=date("d.m.Y",strtotime($row["date"]))?><br>
				<?=$row["shopname"]?>, <?=$row["shopadress"]?><br><br>
			<?endforeach;
			$cost = $row["cost"];
			$query = "SELECT n.*, p.name as name
				FROM `hbuh_purchases` n
				LEFT OUTER JOIN `hbuh_products` p on n.id_product = p.id
					WHERE `id_cheсk` = $check";
			$result = $DB->query($query, false);
			if($result):?>
				<table>
				<?
				$count = 1;
				foreach ($result as $row):?>
					<tr>
					<td class="num"><?=$count?></td>
					<td><span class="dotted"><span class="name"><?=$row["name"]?></span></span></td>
					<td class="quantity"><?=number_format($row["cost"], 2,',',' ')?>р x <?$row["quantum"]?></td>
					<td class="total"><?=number_format($row["cost"]*$row["quantum"], 2,',',' ')?>р</td>
					</tr>
					<?$count++;
				endforeach;
				?></table>
			<?else:?>
				Ошибка поиска покупок по чеку <?=$check?><br><?=$result;
			endif?>
			<div class="total">ИТОГО: <?=number_format($cost, 2,',',' ')?>р</div></div>
		<?else:?>
			Не указан чек
		<?endif;
	}
?>

