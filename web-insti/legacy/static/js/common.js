//------------------------------------------------------------------------------
// Path:        js/common.js
// Version: 	v001
// Description:	Funciones de uso común
//------------------------------------------------------------------------------

//Open new window in the screen center
function openWindow(theURL,winName,winWidth,winHeight) {
    var w = (screen.width - winWidth)/2;
    var h = (screen.height - winHeight)/2 - 50;
	if((winHeight%2)==1) {
		features = 'directories=no,location=no,menubar=no,scrollbars=yes,status=yes,toolbar=no,resizable=yes,width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
	} else {
		features = 'directories=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no,width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
	}
    window.open(theURL,winName,features);
}

function openWindowMap() {
	var winWidth = 0.8*screen.width;
	var winHeight = 0.75*screen.height;
    var w = (screen.width - winWidth)/2;
    var h = (screen.height - winHeight)/2 - 20;
	var url = 'https://www.google.es/maps/place/Auto+Electrochips+S.L./@40.993806,-5.645023,17z/data=!3m1!4b1!4m2!3m1!1s0xd3f2f4d3a5ad39d:0x94798528d7d8296c';
	var features = 'directories=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no,width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
	window.open(url,'map',features);
}