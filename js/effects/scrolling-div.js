/* This script and many more are available free online at
The JavaScript Source!! http://javascript.internet.com
Created by: Mr J | http://www.huntingground.net/ */

scrollStep=2

timerLeft=""
timerRight=""

function toLeft(id){
  document.getElementById(id).scrollLeft=0
}

function scrollDivLeft(id){
  clearTimeout(timerRight) 
  document.getElementById(id).scrollLeft+=scrollStep
  timerRight=setTimeout("scrollDivLeft('"+id+"')",10)
}

function scrollDivRight(id){
  clearTimeout(timerLeft)
  document.getElementById(id).scrollLeft-=scrollStep
  timerLeft=setTimeout("scrollDivRight('"+id+"')",10)
}

function toRight(id){
  document.getElementById(id).scrollLeft=document.getElementById(id).scrollWidth
}

function jumpRight(id, pixels, scalePad) {
	//alert("jumpRight: " + pixels);
	document.getElementById(id).scrollLeft+=pixels;
	moveScale(1, id, scalePad);
}

function jumpLeft(id, pixels) {
	//alert("jumpLeft: " + pixels);
	document.getElementById(id).scrollLeft-=pixels;
	moveScale(-1);
}

function moveScale(dir, id, scalePad) {
	objScale = document.getElementById("scale");
	var curr_width = parseInt(objScale.style.width); 
	var curr_left = parseInt(objScale.style.left);
	//alert(curr_left);
	if (dir == 1) {
		if ( (curr_left + curr_width) <= (960 - curr_width - scalePad + 1) ) {
			objScale.style.left = (curr_left + curr_width) + "px";
		}
		else
		{
			// reset container
			document.getElementById(id).scrollLeft = 0;
			// reset scale
			objScale.style.left = 0;
		}
	}
	else {
		if (curr_left > 0) {
			objScale.style.left = (curr_left - curr_width) + "px";
		}
	}
}

function stopMe(){
  clearTimeout(timerRight) 
  clearTimeout(timerLeft)
}

