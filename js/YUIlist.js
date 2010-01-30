YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.XHR_XML = new function() {
		this.formatActions = function(elCell, oRecord, oColumn, sData) {
			if (searchRole != '0' && searchMy == 'true') {
				elCell.innerHTML = "<a href='detail.php?id=" + oRecord.getData("id") + "&approval=app" + searchRole + "' title='Detailed View' class='dark'><img src='/Common/images/detail.gif' align='absmiddle' border='0'></a> " + oRecord.getData("id");
			} else {
				elCell.innerHTML = "<a href='detail.php?id=" + oRecord.getData("id") + "' title='Detailed View' class='dark'><img src='/Common/images/detail.gif' align='absmiddle' border='0'></a> " + oRecord.getData("id");
			}
		};
		
		this.formatRequestLevel = function(elCell, oRecord, oColumn, sData) {
			if (searchStatus == 'N' && (searchRole == '0' || searchRole == 'none')) {
				var level = (oRecord.getData("level") == '') ? spacer : oRecord.getData("level");
				elCell.innerHTML = "<img src='/Common/images/" + oRecord.getData("level") + ".gif' title='Waiting for " + oRecord.getData("requester") + " (" + oRecord.getData("level") + ")' />";
			}
		};	


		var myColumnDefs = [
			{key:"id", label:"ID", sortable:true, formatter:this.formatActions},
			{key:"level", label:"", sortable:true, formatter:this.formatRequestLevel},
			{key:"title", label:"Position Title", sortable:true},
			{key:"location", label:"Location", sortable:true},
			{key:"requester", label:"Requester", sortable:true},
			{key:"request_date", label:"Date", sortable:true, formatter:YAHOO.widget.DataTable.formatDate},							
			{key:"request_type", label:"Type", sortable:true}
		];
		
		var searchURL = "access=" + searchRole + "&type=" + searchType + "&status=" + searchStatus + "&my=" + searchMy;
		console.log(searchURL);
		
		var myConfigs = {
			initialRequest:searchURL,
			sortedBy:{key:"id",dir:"desc"},
			rowSingleSelect:false,
			paginated:false,
			paginator:{
				containers:null,
				currentPage:1,
				dropdownOptions:[25,50,100],
				pageLinks:0,
				rowsPerPage:50
			}
		};
		
		this.myDataSource = new YAHOO.util.DataSource("../data/requests.php?");
		this.myDataSource.connMethodPost = false;
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.myDataSource.responseSchema = {
			resultNode: "request",
			fields: [{key:"id", parser:YAHOO.util.DataSource.parseNumber},
						  "level",
						  "title",
						  "location",
						  "requester",
					 {key:"request_date", parser:YAHOO.util.DataSource.parseDate},
						  "request_type"]
		};

		this.myDataTable = new YAHOO.widget.DataTable("dataTable", myColumnDefs, this.myDataSource, myConfigs);					
	};
});
 