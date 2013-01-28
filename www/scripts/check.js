var selC = 0,
	podBox = document.createElement('div'),
	categoryXML = null,
	showTolTipTimer = null;


function pKey(e)
{
	var ev = window.event || e, 
		keycode = ev.keyCode,
		cellule = p.lCell || p.table.rows[0].cells[0];
	clearTimeout(showTolTipTimer);
	switch(keycode){
	case 40:
		if((cellule.cellIndex == tCells.NAME || this.id == 'shop') && ge('pBox')){ //стрелка вниз
			if(ge('rc'+(selC+1))){
				if(ge('rc'+(selC)))
					ge('rc'+(selC)).style.backgroundColor = '#fff';
				selC++;
				ge('rc'+(selC)).style.backgroundColor = '#aaf';
				}
				return false;}
	case 38:		
		if((cellule.cellIndex == tCells.NAME || this.id == 'shop') && ge('pBox')) { //стрелка вверх
			if(ge('rc'+(selC-1))){
				if(ge('rc'+(selC)))
					ge('rc'+(selC)).style.backgroundColor = '#fff';
				selC--;
				ge('rc'+(selC)).style.backgroundColor = '#aaf';
				}
			return false;}
	case 9://таб
	case 13://или интер

	 	if(selC){ //Если выделен элемент в toolTip...
				ge('rc'+(selC)).click();} //Вставляем имя продукта
		else{
			hideTolTip();
			//Если идёт редактирование ячейки имени и у ячейки отсутствует идентификатор продукта...
			if(cellule.cellIndex == tCells.NAME && cellule.product == null && this.value.length > 0) 
				checkProduct(this.value); //тогда запускаем проверку
			}
		if (this.id != 'shop'){
			if (ev.shiftKey)
				return prewCellClick(cellule);
			else
				return nextCellClick(cellule);}
		break;
	case 27:
		hideTolTip();
		break;
	default: 	
		if (this.id == 'shop')
			this.shopId = null;
		if (cellule.cellIndex == tCells.NAME)
			cellule.product = null;
	}
}

function prewCellClick(cellule){
	if(cellule.previousSibling.cellIndex != 0){
		cellule.previousSibling.onclick();}
	else{
		if(cellule.parentNode.previousSibling != null)
			cellule.parentNode.previousSibling.cells[tCells.TOTAL].onclick();
		else
			return true;}
	return false;
}


function nextCellClick(cellule){
	if(cellule.nextSibling.className != "buttonCell"){
		cellule.nextSibling.onclick();}
	else{
		if(cellule.parentNode.nextSibling != null)
			cellule.parentNode.nextSibling.childNodes[1].onclick();
		else
			return true;}
	return false;
}

function uKey(e)
{
	var ev = window.event || e, 
		keycode = ev.keyCode,
		cellule = p.lCell;
	if(keycode != 38 && keycode != 40 && keycode != 9 && keycode != 13 && keycode != 27 && this.value.length > 0){
		if(cellule)
			if(cellule.cellIndex == tCells.NAME)
				showTolTipTimer = setTimeout(showTolTipName,400,this.value);
		if(this.id == 'shop')
			showTolTipTimer = setTimeout(showTolTipShop,400,this.value);
	}
	else
		if (this.value.length == 0)
			hideTolTip();
}

function showProductAdd(name){
	var mainContainer = document.createElement("div");
	mainContainer.textContent = 'Позиции с именем "'+name+'" не существует';
	mainContainer.innerHTML = '<br>';
	mainContainer.className = "productAddDialogContainer"
	var categoryConteiner = document.createElement("div");
	categoryConteiner.className = "categoryConteiner";
	categoryConteiner.onmousewheel = function(ev){
		ev.cancelBubble = true;
		ev = ev || window.event;
  		var delta = ev.deltaY || ev.detail || ev.wheelDelta;
		this.scrollTop -= delta/2;
		return false;
	}

	if(categoryXML != null)
		addElement(categoryXML.firstChild.firstChild, categoryConteiner,0,"");
	var productInput = document.createElement("input");
	productInput.value = name;
	productInput.id = "productInput";
	var categoryInput = document.createElement("input");
	categoryInput.id = "categoryInput";
	
	var label = document.createElement("label");
	label.textContent = "Имя товара:"
	mainContainer.appendChild(label);
	mainContainer.appendChild(productInput);
	label = document.createElement("label");
	label.textContent = "Имя категории:"
	mainContainer.appendChild(label);
	mainContainer.appendChild(categoryInput);
	mainContainer.appendChild(categoryConteiner);
	showConfirmDialog(mainContainer,addProduct,null,"Добавить","Отмена");
	ge("productInput").focus();
}
	
