
function findPos(obj) {
	var curleft = curtop = 0;
	// browser supports offsetParent
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
	return [curleft,curtop];
}

function changeMainDivHeight(footerHeight, orgHeight) {
	var el = document.getElementById("main"); 
	var pos = findPos(el);
	var newHeight = document.documentElement.clientHeight - pos[1] - footerHeight;
	//window.status = pos[1] + ";" + newHeight + ">" + orgHeight;
	if (newHeight > orgHeight) {
		el.style.height = newHeight + "px";
		el.style.margin = "auto";
	}
}
