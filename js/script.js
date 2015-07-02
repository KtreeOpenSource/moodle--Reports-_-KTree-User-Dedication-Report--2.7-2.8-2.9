var dtCh= "/";
var minYear=1900;
var maxYear=2500;
var dtchar="-";
function isInteger(s){
	var i;
for (i = 0; i < s.length; i++){var c = s.charAt(i);
if (((c < "0") || (c > "9"))) return false;
}
return true;
}
function stripCharsInBag(s, bag){
	var i;
var returnString = "";
for (i = 0; i < s.length; i++){
var c = s.charAt(i);
if (bag.indexOf(c) == -1) returnString += c;
}
return returnString;
}
function daysInFebruary (year){
return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31;
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30;}
		if (i==2) {this[i] = 29;}
}
return this
}
function isDate(dtStr){
var pos1, pos2, strMonth, strYear, strDay;
var daysInMonth = DaysArray(12);
if(dtStr.search("-")== -1)
{
	pos1=dtStr.indexOf(dtCh);
	pos2=dtStr.indexOf(dtCh,pos1+1);
	strMonth=dtStr.substring(0,pos1);
	strDay=dtStr.substring(pos1+1,pos2);
	strYear=dtStr.substring(pos2+1);
if(dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date");
		return false;
	}
}
else
{
	pos1=dtStr.indexOf(dtchar);
    pos2=dtStr.indexOf(dtchar,pos1+1);
	strYear=dtStr.substring(0,pos1);
	strMonth=dtStr.substring(pos1+1,pos2);
	strDay=dtStr.substring(pos2+1);
if(dtStr.indexOf(dtchar,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtchar))==false){
		alert("Please enter a valid date");
		return false;
	}
}
	strYr=strYear;
	if(strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
	if(strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1);
	for(var i = 1; i <= 3; i++) {
		if(strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if(pos1==-1 || pos2==-1){
		alert("Please enter the date and The date format should be : mm/dd/yyyy");
		return false;
	}
	if(strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month");
		return false;
	}
	if(strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day");
		return false;
	}
	if(strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear);
		return false;
	}
return true;
}
function ValidateForm(){
	var dt = document.frmSample.date1;
	if(isDate(dt.value)==false){
		dt.focus();
		return false;
	}
var dt=document.frmSample.date2
	if(isDate(dt.value)==false){
		dt.focus();
		return false;
	}
var date1 = document.frmSample.date1;
var date2= document.frmSample.date2;
if (Date.parse(date1.value) > Date.parse(date2.value)) {
alert("Invalid Date Range!\nStart Date cannot be after End Date!");
return false;
}return true;
}
	$(function() {
		$( "#datepicker" ).datepicker();
	});
	$(document).ready(function(){
		$("#datepicker1").datepicker({		
		onSelect: function(selected){
			$("#datepicker2").datepicker("option","minDate", selected);
			}
		});
		$("#datepicker2").datepicker({			
			onSelect: function(selected) {
			$("#datepicker1").datepicker("option","maxDate", selected);
			}
		});
	});
function valid(){		
		document.getElementById(datepicker2).display = true;
	}

