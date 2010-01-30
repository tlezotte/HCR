var pageOffset = 0;
var pageSize = 50;
var pageStop = pageOffset + pageSize;

var dsEmployees = new Spry.Data.XMLDataSet("employees_xml.php?" + searchQuery + "", "employees/employee",{sortOnLoad:"lst",sortOrderOnLoad:"ascending"});
//var dsEmployees = new Spry.Data.XMLDataSet("employees_xml.php?" + searchQuery + "", "employees/employee",{filterFunc: MyPagingFunc,sortOnLoad:"lst",sortOrderOnLoad:"ascending"});
//var dsEmployees2 = new Spry.Data.XMLDataSet("employees_xml.php?" + searchQuery + "", "employees/employee[position() >=" + (pageOffset+1) + " and position() < " + (pageStop+1) + "]");
dsEmployees.setColumnType("hire", "date");

var dsCell = new Spry.Data.XMLDataSet("employees_xml.php?eid={dsEmployees::@id}", "employees/employee/cell");

var filterEID = function(dataSet, row, rowNumber){
	var daText=document.getElementById('eidFilter').value;
	var daPattern=new RegExp(daText,"i");
	if (row["eid"].search(daPattern) != -1){
		return row;
	}else{	                    
		return null;
	}                       
}
var filterFST = function(dataSet, row, rowNumber){
	var daText=document.getElementById('fstFilter').value;
	var daPattern=new RegExp(daText,"i");
	if (row["fst"].search(daPattern) != -1){
		return row;
	}else{	                    
		return null;
	}                       
}
var filterLST = function(dataSet, row, rowNumber){
	var daText=document.getElementById('lstFilter').value;
	var daPattern=new RegExp(daText,"i");
	if (row["lst"].search(daPattern) != -1){
		return row;
	}else{	                    
		return null;
	}                       
}
var filterDEPT = function(dataSet, row, rowNumber){
	var daText=document.getElementById('deptFilter').value;
	var daPattern=new RegExp(daText,"i");
	if (row["dept"].search(daPattern) != -1){
		return row;
	}else{	                    
		return null;
	}                       
}
var filterLOCATION = function(dataSet, row, rowNumber){
	var daText=document.getElementById('locationFilter').value;
	var daPattern=new RegExp(daText,"i");
	if (row["location"].search(daPattern) != -1){
		return row;
	}else{	                    
		return null;
	}                       
}			
function mostrar(from,daTarget){
	elemento=document.getElementById(daTarget);
	elemento.style.visibility='visible';
	if(!document.all){
		elemento.style.opacity=0.0;
		var ensenar=new Spry.Effects.Opacity(elemento,1.0, { duration: 400, steps: 10 });
		resaltarSeleccion(from);
	}
}
function resaltarSeleccion(daSelection){
	for (i=0;i<document.links.length;i++){
		if(document.links[i].id!=daSelection){
			var sombrear=new Spry.Effects.Opacity(document.links[i],0.5, { duration: 400, steps: 10});
		}else{
			document.links[i].opacity=1;
		}
	}
	var resaltar=new Spry.Effects.Opacity(daSelection,1, { duration: 400, steps: 10});
}

/* ----- Start Paging ----- */
function MyPagingFunc(ds, row, rowNumber)
{
	if (rowNumber < pageOffset || rowNumber >= pageStop)
		return null;
	return row;
}

function UpdatePage(offset)
{
	var numRows = dsEmployees.getUnfilteredData().length;
	
	if (offset > (numRows - pageSize))
		offset = numRows - pageSize;
	if (offset < 0)
		offset = 0;

	pageOffset = offset;
	pageStop = offset + pageSize;

	// Re-apply our non-destructive filter on dsStates1:
	dsEmployees.filter(MyPagingFunc);

	// Tell our 2nd region to update because we've adjusted
	// some of the variables it uses in its spry:if expressions.
	Spry.Data.updateRegion("content");

	// Change the XPath for the 3rd data set so that the 3rd
	// region updates. Remember, position() starts at one and not
	// zero, so we have to add one to our pageOffset and pageStop.
	dsEmployees2.setXPath("employees/employee[position() >=" + (pageOffset+1) + " and position() < " + (pageStop+1) + "]");
}
/* ----- End Paging ----- */

/* ----- Add pre-text to search bar ----- */
function clear_textbox()
{
	if (document.searchBarForm.eidFilter.value == "EID")
		document.searchBarForm.eidFilter.value = "";		
	if (document.searchBarForm.fstFilter.value == "First Name")
		document.searchBarForm.fstFilter.value = "";
	if (document.searchBarForm.lstFilter.value == "Last Name")
		document.searchBarForm.lstFilter.value = "";
	if (document.searchBarForm.deptFilter.value == "Department")
		document.searchBarForm.deptFilter.value = "";
	if (document.searchBarForm.locationFilter.value == "Location")
		document.searchBarForm.locationFilter.value = "";												
} 
