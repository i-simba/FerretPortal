var tempD = new Date().toLocaleDateString();
var tempD2 = new Date( tempD );
var tempD3 = tempD2.toISOString();
var date = tempD3.split('T')[0];
var tDate = date.split('-')[1] + " / " + date.split('-')[2];

var tempT = new Date().toTimeString().split(" ")[0];
var time = tempT.split(":")[0] + ":" + tempT.split(":")[1];

if (document.getElementById('pDate'))
    document.getElementById('pDate').value = date;
if (document.getElementById('sDate'))
    document.getElementById('sDate').value = date;
if (document.getElementById('wDate'))
    document.getElementById('wDate').value = date;

if (document.getElementById('pTime'))
    document.getElementById('pTime').value = time;
if (document.getElementById('sTime'))
    document.getElementById('sTime').value = time;

if (document.getElementById('ptDate'))
    document.getElementById('ptDate').innerHTML = tDate;