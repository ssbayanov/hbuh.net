<?php
include_once "config.php";

if(!empty($_GET["task"])){
	$check = $_GET["task"];
	$query = 'SELECT c.*, s.name as shopname, s.adress as shopadress
		FROM `hbuh_checks` c
		LEFT OUTER JOIN `hbuh_shops` s on c.shop = s.id
		WHERE c.id = '.$check;
	
	$row = 0;
	$result = $DB->query($query);
	foreach($result as $row){
	echo '<div class="check">'.date("d.m.Y",strtotime($row["date"]))."<br>".$row["shopname"].", ".$row["shopadress"]."<br><br>";
	break;}
	$cost = $row["cost"];

	$query = "SELECT n.*, p.name as name
		FROM `hbuh_purchases` n
		LEFT OUTER JOIN `hbuh_products` p on n.id_product = p.id
			WHERE `id_cheсk` = $check";
	$result = $DB->query($query, false);
	if($result)
	{
		echo '<table>';
		$count = 1;
		foreach ($result as $row){
			echo "<tr>";
			echo '<td class="num">'.$count.'</td>
			<td><span class="dotted"><span class="name">'.$row["name"].'</span></span></td>
			<td class="quantity">'.number_format($row["cost"], 2,',',' ').'р x'.$row["quantum"].'</td>
			<td class="total">'.number_format($row["cost"]*$row["quantum"], 2,',',' ')."р</td>";
			echo "</tr>";
			$count++;}
		echo '</table>';
	
	}
	else
		echo "Ошибка поиска покупок по чеку $check<br>$result";	
	
	echo '<div class="total">ИТОГО: '.number_format($cost, 2,',',' ').'р</div></div>';
}
else{
	echo "Не указан чек";}
	
?>
