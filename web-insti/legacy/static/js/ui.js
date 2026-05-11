//------------------------------------------------------------------------------
// Aplicación
//
var ui = {
    //--------------------------------------------------------------------------
    // Constantes
    //
    //Estados de las respuestas devueltas por el API de comunicación con el servidor
    STATUS_OK:    1,
    STATUS_FAIL:  0,
    STATUS_ERROR: -1,


    //--------------------------------------------------------------------------
    // Variables
    //
    debug: false,
    lang: 'es',


    //--------------------------------------------------------------------------
    // Componentes de la interfaz
    //
    menu: null,

    //--------------------------------------------------------------------------
    // Métodos
    //
    init: function(debug,lang,callFunction) {
        this.debug = (debug !== '0');
        this.lang = lang;

        switch( callFunction ) {
            case 'inicio': this.inicio(); break;
            case 'empresa': this.empresa(); break;
            case 'abs': this.abs(); break;
            case 'airbags': this.airbags(); break;
            case 'centralitas': this.centralitas(); break;
            case 'cuadros': this.cuadros(); break;
            case 'direcciones': this.direcciones(); break;
            case 'inmovilizadores': this.inmovilizadores(); break;
            case 'navegacion': this.navegacion(); break;
            case 'potenciacion': this.potenciacion(); break;
            case 'prestamo': this.prestamo(); break;
            case 'unidades': this.unidades(); break;
			
        }
    },


    //--------------------------------------------------------------------------
    // Miscelanea
    //
    openWindow: function(theURL,winName,winWidth,winHeight) {
        var w = (screen.width - winWidth)/2;
        var h = (screen.height - winHeight)/2 - 50;
        if((winHeight%2)==1) {
            features = 'directories=no,location=no,menubar=no,scrollbars=yes,status=yes,toolbar=no,resizable=yes,width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
        } else {
            features = 'directories=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no,width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
        }
        return window.open(theURL,winName,features);
    },

    openWindowMap: function() {
        var winWidth = 0.8*screen.width;
        var winHeight = 0.75*screen.height;
        var w = (screen.width - winWidth)/2;
        var h = (screen.height - winHeight)/2 - 20;
        var url = 'https://www.google.es/maps/place/Auto+Electrochips+S.L./@40.989191,-5.6502697,17z/data=!3m1!4b1!4m5!3m4!1s0xd3f2f4d3a5ad39d:0x94798528d7d8296c!8m2!3d40.989191!4d-5.648081';
        var features = 'directories=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no,width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
        window.open(url,'map',features);
    },
    
    /**
     * Verifica datos y envia el formulario mediante POST a la URL indicada que se abre en una nueva ventana
     * @returns {Boolean}
     */
    sendEmail: function(url) {
        //Quitamos las marcas de error
        $("#idNombre").removeClass("formulario_error");
        $("#idTelefono").removeClass("formulario_error");
        $("#idEmail").removeClass("formulario_error");
        $("#idMarca").removeClass("formulario_error");
        $("#idModelo").removeClass("formulario_error");
        $("#idDescripcion").removeClass("formulario_error");

        //Recolectamos los datos del formulario
        var nombre      = $("#idNombre").val();
        var direccion   = $("#idDireccion").val();
        var cp          = $("#idCP").val();
        var poblacion   = $("#idPoblacion").val();
        var provincia   = $("#idProvincia").val();
        var telefono    = $("#idTelefono").val();
        var email       = $("#idEmail").val();

        var marca       = $("#idMarca").val();
        var modelo      = $("#idModelo").val();
        var carroceria  = $("#idCarroceria").val();
        var motor       = $("#idMotor").val();
        var agno        = $("#idAgno").val();
        var ref         = $("#idRef").val();

        var descripcion = $("#idDescripcion").val();

        //Validamos los campos necesarios
        var valido = true;
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        if( nombre == '' || nombre == 'Nombre(*)' ) {
            valido = false;
            $("#idNombre").addClass("formulario_error");
            alert("Debe rellenar el campo NOMBRE");

        } else if( telefono == '' || telefono == 'Teléfono(*)' || telefono.length < 9 || isNaN(telefono) ) {
            valido = false;
            $("#idTelefono").addClass("formulario_error");
            alert("Debe rellenar el campo TELÉFONO con un número de teléfono válido");

        } else if( email == '' || email == 'eMail(*)' || !emailReg.test(email) ) {
            valido = false;
            $("#idEmail").addClass("formulario_error");
            alert("Debe rellenar el campo EMAIL con un correo electrónico válido");

        } else if( marca == '' || marca == 'Marca(*)' ) {
            valido = false;
            $("#idMarca").addClass("formulario_error");
            alert("Debe rellenar el campo MARCA");

        } else if( modelo == '' || modelo == 'Modelo(*)' ) {
            valido = false;
            $("#idModelo").addClass("formulario_error");
            alert("Debe rellenar el campo MODELO");

        } else if( descripcion == '' || descripcion == 'Descripción(*)' ) {
            valido = false;
            $("#idDescripcion").addClass("formulario_error");
            alert("Debe rellenar el campo DESCRIPCION");

        }

        //Enviamos el email
        if( valido ) {
            $.ajax({
                method: 'GET',
                url: './email',
                data: {
                    nombre: nombre,
                    direccion: direccion,
                    cp: cp,
                    poblacion: poblacion,
                    provincia: provincia,
                    telefono: telefono,
                    email: email,
                    marca: marca,
                    modelo: modelo,
                    carroceria: carroceria,
                    motor: motor,
                    agno: agno,
                    ref: ref,
                    descripcion: descripcion,
                    hash: (Number(telefono) + 313*nombre.length + 677*email.length)%10000
                }
            })
            .done(function(objData) {
                //alert(""+objData);
                location.href = url;
            })
            .fail(function(jqXHR,textStatus) {
                valido = false;
                alert("No se ha podido enviar la consulta online.\nPor favor, vuelva a intentarlo más tarde.");
            });
        }

        //Devolvemos si se ha enviado o no el email
        return valido;
    },


    //--------------------------------------------------------------------------
    // Menu
    //
    inicio: function() {
        $('.scrollable').scrollable({ circular: true }).navigator('.navi').autoscroll({ interval: 3000 });
        $.preload(
            './web/img/'+this.lang+'/boton_centralitas_over.jpg',
            './web/img/'+this.lang+'/boton_cuadros_over.jpg',
            './web/img/'+this.lang+'/boton_inmovilizadores_over.jpg',
            './web/img/'+this.lang+'/boton_direcciones_over.jpg',
            './web/img/'+this.lang+'/boton_abs_over.jpg',
            './web/img/'+this.lang+'/boton_airbags_over.jpg',
            './web/img/'+this.lang+'/boton_unidades_over.jpg',
            './web/img/'+this.lang+'/boton_navegacion_over.jpg',
            './web/img/'+this.lang+'/boton_potenciacion_over.jpg',
            './web/img/'+this.lang+'/boton_prestamo_over.jpg'
        );
    },
    
    empresa: function() {
        $(".scrollable").scrollable({ circular: true }).navigator(".navi").autoscroll({ interval: 3000 });
    },
    
    contacto: function() {},
	proyectos: function() {},
    
    //--------------------------------------------------------------------------
    // Secciones
    //
    abs: function() {
        this.menu = new Menu();
        $('#menu-abs').css('background-color','#b08938');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    airbags: function() {
        this.menu = new Menu();
        $('#menu-airbags').css('background-color','#5e87b8');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    centralitas: function() {
        this.menu = new Menu();
        $('#menu-centralitas').css('background-color','#844287');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    cuadros: function() {
        this.menu = new Menu();
        $('#menu-cuadros').css('background-color','#785a38');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    direcciones: function() {
        this.menu = new Menu();
        $('#menu-direcciones').css('background-color','#6c7950');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    inmovilizadores: function() {
        this.menu = new Menu();
        $('#menu-inmovilizadores').css('background-color','#0083a9');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    navegacion: function() {
        this.menu = new Menu();
        $('#menu-navegacion').css('background-color','#d27f3b');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    potenciacion: function() {
        this.menu = new Menu();
        $('#menu-potenciacion').css('background-color','#128091');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    prestamo: function() {
        this.menu = new Menu();
        $('#menu-prestamo').css('background-color','#da332d');
        $().UItoTop({ easingType: 'easeOutQuart' });
    },

    unidades: function() {
        this.menu = new Menu();
        $('#menu-unidades').css('background-color','#8d475f');
        $().UItoTop({ easingType: 'easeOutQuart' });
    }
};