<?php	$query = "SELECT p.*, c.name as nameCategory
		FROM `hbuh_products` p
		LEFT OUTER JOIN `hbuh_categories` c on p.category = c.id
		ORDER BY p.name";
//			WHERE `id_cheсk` = $check";
	$result = $DB->query($query, false);
	if($result)
	{
		?><table><?
		$count = 1;
		foreach ($result as $row):?>
				<tr>
					<td><?=$count?></td>
					<td><?=$row["name"]?></td>
					<td><?=$row["nameCategory"]?></td>
				</tr><?
				$count++;
			endforeach;
		?></table><?
	
	}
?>
