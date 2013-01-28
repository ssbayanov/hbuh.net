function RefreshCategories(req){
    if( req.readyState == 4 ) {
		if(req.responseXML != null)
			if(req.responseXML.getElementsByTagName('error').length == 0)
				addElement(req.responseXML.firstChild.firstChild, ge("categoryConteiner"),0,"");
			else
				XMLError(XML);
		else
			showError("Ошибка парсинга XML категории<br>"+req.responseText);}
}


function updateCategories(){
	var query = 'task=categories'; 
	Request(query, '/ajax/nameXML.php', RefreshCategories); 
}

initCategoriesAjax();
