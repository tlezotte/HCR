YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.XHR_XML = new function() {
		this.formatUrlMyRequests = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<a href='Requests/detail.php?id=" + oRecord.getData("id") + "' title='Waiting for " + oRecord.getData("requester") + " (" + oRecord.getData("level") + ")' class='dark' target='ChangeLog'>" + sData + "</a>";
		};

		 this.formatRequestLevel = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<img src='/Common/images/" + oRecord.getData("level") + ".gif' title='Waiting for " + oRecord.getData("requester") + " (" + oRecord.getData("level") + ")' />";
		};		
		   
		var colMyRequests = [
			{label:"My Open Requisitions", width:"300px", children:[
				{key:"id", label:"#", sortable:true, formatter:this.formatUrlMyRequests},
				{key:"level", label:"", sortable:true, formatter:this.formatRequestLevel},
				{key:"title", label:"Title", sortable:true},
				{key:"request_type", label:"Type", sortable:true}					
			]}
		];
		var cfgMyRequests = {
			initialRequest:"type=all&status=N&my=true",
			sortedBy:{key:"id",dir:"desc"}
		};                  
		
		this.dsMyRequests = new YAHOO.util.DataSource("data/requests.php?");
		this.dsMyRequests.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsMyRequests.responseSchema = {
			resultNode: "request",
			fields: [{key:"id", parser:YAHOO.util.DataSource.parseNumber},
					 "request_type",
					 "requester",
					 "level",						 
					 "title"]
		};

		this.MyRequests = new YAHOO.widget.DataTable("myRequestsTable", colMyRequests, this.dsMyRequests, cfgMyRequests);
		
		/* ----- Change Log from Intranet website ----- */
		this.formatPubDate = function(elCell, oRecord, oColumn, sData) {
			var pubDate = oRecord.getData("pubDate").split(" ");
			elCell.innerHTML = pubDate[2] + " " + pubDate[1] + ", " + pubDate[3];
		};
					
		this.formatUrlIntranet = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<a href='" + oRecord.getData("link") + "' title='" + oRecord.getData("pubDate") + "' onClick=\"displayChangeLog('on');\" class='dark' target='ChangeLog'>" + sData + "</a> <a href='" + oRecord.getData("link") + "' title='Open in new window or tab' target='log'><img src='/Common/images/offsite.gif' border='0'></a>";
		};

		var colIntranet = [
			{label:"Changes Log", width:"250px", children:[
				{key:"pubDate", label:"Date", sortable:true, formatter:this.formatPubDate},
				{key:"title", label:"Article", formatter:this.formatUrlIntranet}
			]}
		];
		var cfgIntranet = {
			//sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsIntranet = new YAHOO.util.DataSource("proxy/intranet.xml");
		this.dsIntranet.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsIntranet.responseSchema = {
			resultNode: "item",
			fields: ["title",
					 "link",
					 "pubDate"]
		};

		this.Intranet = new YAHOO.widget.DataTable("ChangeLogTable", colIntranet, this.dsIntranet, cfgIntranet);

		/* ----- SHRM News ----- */					
		this.formatUrlIntranet = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<a href='" + oRecord.getData("link") + "' onClick=\"displayChangeLog('on');\" class='dark' target='ChangeLog'>" + sData + "</a> <a href='" + oRecord.getData("link") + "' title='Open in new window or tab' target='log'><img src='/Common/images/offsite.gif' border='0'></a>";
		};

		var colSHRM = [
			{key:"title", label:"Society for Human Resource Management", width:"250px", formatter:this.formatUrlIntranet}
		];
		var cfgSHRM = {
			//sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsSHRM = new YAHOO.util.DataSource("proxy/shrm.xml");
		this.dsSHRM.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsSHRM.responseSchema = {
			resultNode: "item",
			fields: ["title",
					 "link"]
		};

		this.SHRM = new YAHOO.widget.DataTable("ShrmTable", colSHRM, this.dsSHRM, cfgSHRM);
	};
});