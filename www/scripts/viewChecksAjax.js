function getResult(req){
	if(req.readyState == 4)
		if(req.responseXML != null){
			getChecks();
			showXMLMessages(req.responseXML)}
		else
			showError("Ошибка парсинга XML результата<br>"+req.responseText);
}

function getChecks(){
	var query = 'startDate='+startDate+'&endDate='+endDate; 
	Request(query, '/ajax/getChecks.php', RefreshChecks); 
}

function doAction(id, task){
	var query = 'task='+task+'&id='+id; 
	Request(query, '/ajax/editChecks.php', getResult); 
}

function RefreshChecks(req){
	if(req.readyState == 4)
		if(req.responseXML != null){
			genChecksTable(req.responseXML);
			updateStat();}
		else
			showError("Ошибка парсинга XML таблицы чеков<br>"+req.responseText);
}

getChecks();
showPreload(-1);