// When the page is loaded
jQuery(document).ready(function(){
    // We add a new handler to the submit event of the referafriend form
    jQuery("#contestForm").submit(function(){
		// Get the contest ID
		var contestId = jQuery("#contest_id").val();
        // In order to set a cookie to true
		var expDate = new Date();
		expDate.setDate(expDate.getDate() + 10000);
        Mage.Cookies.set("hasSubscribed-"+contestId, true, expDate);
    });
});

/*
 set/unset the check value for all check boxes
*/
function setAllCheckBoxes(formName, fieldName, checkValue) {
    if(!document.forms[formName])
        return;
    var objCheckBoxes = document.forms[formName].elements[fieldName];
    if(!objCheckBoxes)
        return;
    var countCheckBoxes = objCheckBoxes.length;
    if(!countCheckBoxes) {
        objCheckBoxes.checked = CheckValue;
    }
    else {
        for(var i = 0; i < countCheckBoxes; i++) {
            objCheckBoxes[i].checked = checkValue;
        }
    }
}

/*
Dynamically Add/Remove rows in HTML table using JavaScript
http://viralpatel.net/blogs/2009/03/dynamically-add-remove-rows-in-html-table-using-javascript.html
*/
function addRow(tableID) {
    //alert("addRow:" + tableID);
    var table = document.getElementById(tableID);

    var rowCount = table.rows.length;
    if (rowCount >= 10) {
        alert("You have reached the maximum amount of friends!");
        return;
    }

    var row = table.insertRow(rowCount);
    rowCount++;

    var cell2 = row.insertCell(0);
	var label = document.createElement("label");
	label.setAttribute("for","friend" + rowCount);
	label.innerHTML = rowCount + daySuffix(rowCount) + " Friends Email";
	cell2.appendChild(label);
    var element2 = document.createElement("input");
    element2.name = "friend" + rowCount;
    element2.type = "text";
    element2.className = "input-text validate-email";
    cell2.appendChild(element2);
}

function delRow(tableID) {
    try {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        if (rowCount == 0) {
            alert("You have no more friends to remove!");
        }
        else {
            table.deleteRow(rowCount - 1);
        }
        /*
        for(var i=0; i<rowCount; i++) {
            var row = table.rows[i];
            var chkbox = row.cells[0].childNodes[0];
            if(null != chkbox && true == chkbox.checked) {
                table.deleteRow(i);
                rowCount--;
                i--;
            }
        }
        */
    }
    catch(e) {
        alert("You have no more friends to remove!");
    }
}

function daySuffix(d) {
    d = String(d);
    result = d.substr(-(Math.min(d.length, 2))) > 3 && d.substr(-(Math.min(d.length, 2))) < 21 ? "th" : ["th", "st", "nd", "rd", "th"][Math.min(Number(d)%10, 4)];
    return result;
}

function popupTerms(storeCode, contestId)
{
    var w = 700, h = 700;
    if (document.all || document.layers)
    {
       w = screen.availWidth;
       h = screen.availHeight;
    }
    var popW = 600, popH = 650;
    var leftPos = (w-popW)/2, topPos = (h-popH)/2;
    if (storeCode == "")
    {
        var urlToOpen = "/contests/index/terms/id/"+contestId;
    }
    else
    {
        var urlToOpen = "/"+storeCode+"/contests/index/terms/id/"+contestId;
    }
	win = new Window({ 
		id: "winTerms", 
		className: "default", 
		title: "Terms and Conditions", 
		url:urlToOpen, 
		zIndex:3000, 
		destroyOnClose: true, 
		recenterAuto:false, 
		resizable: true, 
		width:popW, 
		height:popH, 
		minimizable: false, 
		maximizable: false, 
		draggable: false
	});
	win.showCenter(true);
}

function clearForm(oForm) {
    var frm_elements = oForm.elements;
    for (i = 0; i < frm_elements.length; i++) {
        field_type = frm_elements[i].type.toLowerCase();
        switch (field_type)   {
            case "text":
            case "password":
            case "textarea":
            case "hidden":
                frm_elements[i].value = "";
                break;
            /*
            case "radio":
            case "checkbox":
                if (frm_elements[i].checked) {
                    frm_elements[i].checked = false;
                }
                break;
            */
            case "select-one":
            case "select-multi":
                frm_elements[i].selectedIndex = 0;
                break;
            default:
                break;
        }
    }
}