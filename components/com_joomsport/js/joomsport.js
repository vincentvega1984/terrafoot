function JS_addSelectedToList( frmName, srcListName, tgtListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );
	var tgtList = eval( 'form.' + tgtListName );

	var srcLen = srcList.length;
	var tgtLen = tgtList.length;
	var tgt = "x";

	//build array of target items
	for (var i=tgtLen-1; i > -1; i--) {
		tgt += "," + tgtList.options[i].value + ","
	}

	//Pull selected resources and add them to list
	//for (var i=srcLen-1; i > -1; i--) {
	for (var i=0; i < srcLen; i++) {
		if (srcList.options[i].selected && tgt.indexOf( "," + srcList.options[i].value + "," ) == -1) {
			opt = new Option( srcList.options[i].text, srcList.options[i].value );
			tgtList.options[tgtList.length] = opt;
		}
	}
}

function JS_delSelectedFromList( frmName, srcListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );

	var srcLen = srcList.length;

	for (var i=srcLen-1; i > -1; i--) {
		if (srcList.options[i].selected) {
			srcList.options[i] = null;
		}
	}
}
function getObj(name) {
  if (document.getElementById)  {  return document.getElementById(name);  }
  else if (document.all)  {  return document.all[name];  }
  else if (document.layers)  {  return document.layers[name];  }
}
onload=function(){
if (document.getElementsByClassName == undefined) {
	document.getElementsByClassName = function(className)
	{
		var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
		var allElements = document.getElementsByTagName("*");
		var results = [];

		var element;
		for (var i = 0; (element = allElements[i]) != null; i++) {
			var elementClass = element.className;
			if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
				results.push(element);
		}

		return results;
	}
}
}

function extractNumber(obj, decimalPlaces, allowNegative)
{
	var temp = obj.value;
	
	// avoid changing things if already formatted correctly
	var reg0Str = '[0-9]*';
	if (decimalPlaces > 0) {
		reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
	} else if (decimalPlaces < 0) {
		reg0Str += '\\.?[0-9]*';
	}
	reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
	reg0Str = reg0Str + '$';
	var reg0 = new RegExp(reg0Str);
	if (reg0.test(temp)) return true;

	// first replace all non numbers
	var reg1Str = '[^0-9' + (decimalPlaces != 0 ? '.' : '') + (allowNegative ? '-' : '') + ']';
	var reg1 = new RegExp(reg1Str, 'g');
	temp = temp.replace(reg1, '');

	if (allowNegative) {
		// replace extra negative
		var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
		var reg2 = /-/g;
		temp = temp.replace(reg2, '');
		if (hasNegative) temp = '-' + temp;
	}
	
	if (decimalPlaces != 0) {
		var reg3 = /\./g;
		var reg3Array = reg3.exec(temp);
		if (reg3Array != null) {
			// keep only first occurrence of .
			//  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
			var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
			reg3Right = reg3Right.replace(reg3, '');
			reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
			temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
		}
	}
	
	obj.value = temp;
}
function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}	


function Delete_tbl_row(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
}
function Delete_tbl_row_md(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index+1);
	//element.parentNode.parentNode.parentNode.deleteRow(del_index+1);
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
}
function chng_disbl_aet(){
		
	if(getObj('is_extra1').checked){
		
		getObj('aet1').disabled = '';
		getObj('aet2').disabled = '';
	}				else{

		getObj('aet1').disabled = 'true';
		getObj('aet2').disabled = 'true';
	}
}

function js_newsort(sort,th){
	 var field1 = document.getElementById("field");
    if(field1){
        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = "sortfield";
        input_hidden.value = sort;
        field1.appendChild(input_hidden);
    }

	document.adminForm.sortfield.value = sort;
	if(th.hasClass('desc')){ 
		document.adminForm.sortdest.value = 0;
	}else{
		document.adminForm.sortdest.value = 1;
	}
	document.adminForm.submit();
}

function subPcount(ulid){
	
	if(getObj(ulid).style.display=="block"){
		getObj(ulid).style.display = "none";
	}else{
		getObj(ulid).style.display = "block";
		if(ulid=='jsul_start'){
			getObj("jsul_last").style.display = "none";
		}else{
			getObj("jsul_start").style.display = "none";
		}
	}
}