function addElement(XMLNode,elm,pos,name){
	if(XMLNode != null){
		var category = document.createElement("div");
		category.textContent = XMLNode.getAttribute("name");
		category.style.paddingLeft = pos+'px';
		category.className = "categoryItem";
		var newName = name;
		if(XMLNode.getAttribute("id") != 1){
			if(name != "")
				newName += ', ';
			newName += XMLNode.getAttribute("name");
			category.categoryName = newName;
			category.onclick = function() {
				ge("categoryInput").value = this.categoryName;
				ge("categoryInput").product = XMLNode.getAttribute("id");}}
		else
			category.className = "categoryHead";
		elm.appendChild(category);
		addElement(XMLNode.firstChild,elm, pos+10, newName);
		addElement(XMLNode.nextSibling,elm,pos,name);
	}
}

function addProduct(){
	var cellule = p.lCell;
	sendAddProduct(ge("productInput").value,ge("categoryInput").value);
	cellule.parentNode.cells[tCells.NAME].textContent = ge("productInput").value;
	setTimeout(checkProduct,1000,cellule.parentNode.cells[tCells.NAME].textContent);
	hideDialog();
	updateCategory();
	nextCellClick(cellule.parentNode.cells[tCells.NAME]);
}

function chekFindProduct(XML){
	if(XML.getElementsByTagName('errors').length == 0){
		var cellule = p.lCell,
			product = XML.getElementsByTagName('product');
		if(product.length > 0){
			cellule.parentNode.cells[tCells.NAME].product = product[0].getAttribute("id");
			}
		else{
			cellule.parentNode.cells[tCells.NAME].product = null;
			showProductAdd(cellule.parentNode.cells[tCells.NAME].textContent || p.iBox.value);
			return;}
		}
	else
		XMLError(XML);
nextCellClick(cellule.parentNode.cells[tCells.NAME]);
}

function updCost() { //Обновление стоимости
	f = 0;
	for(i = 1; i < p.table.rows.length; i++){
		if(chekValue(p.table.rows[i].cells[tCells.TOTAL].textContent))
			f+=chekValue(p.table.rows[i].cells[tCells.TOTAL].textContent);}

	if(ge('direction').selectedIndex == 0)
		ge('total').value = accounting.formatMoney(-f, "р", 2, " ", ",", "%v %s");
	else
		ge('total').value = accounting.formatMoney(f, "р", 2, " ", ",", "%v %s");
}

function hideTolTip() {
	podBox.innerHTML = "";
	if(ge("pBox"))
		prnt.removeChild(podBox);
	selC = 0;
}

function offsetPosition(element) {
    var offsetLeft = 0, offsetTop = 0;
    do {
        offsetLeft += element.offsetLeft;
        offsetTop  += element.offsetTop;
    } while (element = element.offsetParent);
    return [offsetLeft, offsetTop];
}


function pasteName(ev) 
{
	var cellule = p.lCell,
		product = this.childNodes[0];
	cellule.textContent = product.textContent;
	p.iBox.value = product.textContent;
	cellule.product = product.product;
	cellule.nextSibling.onclick();
	hideTolTip();
}

function pasteShop(ev) 
{
	var shop = this.childNodes[0];
	ge("shop").value = shop.textContent;
	ge("shop").shopId = shop.shopId;
	hideTolTip();
}

