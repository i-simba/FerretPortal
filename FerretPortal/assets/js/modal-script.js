var select = document.getElementById('sleepBodySelect');
var edit = document.getElementById('sleepBodyEdit');
var del = document.getElementById('sleepBodyDel');
var foot = document.getElementById('sleepFoot');
var save = document.getElementById('sleepSave');
var back = document.getElementById('sleepBack');
var editName = document.getElementById('sleepEditName');
var wakeTime = document.getElementById('sleepEditWake');
var sleepTime = document.getElementById('sleepEditSleep');
var editWake = document.getElementById('sleepWake');
var editSleep = document.getElementById('sleepSleep');

var sleepWake_t = "";
var sleepSleep_t = "";

function alertClose( loc ) {
    var close = document.getElementById('alertCloseBtn');

    if ( loc === "h" )
        location.href = ""; // Link to LocalHost page = home
    if ( loc === "w" )
        location.href = ""; // Link to LocalHost page = weight
}

function rowClick( id, name, wake, sleep ) {
    var modal = document.getElementById('sleepEdit');
    var sleepID = document.getElementById('sleepDelID');

    sleepID.setAttribute('value', id);
    modal.style.display = 'block';
    
    editName.innerHTML = name.toUpperCase();
    wakeTime.innerHTML = formatTime(wake);
    sleepWake_t = wake;
    if ( sleep != 'empty' ) {
        sleepTime.innerHTML = formatTime(sleep);
        sleepSleep_t = sleep;
    }
}

function selectEdit() {
    foot.classList.add("justify-content-between");
    edit.style.display = 'block';
    select.style.cssText = 'display: none !important';
    save.setAttribute('name', 'sleepEditBtn');
    save.hidden = false;
    back.hidden = false;

    editWake.value = sleepWake_t;
    editSleep.value = sleepSleep_t;
}

function selectDel() {
    foot.classList.add("justify-content-between");
    del.style.display = 'block';
    select.style.cssText = 'display: none !important';
    save.setAttribute('name', 'sleepDeleteBtn');
    save.hidden = false;
    back.hidden = false;
}

function resetSleep() {
    edit.style.cssText = 'display: none !important';
    del.style.cssText = 'display: none !important';
    foot.classList.remove("justify-content-between");
    select.style.display = 'block';
    save.setAttribute('name', 'sleepSave');
    save.hidden = true;
    back.hidden = true;
}

function closeSleep() {
    var modal = document.getElementById('sleepEdit');
    
    resetSleep();
    wakeTime.innerHTML = '--:--';
    sleepTime.innerHTML = '--:--';
    sleepWake_t = "";
    sleepSleep_t = "";
    modal.style.display = 'none';
}

/* helper */
function formatTime(time) {
    const parts = time.split(':'); // Split the time string by ':'
    let hours = parseInt(parts[0], 10); // Get the hours part and convert to an integer
    const minutes = parts[1]; // Get the minutes part
    let ampm = 'AM';

    if (hours >= 12) {
        ampm = 'PM';
        if (hours > 12) {
            hours -= 12; // Convert to 12-hour format
        }
    } else if (hours === 0) {
        hours = 12; // Handle midnight case
    }

    return `${hours}:${minutes} ${ampm}`; // Format the time string
}