<div id="checkTableBox">
</div>
<script type="text/javascript">
//<![CDATA[
	<?php 
		$date = getInterval($_GET["range"]);
	?>
	var startDate = '<?=$date["startDate"]?>', endDate = '<?=$date["endDate"]?>';
	
	showPreload(1);
	GetScript("/scripts/viewChecks.js");
//]]>
</script>