errorBox = document.createElement('div'),
dialog = document.createElement("div"),
blockBox = document.createElement("div"),
hideErrorTimer = null;
prnt = null;
var requests = 0;

XMLErrors = {
	ERROR_NO_CONNECTED: 0,
	ERROR_MYSQL: 1,
	ERROR_POST_DATA: 2,
	ERROR_TASK: 3,
	ERROR_UNCOMPLETE: 4,
	ERROR_TEXT: 5,
	ERROR_NOLOGIN: 6
}

XMLErrorText = [
	"Ошибка подключения к БД",
	"Ошибка выполнения MySQL запроса",
	"Ошибка POST данных",
	"Ошибка. Неизвестная задача",
	"Ошибка. Задача не выполнена",
	"Ошибка.",
	"Ошибка авторизации"
	]

tCells = {
	NUMBER: 0,
	NAME: 1,
	QTY: 2,
	COST: 3,
	TOTAL: 4
}

//Elements functions
function ge(id) 
{ 
	return document.getElementById(id); 
} 

//positional functions
function getOffsetRect(elem) {
    // (1)
    var box = elem.getBoundingClientRect()
    
    // (2)
    var body = document.body
    var docElem = document.documentElement
    
    // (3)
    var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
    var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft
    
    // (4)
    var clientTop = docElem.clientTop || body.clientTop || 0
    var clientLeft = docElem.clientLeft || body.clientLeft || 0
    
    // (5)
    var top  = box.top +  scrollTop - clientTop
    var left = box.left + scrollLeft - clientLeft
    
    return { top: Math.round(top), left: Math.round(left) }
}


//Messages functions


function showXMLMessages(XML){
	if(XML.getElementsByTagName('error').length == 0){
		var message = XML.getElementsByTagName('message');
		for(var i = 0; i < message.length; i++){
			showMessage(message[i].getAttribute("text"));}}
	else
		XMLError(XML);
}

function XMLError(XML){
	var errors = XML.getElementsByTagName('error');
	for(var i = 0; i < errors.length; i++){
		showError("Строка: "+errors[i].getAttribute("errorLine")+" "+
			XMLErrorText[errors[i].getAttribute("errorType")]+"<br>"+
			errors[i].getAttribute("errorText"));}
}

function showError(text){
	errorBox.textContent += text;
	errorBox.innerHTML += "<br>";
	errorBox.style.display = "block";
	clearTimeout(hideErrorTimer);
	hideErrorTimer = setTimeout("hideError()",7000);
}

function showMessage(text){
	errorBox.innerHTML += text+"<br><br>";
	errorBox.className = "messageBox";
	errorBox.style.display = "block";
	clearTimeout(hideErrorTimer);
	hideErrorTimer = setTimeout("hideError()",5000);
}

function hideError() {
	errorBox.innerText = "";
	errorBox.style.display = "none";
}

//Values functiona
function chekValue(value){
	return parseFloat(value.replace(',','.').replace(/ /g,'').replace('р',''));
}

function is_int(input){
	return typeof(input)=='number'&&parseInt(input)==input;
}
 
function GetScript(url) {
   var e = document.createElement("script");
   e.src = url;
   e.type="text/javascript";
   document.getElementsByTagName("head")[0].appendChild(e); 
}

//Ajax functions

function showPreload(par){
		requests+=par;
		ge("preloader").innerHTML = requests;
		if(requests)
			ge("preloader").style.visibility = "visible";
		else
			ge("preloader").style.visibility = "hidden";
}

function Create(){  
	if(navigator.appName == "Microsoft Internet Explorer"){  
		req = new ActiveXObject("Microsoft.XMLHTTP");}
	else{  
		req = new XMLHttpRequest();}  
	return req;  
} 

function onStateChange(event){
	var req = event.currentTarget
	req.onRefresh(req);
	if(req.readyState == 4){
		delete req;
		showPreload(-1);
		}
}

function Request(query, URI, Refresh) { 
	var req = Create();
	req.open('post', URI , true );
	req.onRefresh = Refresh;
	req.onreadystatechange = onStateChange;
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8"); 
	showPreload(1);
	req.send(query);  
}  

function updateStat(){
	Request('', '/ajax/stats.php', RefreshStats);
}

function RefreshStats() { 
    if(req.readyState == 4 ) {
    	ge("stats").innerHTML = req.responseText; }
} 

function hideConfirmDialog(){
	hideDialog();
}



function showConfirmDialog(child, okFunction, cancelFunction, okButtonText, cancelButtonText){
	cancelFunction = cancelFunction || hideConfirmDialog;
	okButtonText = okButtonText || "Да";
	cancelButtonText = cancelButtonText || "Нет";
	
	var dialogConteiner = document.createElement("div");
	var buttonConteiner = document.createElement("div");
	buttonConteiner.className = "buttonConteiner";
	
	var okButton = document.createElement("div");
	okButton.innerHTML = okButtonText;
	okButton.onclick = okFunction;
	okButton.className = "okButton";
	buttonConteiner.appendChild(okButton);
	
	var cancelButton = document.createElement("div");
	cancelButton.innerHTML = cancelButtonText;
	cancelButton.onclick = cancelFunction;
	cancelButton.className = "cancelButton";
	buttonConteiner.appendChild(cancelButton);
	dialogConteiner.appendChild(child);
	dialogConteiner.appendChild(buttonConteiner);
	showDialog(dialogConteiner);
}

function showDialog(elm){
	showBlockBox();
	dialog.appendChild(elm);
	prnt.appendChild(dialog);
}

function hideDialog(){
	showBlockBox();
	dialog.removeChild(dialog.firstChild);
	prnt.removeChild(dialog);
	hideBlockBox();
}

function showBlockBox(){
	blockBox.style.display = "block";
}

function hideBlockBox(){
	blockBox.style.display = "none";
}

function initIndex(){
	prnt = document.getElementsByTagName('BODY')[0];
	blockBox.className = "blockBox";
	errorBox.className = "errorBox";
	dialog.className = "dialog";
	prnt.appendChild(blockBox);
	prnt.appendChild(errorBox);
	updateStat();
}
