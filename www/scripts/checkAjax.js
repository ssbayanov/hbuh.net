function RefreshNames() 
{
    if( req.readyState == 4 ) {
		if(req.responseXML != null)
			genNames(req.responseXML); 
		else
			showError("Ошибка парсинга XML позиции<br>"+req.responseText);}
}

function RefreshCategory(req) 
{ 
    if( req.readyState == 4 ) {
		if(req.responseXML != null)
			categoryXML = req.responseXML; 
		else
			showError("Ошибка парсинга XML категории<br>"+req.responseText);}
}


function RefreshShop(req) 
{ 
    if( req.readyState == 4 ) {
		if(req.responseXML != null)
			genShops(req.responseXML); 
		else
			showError("Ошибка парсинга XML магазина<br>"+req.responseText);}
}

function RefreshProductId(req){
    if( req.readyState == 4 ) {
		if(req.responseXML != null)
			chekFindProduct(req.responseXML); 
		else
			showError("Ошибка парсинга XML id категории<br>"+req.responseText);}
}

function RefreshAddProduct(req){
    if( req.readyState == 4 ) {
		if(req.responseXML != null)
			showXMLMessages(req.responseXML);
		else
			showError("Ошибка парсинга XML добавления продукта<br>"+req.responseText)
		}
}

function showTolTipName(name) {
	var query = 'name='+name+
		'&task=product'; 
	Request(query, '/ajax/nameXML.php', RefreshNames); 
}

function showTolTipShop(name) {
	var query ='name='+name+
		'&task=shop'; 
	Request(query, '/ajax/nameXML.php', RefreshShop); 
}

function output(req)
{
    if( req.readyState == 4 ) {
		if(req.responseXML != null)
			getAddPosition(req.responseXML); 
		else
			showError("Ошибка парсинга XML в процессе добавления чека<br>"+req.responseText);
		setTimeout("updateStat()",1000);}
}

function sender()
{
	var query = "task="+task;
	if(ge("shop").shopId != null)
		query += '&shop='+ge("shop").shopId;
	else
		query += '&shop='+ge("shop").value;
		query += '&total='+chekValue(ge("total").value)+
		'&date='+ge("date").value+
		'&comment='+ge('comments').value+
		'&success='+ge("success").value;
	if(id != null)
		query += '&id='+id;
	
	var i = 1;
	while(p.table.rows[i].cells[tCells.NAME].product != null) {
			query +='&product'+i+'='+p.table.rows[i].cells[tCells.NAME].product
			+'&cost'+i+'='+chekValue(p.table.rows[i].cells[tCells.COST].innerText)
			+'&quantum'+i+'='+chekValue(p.table.rows[i].cells[tCells.QTY].innerText);
			i++;}
		Request(query,  '/ajax/addPosition.php', output);
}

function checkProduct(name){
	var query = 'name='+name+'&task=productId'; 
	Request(query, '/ajax/nameXML.php', RefreshProductId); 
}

function updateCategory(){
	var query = 'task=categories'; 
	Request(query, '/ajax/nameXML.php', RefreshCategory); 
}

function sendAddProduct(name,category){
	var query = 'task=addProduct'+
		'&name='+name+
		'&category='+category; 
	Request(query, '/ajax/addPosition.php', RefreshAddProduct); 
}
initAjax();
//End Script for some exploits//End Script for some exploits
