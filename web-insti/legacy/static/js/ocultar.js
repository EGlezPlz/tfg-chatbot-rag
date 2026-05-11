var clic = 1;
function divLogin(){ 
   /*var ventana = document.getElementById("caja2");
    ventana.style.display = "inline-block";*/
   if(clic==1){
   document.getElementById("caja").style.height = "100px";
   document.getElementById("boton").style.display = "inline-block";
   document.getElementById("boton2").style.display = "inline-block";
   clic = clic + 1;
   } else{  
		document.getElementById("caja").style.height = "0px";
		document.getElementById("boton").style.display = "none";	   
		document.getElementById("boton2").style.display = "none";
		clic = 1;
   }   
}

function mostrarES()
{
    var ventana = document.getElementById("instruccionesES");
  //  ventana.style.marginTop = "4%";
  //  ventana.style.left = "24%";
    ventana.style.display = "block";
}

function ocultarES()
{
    var ventana = document.getElementById("instruccionesES");
    ventana.style.display = "none";
}

function mostrarPT()
{
    var ventana = document.getElementById("instruccionesPT");
    //ventana.style.marginTop = "100px";
    //ventana.style.left = ((document.body.clientWidth-100) / 2) +  "px";
    ventana.style.display = "block";
}

function ocultarPT()
{
    var ventana = document.getElementById("instruccionesPT");
    ventana.style.display = "none";
}

/*function divIns(){
	if(clic==1){
		document.getElementById("instrucciones").style.visibility="visible";
		clic=clic+1;
	}
	else{
		document.getElementById("instrucciones").style.visibility="hidden";
		clic=1
	}
}*/
