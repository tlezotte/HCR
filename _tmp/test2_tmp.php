<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>
<style type="text/css">
table.fruits {
	border-left: solid 1px #990033;
	border-top: solid 1px #990033;
	border-bottom: solid 1px #990033;
	font-family: Arial, Helvetica, sans-serif;
	border-collapse: collapse;
	width: 36em;
	margin-left: 1em;
}

table.fruits th {
	text-align: center;
	border-right: solid 1px #990033;
	border-bottom: solid 1px #990033;
	background: #990033;
	color: white;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 5px;
	padding-bottom: 5px;
}

table.fruits th a {
    color: white;
    text-decoration: none;
}

table.fruits th a:hover {
    color: #EEEEEE;
}
    
table.fruits td {
    text-align: right;
    border-right: solid 1px #990033;
    padding: 5px;
}

table.fruits tr.odd {
    background: #F4F4F4;
}

p.paging {
    text-align: center;
    font-weight: bold;
}

p.paging a {
    color: #990033;
}

</style>
<body>
<div align="center">
<?php
/* Includes */    
require_once "PEAR.php";
define("DB_DATAOBJECT_NO_OVERLOAD",true); /* This is needed for some buggy versions of PHP4 */
require_once "DB/DataObject.php";
require_once "Structures/DataGrid.php";    

/* Database and DataObject setup */
$dataobjectOptions = &PEAR::getStaticProperty("DB_DataObject","options");
$dataobjectOptions["database"] =  "mysql://hcr:human@a2.yourcompany.com/HCR";
$dataobjectOptions["proxy"] = "full";


/* Instantiate */
$dataobject = &DB_DataObject::factory('Position');
$datagrid =& new Structures_DataGrid(20); /* 20 rows per table */

/* Required in order to use the "fields" and "labels" options */
$datagridOptions["generate_columns"] = true; 

/* The fields we want to display */
$datagridOptions["fields"] = array ("title_id", "grade", "title_name", "min", "max");

/* Translate the fields names into user-friendly labels */
$datagridOptions["labels"] = array (
    "grade" 		=> "Grade", 
    "title_name" 	=> "Title Name", 
    "min" 			=> "Minuim&nbsp;(&#36;)",
	"max" 			=> "Maxuim&nbsp;(&#36;)"
);

// Specify how the DataGrid should be sorted by default
$datagrid->setDefaultSort(array('(grade+0)' => 'ASC'));

// We want to remove the ID field, so we retrieve a reference to the Column:
$column =& $datagrid->getColumnByField('title_id');

// And we drop that column:
$datagrid->removeColumn($column);

// Define columns
$datagrid->addColumn(new Structures_DataGrid_Column(null, null, null, array('width' => '10'), null, 'printEditLink()'));
//$datagrid->addColumn(new Structures_DataGrid_Column('Grade', 'grade', 'grade'));
//$datagrid->addColumn(new Structures_DataGrid_Column('Title Name', 'title_name', 'title_name'));

function printEditLink($params)
{
    extract($params);
    return "<a href=\"edit.php?title_id=" . $record['title_id'] . "\"><img src=\"/Common/images/change.gif\" border=\"0\"></a>";
}

/* Pass these options at binding time */
$datagrid->bind($dataobject, $datagridOptions);


$renderer =& $datagrid->getRenderer();

/* For the <table> element : */
$renderer->setTableAttribute("class", "fruits");

/* For every odd <tr> elements */
$renderer->setTableOddRowAttributes(array ("class" => "odd"));

$pagingHtml = $renderer->getPaging();

$datagrid->render();
echo "<p class=\"paging\">Pages : $pagingHtml</p>";
?>
</div>
</body>
</html>
