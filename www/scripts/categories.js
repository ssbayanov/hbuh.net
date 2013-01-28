

function addElement(XMLNode,elm,pos,name, color){
	color = color || 200;
	if(XMLNode != null){
		var item = document.createElement("div"),
			category = document.createElement("div");
		category.className = "categoryItem";
		item.className = "item";
		
		item.appendChild(category);
		var newName = name;
		if(XMLNode.getAttribute("id") != 1){
			if(name != "")
				newName += ', ';
			newName += XMLNode.getAttribute("name");
			var addPad = 0;
			if(XMLNode.firstChild != null){
				var checkBx = document.createElement("span");
				checkBx.className = "chkBx";
				category.appendChild(checkBx);
			}
			else
				addPad = 17;
			category.style.paddingLeft = (addPad+pos)+'px';
			//category.categoryName = newName;
			category.style.backgroundColor = "RGB("+color+", "+color+", 255)";
			category.title = newName;
			category.className = "categoryClose";
			category.onclick = function(ev){
				ev.cancelBubble = true;
				if(this.className == "categoryClose")
					this.className = "categoryOpen";
				else
					this.className = "categoryClose";
			}
		}
		else
			category.className = "categoryHead";
			
		category.innerHTML += XMLNode.getAttribute("name");
		elm.appendChild(item);
		var colorChild;
		if(color >= 255)
			colorChild = 255;
		else
			colorChildr = color+12;
		addElement(XMLNode.firstChild,category, pos+10, newName, colorChildr);
		addElement(XMLNode.nextSibling,elm,pos,name, color);
	}
}

function initCategories(){
	showPreload(-1);
	var categoryConteiner = document.createElement("div");
	categoryConteiner.className = "categoryConteiner";
	categoryConteiner.id = "categoryConteiner";
	ge("categoriesBox").appendChild(categoryConteiner);
	showPreload(1);
	GetScript("/scripts/categoriesAjax.js");
	
}

function initCategoriesAjax(){
	updateCategories();
	showPreload(-1);
}

initCategories();

