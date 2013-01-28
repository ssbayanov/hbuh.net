var menu = document.createElement('table'),
	hideMenuTimer = null,
	isVisibleMunu = false,
	lastRow = null;


function clickChek(){
	location=this.getElementsByTagName("a")[0].getAttribute("href");
}

function showMenu(){
	window.event.cancelBubble = true;
	isVisibleMunu = true;
	clearTimeout(hideMenuTimer);
	pos = getOffsetRect(this);
	menu.style.top = (pos.top+16)+'px';
	menu.style.left = pos.left+'px';
	
	lastRow = this.parentNode.parentNode;
	

	if(lastRow.success == 0)
		insertItemMenu("Провести");
	else
		insertItemMenu("Отменить проведение");
		
	insertItemMenu("Изменить");	
	
	insertItemMenu("Удалить");
	
	menu.style.display = 'block';
	
}

function hideMenu(){
	menu.style.display = 'none';
	isVisibleMunu = false;
	while(menu.rows.length > 0)
		menu.deleteRow(0);
	if(lastRow != null)
		lastRow.onmouseout();
	}


function insertItemMenu(text){
	var r = menu.insertRow(menu.rows.length);
	r.onclick = rowClick;
	var c = r.insertCell(0);
	c.innerHTML = text;
	c.className = "itemMenu";
	return r;
}

function outRow() {
	if(!isVisibleMunu)
		this.className = '';
}

function overRow() {
	if(isVisibleMunu && this != lastRow)
		hideMenu();
	this.className = 'cheksButtonsHover';
}

function genChecksTable(XML){
	if(XML.getElementsByTagName('error').length == 0)
		if(XML.getElementsByTagName('check').length > 0){
			var checks = XML.getElementsByTagName('check'),
				table = document.createElement("table");
				
			ge("checkTableBox").innerHTML = "";
			table.className = "cheks";
			var row = table.insertRow(0);
			row.innerHTML = '<th class="cheksDirection"></th>'+
				'<th class="cheksDate">Дата</th>'+
				'<th class="cheksLocation">Место покупки</th>'+
				'<th class="cheksTotal">Сумма</th>'+
				'<th class="cheksButtons"></th>';
			for(var i = 0; i < checks.length; i++)
				{
					row = table.insertRow(i+1);
					row.onclick = clickChek;
					row.onmouseover = overRow;
					row.onmouseout = outRow;
					row.checkId = checks[i].getAttribute("checkId");
					row.success = checks[i].getAttribute("success");
					var cell = row.insertCell(0);
					cell.className = "cheksDirection";
					var image;
					if (chekValue(checks[i].getAttribute("cost")) < 0)
						image = "up";
					else
						image = "down";
					
					if (checks[i].getAttribute("success") == 0)
						image += "_failure";
					
					cell.innerHTML = '<a href="/check/view/'+
						checks[i].getAttribute("checkId")+'.html"><img src="/images/'
						+image+'.png" class="checkArrow"></a>';
					
					cell = row.insertCell(1);
					cell.className = "cheksDate";
					cell.innerHTML = checks[i].getAttribute("date");
					
					cell = row.insertCell(2);
					cell.className = "cheksLocation";
					cell.innerHTML = checks[i].getAttribute("shopName");
					cell.title = checks[i].getAttribute("shopAdress");
					
					cell = row.insertCell(3);
					cell.className = "cheksTotal";
					cell.innerHTML = checks[i].getAttribute("cost")+'р';
					
					cell = row.insertCell(4);
					cell.className = "cheksButtons";
					//cell.innerHTML = checks[i].getAttribute("cheksButtons");
					var arrowDiv = document.createElement("div");
					arrowDiv.className = "arrowConteiner hidden";
					arrowDiv.onclick = showMenu;
					arrowDiv.innerHTML = '<span class="arrow"></span>';
					cell.appendChild(arrowDiv);}
			ge("checkTableBox").appendChild(table);	}
	else
		XMLError(XML);
}

function rowClick(){
	switch(this.rowIndex){
		case 0: 
			if(lastRow.success == '1') 
				doAction(lastRow.checkId, "doUnsuccess"); 
			else 
				doAction(lastRow.checkId, "doSuccess"); break;
		case 1: location = "/check/modify/"+lastRow.checkId; break;
		case 2: showConfirmDelete(); break;}
}

function deleteLastCheck(){
	hideConfirmDialog();
	doAction(lastRow.checkId, "doDelete");
}

function showConfirmDelete(){
	var dialogConteiner = document.createElement("span");
	dialogConteiner.innerHTML = "Удалить чек на сумму "+lastRow.cells[3].innerHTML+" от "+lastRow.cells[1].innerHTML;
	showConfirmDialog(dialogConteiner,deleteLastCheck); 
}

function initViewChecks() {
	if(prnt != null) {
		menu.className = "menuTable";
		menu.id = "mTable";
		menu.onmouseover = function(e) {
			clearTimeout(hideMenuTimer);}
		menu.onmouseout = function(e) {
			hideMenuTimer = setTimeout("hideMenu()",1000);}
		
		prnt.appendChild(menu);
		prnt.onclick = function() {
			hideMenu();}
		GetScript("/scripts/viewChecksAjax.js");}
	else
		setTimeout("initViewChecks()",500);
}

initViewChecks();
