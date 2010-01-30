<html>
<body>
<?php

/**
 *  The simplest way to list a query's result
 *  You can already
 *      move between pages,
 *      change the number of records displayed in a page
 *      and sort on every column
 */

// change these to match your mySQL host's settings
require_once('../Connections/connDB.php'); 

// require the class
require "/var/www/Common/Javascript/datagrid/class.datagrid.php";

// this is the query that will be displayed
// remember: no ORDER BY and no LIMIT!
$query = "
    SELECT
        *
    FROM
        Position
";

// instantiate the class and pass the query string to it
$grid = new dataGrid($query);

// show all the columns
$grid->showColumn("grade");
$grid->showColumn("title_name");
$grid->showColumn("min");
$grid->showColumn("max");

// sort the data by the "title" column
$grid->setDefaultSortColumn("(grade+0)");



// create a function that will set a javascript action for when clicking on the rows
// you could use this to redirect the page
function action($value_of_clicked_field, $array_with_the_values_of_all_fields_in_clicked_row)
{
    return "javascript:alert('the title on this row is \'".$array_with_the_values_of_all_fields_in_clicked_row["title"]."\' \\nCreated on ".date("M-d-Y H:i", $array_with_the_values_of_all_fields_in_clicked_row["created"])."')";
}

// bound the function to the rows
$grid->setRowActionFunction("action");

// we add a custom column
$grid->showCustomColumn("operations");

// we add content to it by using a callback function
// first create the function
function custom_content($value)
{
    return "[<a href=\"javascript:if(confirm('Are you sure?')){}else{}\">delete</a>]";
}

// bound this function to the "operations" column's fields
$grid->setCallbackFunction("operations", "custom_content");
// disable sorting on it
$grid->disableSorting("operations");
// unset for this column the action function set before
$grid->unsetActionFunction("operations");

// add a "selector" column
$grid->showSelectorColumn("&nbsp;", "check", "id");

// make the "title" column the widest
$grid->setTitleHTMLProperties("title", "style='width:100%'");
// make the "title" column's fields left aligned
$grid->setFieldHTMLProperties("title", "style='text-align:left'");

// make the "created", "modified" and "operations" column's fields 'nowrap'-ed and be centered
$grid->setFieldHTMLProperties("grade", "align='center' nowrap='nowrap'");
$grid->setFieldHTMLProperties("title_name", "align='left' nowrap='nowrap'");
$grid->setFieldHTMLProperties("min", "align='right' nowrap='nowrap'");
$grid->setFieldHTMLProperties("max", "align='right' nowrap='nowrap'");

// add a title to the table
$grid->setHeaderHTML("<h2>data grid class</h2>");

// and also add a footer
// you could use this button to dynamically change the form's action and submit the checked values...
$grid->setFooterHTML("<br /><input type='submit' value='Submit'>");

// witness magic!
$grid->render();

?>
</body>
</html>