function genShops(XML){
	if(XML.getElementsByTagName('errors').length == 0){
		if(XML.getElementsByTagName('shop').length > 0){
			prnt.appendChild(podBox);
			podBox.style.display = 'block';
			podBox.style.top = (ge("shop").offsetTop+ge("shop").offsetParent.offsetTop+20)+'px';
			podBox.style.left = (ge("shop").offsetLeft+ge("shop").offsetParent.offsetLeft)+'px';
			var elements = XML.getElementsByTagName('shop'),
				table = document.createElement("table");
			podBox.innerHTML = "";
			table.className = "tolTip";
			for(i = 0; i < elements.length; i++)
				{
					var row = table.insertRow(i);
					row.id = "rc"+(i+1);
					row.onclick = pasteShop;
					var cell = row.insertCell(0);
					cell.className = "tTLeft"
					cell.innerText = elements[i].getAttribute("name")+", "+elements[i].getAttribute("adress");
					cell.shopId = elements[i].getAttribute("id");
				}
			podBox.appendChild(table);
		}
		else 
			hideTolTip();
	}
	else
		XMLError(XML);
}

function genNames(XML){
	if(XML.getElementsByTagName('errors').length == 0){
		if(XML.getElementsByTagName('position').length > 0){
			prnt.appendChild(podBox);
			podBox.style.display = 'block';
			podBox.style.top = (ge("iBox").offsetTop+ge("iBox").offsetParent.offsetTop+20)+'px';
			podBox.style.left = (ge("iBox").offsetLeft+ge("iBox").offsetParent.offsetLeft)+'px';
			var elements = XML.getElementsByTagName('position'),
				table = document.createElement("table");
			podBox.innerHTML = "";
			table.className = "tolTip";
			for(i = 0; i < elements.length; i++)
				{
					var row = table.insertRow(i);
					row.id = "rc"+(i+1);
					row.onclick = pasteName;
					var cell = row.insertCell(0);
					cell.className = "tTLeft"
					cell.innerText = elements[i].getAttribute("name");
					cell.product = elements[i].getAttribute("posId");
/*					cell = row.insertCell(1);
					cell.className = "tTRight"
					cell.innerText = elements[i].getAttribute("cost");*/
				}
			podBox.appendChild(table);
		}
		else 
			hideTolTip();
	}
	else
		XMLError(XML);
}

function changeCell(ev){
		if(this.textContent.length > 0){
			checkProduct(this.textContent);	}
}

function getAddPosition(XML){
	if(XML.getElementsByTagName('error').length == 0){
		mes = XML.getElementsByTagName('message');
		if(mes[0].getAttribute("text") == "success"){
			var goForm = document.createElement('form');
			goForm.action = "/check/modify/"+mes[1].getAttribute("text")+".html";
			goForm.name = "resend";
			goForm.id = "resend";
			goForm.method = "post";
			var messInput = document.createElement('input');
			messInput.type = "text";
			messInput.name = "mess";
			messInput.value = "Чек добавлен";
			var submInput = document.createElement('input');
			submInput.type = "submit";
			submInput.name = "submit";
			goForm.appendChild(messInput);
			goForm.appendChild(submInput);
			prnt.appendChild(goForm);
			submInput.click();
		}
		else
			showXMLMessages(XML);
	}
	else
		XMLError(XML);
}

function initAddCheck()
{
	if(prnt != null) {
		showPreload(1);
		GetScript("/scripts/formatNum.js");
		GetScript("/scripts/dTable.js");
		GetScript("/scripts/checkAjax.js");
		ge("direction").onchange = updCost;

		ge("shop").onkeyup = uKey;
		ge("shop").onkeydown = pKey;
		podBox.id = 'pBox';
		podBox.className = 'pBox';}
	else
		setTimeout("initAddCheck()",500);
}

function initTable(){
	showPreload(-1);
	p = new dTable(ge("divTable"),4);
	p.setHeaders("Название;Кол-во;Цена;Стоимость;");
	p.iBox.onkeyup = uKey;
	p.iBox.onkeydown = pKey;
	if(id != null)
		tableLoad();
	p.insrtRow();
}

function initAjax(){
	showPreload(-1);
	ge("add").onclick = sender;
	updateCategory();
}

initAddCheck();//End Script for some exploits
