function setDob() {
    var oSel1 = document.getElementById("day");
    var day = oSel1.options[oSel1.selectedIndex].value;

    var oSel2 = document.getElementById("month");
    var mon = oSel2.options[oSel2.selectedIndex].value;

    var oSel3 = document.getElementById("year");
    if (oSel3) {
        if (typeof oSel3.selectedIndex !== "undefined") {
            var yr = oSel3.options[oSel3.selectedIndex].value;
        } else {
            var yr = "2000";
        }
    } else {
        var yr = "2000";
    }

    var dob = document.getElementById("dob");
    dob.value = day + "/" + mon + "/" + yr;
    //alert(dob.value);
}

function populateDays(oSel, iDay) {
    //alert('populateDays: ' + iDay);

    var dob = document.getElementById("dob");
    dob.value = "";

    var month = oSel.options[oSel.selectedIndex].value;

    //alert(month);
    var dm = daysInMonth(month-1, 2000);
    removeAllOptions("day");
    for(var i=1; i<=dm; i++) {
        appendOptionLast(i, "day");
    }

    var oDay = document.getElementById("day");
    for(i=0;i<oDay.length;i++) {
        if (oDay.options[i].value == iDay) {
            oDay.selectedIndex=i;
        }
    }
}

function removeAllOptions(sel) {
    var elSel = document.getElementById(sel);
    if (elSel.length > 1) {
        while(elSel.length > 1) {
            elSel.remove(elSel.length - 1);
        }
    }
}

function appendOptionLast(val, sel) {
    var elOptNew = document.createElement('option');
    elOptNew.text = val;
    elOptNew.value = val;
    var elSel = document.getElementById(sel);
    try {
        elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
    }
    catch(ex) {
        elSel.add(elOptNew); // IE only
    }
}

function daysInMonth(iMonth, iYear) {
    return 32 - new Date(iYear, iMonth, 32).getDate();
}