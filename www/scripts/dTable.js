//editable dynamic table 
function dTable(prnt,colls){

	var table = document.createElement('table'),
	delimiter = ';',
	it = this,
	inputBox = document.createElement('input'),
	numeric = true,
	column = colls || 2; //number of colls;
	
	this.iBox = inputBox;
	this.table = table;
	this.lCell = null;
	
	/** 
	 * TABLE FUNCTIONS SECTION
	 * This section to work with the table.
	 * Contains the following functions:
	 * setDelimiter(delim);
	 * deleteRow(event);
	 * insertRow(text);
	 * setHeaders(text);
	 */
	
	//Setup delimiter for haeders and cell content------------------------------------------------------------------------------------------------
	this.setDelimiter = function(delim){
	delimeter = delim;}
	
	
	//Delete row. Not delete head and last row. ------------------------------------------------------------------------------------------------
        this.delRow = function(event) {
            if(table.rows.length > 2) {
                table.deleteRow(this.parentNode.parentNode.rowIndex);
				if (numeric)
					it.renum();
            } else {
                return false;   
            }           
        };
	   
	//Clear all cells in this row ------------------------------------------------------------------------------------------------
        this.clrRow = function(event) {
            var row = this.parentNode.parentNode;
			for(i = 0; i < column; i++)
				row.cells[i+numeric].innerHTML = "";
        };


	//Insert row in table------------------------------------------------------------------------------------------------
	/* 
	 * insertRow(string text)
	 * Text is string devision delimeters var delimiter.
	 * Default delimeter is ";".
	 * Between delimiter and the text should be no spaces.
	 * If text groups more than columns, then text is clipping
	 */
	 
	this.insrtRow = function(text){
		text = text || "";
		table.insertRow(table.rows.length);
		for(i = 0; i <= column+numeric; i++){
			var cellule = table.rows[table.rows.length-1].insertCell(i);
			if(i == column+numeric){
				buttonDel = document.createElement("input");
				buttonDel.type = "image";
				buttonDel.onclick = it.delRow;
				buttonDel.className = "button delete";
				buttonDel.src = "/images/delete.png";
				buttonClr = document.createElement("input");
				buttonClr.type = "image";
				buttonClr.onclick = it.clrRow;
				buttonClr.className = "button clear";
				buttonClr.src = "/images/clear1.png";
				cellule.appendChild(buttonClr);
				cellule.appendChild(buttonDel);
				cellule.className = "buttonCell"}
			else{
				if(numeric && i == 0){
					cellule.textContent = table.rows.length-1+".";
					cellule.className = "numericCell";}
				else {
					cellule.textContent = text.split(delimiter)[0];
					cellule.onclick = it.cellClick;
					cellule.className = "infoCell"+i;
					text = text.substring(text.indexOf(delimiter)+1);
					if(i == 0+numeric)
						cellule.onchange = changeCell;}}}
		it.rows++;
	}
	
	//setup headers. Hadeers set bold------------------------------------------------------------------------------------------------
	this.setHeaders = function(text){
		table.insertRow(0);
		text = text || "";
		for(i = 0;i <= column+numeric; i++){
			var cellule = table.rows[0].appendChild(document.createElement("th"));
			if(i == column+numeric){
				table.rows[0].cells[i].innerHTML = "&nbsp";
				cellule.className = "buttonCellHead"}
			else {	
				if(numeric && i == 0)
					table.rows[0].cells[i].textContent = "№";
				else {
					table.rows[0].cells[i].textContent = text.split(delimiter)[0];
					text = text.substring(text.indexOf(delimiter)+1);}
				cellule.className = "headCell"+i;}}
	}
	
	//renum renumeric all rows
	this.renum = function(){
		for(i = 1; i < table.rows.length; i++)
			table.rows[i].cells[0].textContent = i+".";
	}
	
	//set widths
	this.setWidths = function(widths){
		widths = widths || "";
		for(i = 0;i <= column+numeric; i++){
			var cellule = table.rows[0].cell[i];
			cellule.style.width = widths.split(delimiter)[0];
			widths = widths.substring(widths.indexOf(delimiter)+1);}
	}
	
	/** 
	 * SECTION FUNCTIONS EDITING
	 * This section for edit content
	 * Content:
	 * cellClick(event)
	 * 
	 */
	//Cell click. This function show input under cell for edit cell content------------------------------------------------------------------------------------------------
	this.cellClick = function(event){
		if(inputBox.style.display != 'none')
			inputBox.onblur();
		inputBox.style.width = this.offsetWidth-5+'px';
		inputBox.style.height = this.offsetHeight-7+'px';
		inputBox.style.left = (this.offsetLeft+this.offsetParent.offsetLeft+1)+'px';
		inputBox.style.top = (this.offsetTop+this.offsetParent.offsetTop+1)+'px';
		
		
		
		if(this.textContent != "")
			switch(this.cellIndex){
				case tCells.NAME: inputBox.value = this.textContent; break;
				case tCells.QTY: 
				case tCells.COST: 
				case tCells.TOTAL: inputBox.value = chekValue(this.textContent); break;}
		else
			inputBox.value = "";
		
		//this.appendChild(inputBox);
		it.lCell = this;
		inputBox.style.display = 'block';
		inputBox.focus();
		inputBox.select();
	}

	//Lost focus inputBox------------------------------------------------------------------------------------------------
	this.lostFocus = function(event){
		var 
			//cellule = this.parentNode, //this current cell
			cellule = it.lCell;
			row = cellule.parentNode; //this current row
		
		/*if(cellule.children.length != 0)
			cellule.removeChild(inputBox);	*/
		
		if(inputBox.value != "")
		switch(cellule.cellIndex){
			case tCells.QTY: 
				if(is_int(chekValue(inputBox.value)))
					inputBox.value = accounting.formatNumber(chekValue(inputBox.value),0,' '); 
				else
					inputBox.value = accounting.formatNumber(chekValue(inputBox.value),3,' ',',');
				break;
			case tCells.COST:
			case tCells.TOTAL:inputBox.value = accounting.formatMoney(chekValue(inputBox.value), "р", 2, " ", ",", "%v %s"); break;}


		cellule.textContent = inputBox.value; //insert text in cell
		inputBox.style.display = 'none'; //hide inputBox
		
		if(inputBox.value != "" && table.rows.length <= row.rowIndex+1)
			it.insrtRow(); //if inputBox not empty and we editing last cell in last row, then insert row in end/
		
		if(inputBox.value != "")	
			recalc(row);
		inputBox.value = "";
		

		return false;}

	/*SECTION FOR HBUH FUNCTIONS*/
	function recalc(row){
		if((cellText(row,tCells.QTY) != "" && cellText(row,tCells.QTY) != "0") && (cellText(row,tCells.COST) != "" && cellText(row,tCells.COST) != "0,0 р")) {
			row.cells[tCells.TOTAL].innerHTML = accounting.formatMoney(chekValue(cellText(row,tCells.QTY))*chekValue(cellText(row,tCells.COST)), "р", 2, " ", ",", "%v %s");
			updCost();
			return;}
		if((cellText(row,tCells.QTY) != ""  && cellText(row,tCells.QTY) != "0")&& (cellText(row,tCells.TOTAL) != "" && cellText(row,tCells.TOTAL) != "0,0 р")) {
			row.cells[tCells.COST].innerHTML = accounting.formatMoney(chekValue(cellText(row,tCells.TOTAL))/chekValue(cellText(row,tCells.QTY)), "р", 2, " ", ",", "%v %s");
			updCost();
			return;}
		if((cellText(row,tCells.COST) !="" && cellText(row,tCells.COST) != "0,0 р") && (cellText(row,tCells.TOTAL) != "" && cellText(row,tCells.TOTAL) != "0,0 р")) {
			row.cells[tCells.QTY].innerHTML = chekValue(cellText(row,tCells.TOTAL))/chekValue(cellText(row,tCells.COST));
			updCost();
			return;}
	}
	
	function cellText(row, cell){
		return row.cells[cell].textContent;
	}
	
	//Initiate table ------------------------------------------------------------------------------------------------
	return (function() {
		//setup table
		table.className = "dTable";
		table.id = "dTable";
		
		//setup inputBox
		inputBox.onblur = it.lostFocus;
		inputBox.style.zIndex = 100;
		inputBox.style.display = 'none';
		inputBox.type = 'text';
		inputBox.id = 'iBox';
		inputBox.onclick = function(ev) {
			if (ev != null)
				ev.cancelBubble = true;
			else
				window.event.cancelBubble = true;} //Cancel parent call function
		
		prnt.appendChild(table);//insertTable
		prnt.appendChild(inputBox);		
	}());
}

initTable(); //run function after load
