YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.XHR_XML = new function() {
		/* 
		 * ----- Get users current Requisitions ----- 
		 */				
		this.formatUrlMyRequests = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<a href='Requests/detail.php?id=" + oRecord.getData("id") + "' title='Waiting for " + oRecord.getData("requester") + " (" + oRecord.getData("level") + ")' class='dark' target='ChangeLog'>" + sData + "</a>";
		};

		 this.formatRequestLevel = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<img src='/Common/images/" + oRecord.getData("level") + ".gif' title='Waiting for " + oRecord.getData("requester") + " (" + oRecord.getData("level") + ")' />";
		};		
		   
		var colMyRequests = [
			{label:"My Open Requisitions", children:[
				{key:"id", label:"#", width:"30px", sortable:true, formatter:this.formatUrlMyRequests},
				{key:"level", label:"", width:"20px", sortable:true, formatter:this.formatRequestLevel},
				{key:"title", label:"Title", width:"190px", sortable:true},
				{key:"request_type", label:"Type", width:"60px", sortable:true}					
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


		/* 
		 * ----- Change Log from Intranet website ----- 
		 */
		this.formatPubDate = function(elCell, oRecord, oColumn, sData) {
			var pubDate = oRecord.getData("pubDate").split(" ");
			elCell.innerHTML = pubDate[2] + " " + pubDate[1] + ", " + pubDate[3];
		};
					
		this.formatUrlIntranet = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<a href='" + oRecord.getData("link") + "' title='" + oRecord.getData("pubDate") + "' onClick=\"displayLocalView('on');\" class='dark' target='localView'>" + sData + "</a> <a href='" + oRecord.getData("link") + "' title='Open in new window or tab' target='log'><img src='/Common/images/offsite.gif' border='0'></a>";
		};

		var colIntranet = [
			{label:"Changes Log", children:[
				{key:"pubDate", label:"Date", width:"90px", sortable:true, formatter:this.formatPubDate},
				{key:"title", label:"Article", width:"210px", formatter:this.formatUrlIntranet}
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


		/*
		 * ----- SHRM News ----- 
		 */					
		this.formatUrlIntranet = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<div style='white-space: normal;width:300px'><a href='" + oRecord.getData("link") + "' onClick=\"displayLocalView('on');\" class='dark' target='localView'>" + sData + "</a> <a href='" + oRecord.getData("link") + "' title='Open in new window or tab' target='log'><img src='/Common/images/offsite.gif' border='0'></a></div>";
		};

		var colSHRM = [
			{key:"title", label:"Society for Human Resource Management", width:"300px", formatter:this.formatUrlIntranet}
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

	
		/* 
		 * ----- Yahoo Weather ----- 
		 */
		var colWeather = [
			{key:"description", label:"What it's like outside", width:"300px"}
		];
		var cfgWeather = {
			//sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsWeather = new YAHOO.util.DataSource("proxy/weather.xml");
		this.dsWeather.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsWeather.responseSchema = {
			resultNode: "item",
			fields: ["description"]
		};

		this.Weather = new YAHOO.widget.DataTable("WeatherTable", colWeather, this.dsWeather, cfgWeather);
	};


	YAHOO.example.XHR_JSON = new function() {
		/* 
		 * ----- Stock Market ----- 
		 */		
		this.formatMarket = function(elCell, oRecord, oColumn, sData) {
			var market;
			
			switch (oRecord.getData("title")) {
				case 'DJI': market = 'Dow Jones'; break;
				case 'IXIC': market = 'NASDAQ'; break;
				case 'GSPC': market = 'S&P 500'; break;
			}
			
			elCell.innerHTML = "<a href='" + oRecord.getData("link") + "' onClick=\"displayLocalView('on');\" class='dark' target='localView'>" + market + "</a> <a href='" + oRecord.getData("link") + "' title='Open in new window or tab' target='currency'><img src='/Common/images/offsite.gif' border='0'></a>";
		};

		this.formatChange = function(elCell, oRecord, oColumn, sData) {
			var change = oRecord.getData("change");
			var negative = change.charAt(0);
			var image = (negative == '-') ? 'down_arrow' : 'up_arrow';
			var numbersOnly = change.substring(1,change.length);
			
			elCell.innerHTML = "<img src='/Common/images/" + image + ".gif' />" + numbersOnly;
		};
		
		var colMarket = [
			{label:"Stock Markets", width:"300px", children:[
				{key:"title", label:"Market", width:"150px", formatter:this.formatMarket},
				{key:"price", label:"Price", width:"75px"},
				{key:"change", label:"Change", width:"75px", formatter:this.formatChange}
			]}
		];
		var cfgMarket = {
			//sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsMarket = new YAHOO.util.DataSource("proxy/market.json");
		this.dsMarket.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this.dsMarket.responseSchema = {
			resultsList: "value.items",
			fields: ["title",
					 "price",
					 "change",
					 "link"]
		};

		this.Market = new YAHOO.widget.DataTable("marketTable", colMarket, this.dsMarket, cfgMarket);	
		
	};	
});