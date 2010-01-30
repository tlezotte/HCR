<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>

<style type="text/css">
table.fruits {
    border-left: solid 1px #990033;
    border-top: solid 1px #990033;
    border-bottom: solid 1px #990033;
    font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; 
    border-collapse: collapse;     
    width: 36em;
    margin-left: 1em;
}

table.fruits th {
    text-align: center;
    border-right: solid 1px #990033;
    border-bottom: solid 1px #990033;
    background: #990033;
    padding: 2px;
    color: white;
    padding-left: 1em;
    padding-right: 1em;
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
    padding: 2px;
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
</head>

<body>
<div align="center">
<?php
echo str_replace(".php", "_controller.php", $_SERVER['PHP_SELF']);
/* Includes */    
require_once "PEAR.php";
define("DB_DATAOBJECT_NO_OVERLOAD",true); /* This is needed for some buggy versions of PHP4 */
require_once "DB/DataObject.php";
require_once "Structures/DataGrid.php";    

/* Database and DataObject setup */
$dataobjectOptions = &PEAR::getStaticProperty("DB_DataObject","options");
$dataobjectOptions["database"] =  "mysql://hcr:human@a2.yourcompany.com/HCR";
$dataobjectOptions["proxy"] = "full";

class DataObject_Fruits extends DB_DataObject 
{
    var $__table = "Position";
    var $title_id;
    var $grade;
    var $title_name;
    var $min;
	var $max;
}

/* Instantiate */
$dataobject = new DataObject_Fruits();
$datagrid =& new Structures_DataGrid(20); /* 10 rows per table */

$datagrid->addColumn(new Structures_DataGrid_Column(null, null, null, array('width' => '10'), null, 'printEditLink()'));

/* Required in order to use the "fields" and "labels" options */
$datagridOptions["generate_columns"] = true; 

/* The fields we want to display */
$datagridOptions["fields"] = array ("title_id", "grade", "title_name", "min", "max");

/* Translate the fields names into user-friendly labels */
$datagridOptions["labels"] = array (
    "grade" 		=> "Grade", 
    "title_name" 	=> "Title Name", 
	"min" 			=> "Minium&nbsp;(&#36;)",
    "max" 			=> "Maxium&nbsp;(&#36;)"
);

$datagrid->bind($dataobject, $datagridOptions);

/* Get a reference to the Renderer object */    
$renderer =& $datagrid->getRenderer();

/* For the <table> element : */
$renderer->setTableAttribute("class", "fruits");

/* For every odd <tr> elements */
$renderer->setTableOddRowAttributes(array ("class" => "odd"));

/* Render the table */
$datagrid->render();

/* Get and output HTML links */
$pagingHtml = $renderer->getPaging();
echo "<p>Pages : $pagingHtml</p>";

function printEditLink($params)
{
    extract($params);
    return "<a href=\"edit.php?title_id=" . $record['title_id'] . "\"><img src=\"/Common/images/change.gif\" border=\"0\"></a>";
}

?>
</div>
</body>
</html>
