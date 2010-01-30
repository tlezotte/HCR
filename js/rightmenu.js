/***********************************************
* AnyLink Drop Down Menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

//Contents for menu 1
var menu1=new Array()
menu1[0]='<a href="/go/HCR/Requests/index.php">New Open Position</a>'
menu1[1]='<a href="/go/HCR/Requests/list.php?action=my&access=0">My Open Position</a>'
if (hcr_groups == "hr" || hcr_groups == "ex") {
menu1[2]='<a href="/go/HCR/Requests/list.php">All Open Position</a>'
}

//Contents for menu 2
var menu2=new Array()
menu2[0]='<a href="/go/HCR/Requests/_index.php?action=adjustment">New Wage Adjustment</a>'
menu2[1]='<a href="/go/HCR/Requests/list_beta.php?type=adjustment&status=N&my=true">My Wage Adjustment</a>'
if (hcr_groups == "hr" || hcr_groups == "ex") {
menu2[2]='<a href="/go/HCR/Requests/list_beta.php?type=adjustment&status=N">All Wage Adjustment</a>'
}

//Contents for menu 3
var menu3=new Array()
menu3[0]='<a href="/go/HCR/Requests/_index.php?action=transfer">New Transfer</a>'
menu3[1]='<a href="/go/HCR/Requests/list_beta.php?type=transfer&status=N&my=true">My Transfer</a>'
if (hcr_groups == "hr" || hcr_groups == "ex") {
menu3[2]='<a href="/go/HCR/Requests/list_beta.php?type=transfer&status=N">All Transfer</a>'
}

//Contents for menu 4
var menu4=new Array()
menu4[0]='<a href="/go/HCR/Requests/_index.php?action=conversion">New Conversion</a>'
menu4[1]='<a href="/go/HCR/Requests/list_beta.php?type=conversion&status=N&my=true">My Conversion</a>'
if (hcr_groups == "hr" || hcr_groups == "ex") {
menu4[2]='<a href="/go/HCR/Requests/list_beta.php?type=conversion&status=N">All Conversion</a>'
}

//Contents for menu 5
var menu5=new Array()
menu5[0]='<a href="/go/HCR/Requests/_index.php?action=promotion">New Promotion</a>'
menu5[1]='<a href="/go/HCR/Requests/list_beta.php?type=promotion&status=N&my=true">My Promotion</a>'
if (hcr_groups == "hr" || hcr_groups == "ex") {
menu5[2]='<a href="/go/HCR/Requests/list_beta.php?type=promotion&status=N">All Promotion</a>'
}

//Contents for menu 6, and so on
var menu6=new Array()
menu6[0]='<a href="/go/HCR/Administration/index.php">Home</a>'
menu6[1]='<a href="/go/HCR/Administration/users.php">Users</a>'
menu6[2]='<a href="/go/HCR/Administration/settings.php">Settings</a>'
menu6[3]='<a href="/go/HCR/Administration/db/index.php">Databases</a>'
menu6[4]='<a href="/go/HCR/Administration/utilities.php">Utilities</a>'

//Contents for menu 7, and so on
var menu7=new Array()
menu7[0]='<a href="/go/HCR/Administration/user_information.php">Information</a>'
menu7[1]='<a href="/go/HCR/Administration/user_information.php#password">Change Password</a>'
		
var menuwidth='165px' //default menu width
var menubgcolor='#999966'  //menu bgcolor
var disappeardelay=250  //menu disappear speed onMouseout (in miliseconds)
var hidemenu_onclick="yes" //hide menu when user clicks within menu?

/////No further editting needed

var ie4=document.all
var ns6=document.getElementById&&!document.all

if (ie4||ns6)
document.write('<div id="dropmenudiv" style="visibility:hidden;width:'+menuwidth+';background-color:'+menubgcolor+'" onMouseover="clearhidemenu()" onMouseout="dynamichide(event)"></div>')

function getposOffset(what, offsettype){
var totaloffset=(offsettype=="left")? what.offsetLeft : what.offsetTop;
var parentEl=what.offsetParent;
while (parentEl!=null){
totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;
}
return totaloffset;
}


function showhide(obj, e, visible, hidden, menuwidth){
if (ie4||ns6)
dropmenuobj.style.left=dropmenuobj.style.top="-500px"
if (menuwidth!=""){
dropmenuobj.widthobj=dropmenuobj.style
dropmenuobj.widthobj.width=menuwidth
}
if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover")
obj.visibility=visible
else if (e.type=="click")
obj.visibility=hidden
}

function iecompattest(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge){
var edgeoffset=0
if (whichedge=="rightedge"){
var windowedge=ie4 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset+window.innerWidth-15
dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth
}
else{
var topedge=ie4 && !window.opera? iecompattest().scrollTop : window.pageYOffset
var windowedge=ie4 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18
dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure){ //move up?
edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight
if ((dropmenuobj.y-topedge)<dropmenuobj.contentmeasure) //up no good either?
edgeoffset=dropmenuobj.y+obj.offsetHeight-topedge
}
}
return edgeoffset
}

function populatemenu(what){
if (ie4||ns6)
dropmenuobj.innerHTML=what.join("")
}


function dropdownmenu(obj, e, menucontents, menuwidth){
if (window.event) event.cancelBubble=true
else if (e.stopPropagation) e.stopPropagation()
clearhidemenu()
dropmenuobj=document.getElementById? document.getElementById("dropmenudiv") : dropmenudiv
populatemenu(menucontents)

if (ie4||ns6){
showhide(dropmenuobj.style, e, "visible", "hidden", menuwidth)

dropmenuobj.x=getposOffset(obj, "left")
dropmenuobj.y=getposOffset(obj, "top")
dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px"
dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight+"px"
}

return clickreturnvalue()
}

function clickreturnvalue(){
if (ie4||ns6) return false
else return true
}

function contains_ns6(a, b) {
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function dynamichide(e){
if (ie4&&!dropmenuobj.contains(e.toElement))
delayhidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
delayhidemenu()
}

function hidemenu(e){
if (typeof dropmenuobj!="undefined"){
if (ie4||ns6)
dropmenuobj.style.visibility="hidden"
}
}

function delayhidemenu(){
if (ie4||ns6)
delayhide=setTimeout("hidemenu()",disappeardelay)
}

function clearhidemenu(){
if (typeof delayhide!="undefined")
clearTimeout(delayhide)
}

if (hidemenu_onclick=="yes")
document.onclick=hidemenu
