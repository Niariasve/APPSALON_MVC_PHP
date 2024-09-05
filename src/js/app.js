let paso = 1;
const pasoInicial = 1
const pasoFinal = 3

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function () {
    iniciarApp()
})

function iniciarApp() {

    mostrarSeccion();
    tabs(); //Cambia la seccion cuando se presionen los tabs
    botonesPaginador(); //Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); //Consulta la api en el backend de php

    idCliente(); //Guarda el id del cliente en cita
    nombreCliente(); //Guarda el nombre del cliente en la cita
    seleccionarFecha(); //Guarda la fecha en la cita
    seleccionarHora(); //Guarda la hora en la cita

    mostrarResumen(); //Muestra el resumen de la cita
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button')

    botones.forEach(boton => {
        boton.addEventListener('click', (e) => {
            paso = parseInt(e.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();
        })
    })
}

function mostrarSeccion() {

    //Ocultar la seccion que tenga la clase mostrar
    const seccionAnterior = document.querySelector('.mostrar')

    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar')
    }

    //Seleccionar la seccion con el paso
    const seccion = document.querySelector(`#paso-${paso}`)
    seccion.classList.add('mostrar')

    //Quita la clase actual al tab anterior
    const tabAnterior = document.querySelector('.actual')
    tabAnterior.classList.remove('actual')

    //Resalta el tab actual 
    const tab = document.querySelector(`[data-paso="${paso}"]`)
    tab.classList.add('actual')
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior')
    const paginaSiguiente = document.querySelector('#siguiente')

    if (paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente')
    paginaSiguiente.addEventListener('click', () => {
        if (paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
        mostrarSeccion();
    })
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior')
    paginaAnterior.addEventListener('click', () => {
        if (paso <= pasoInicial) return;
        paso--;
        botonesPaginador();
        mostrarSeccion();
    })
}

async function consultarAPI() {

    try {
        const url = `${location.origin}/api/servicios`;
        const resultado = await fetch(url);
        const servicios = await resultado.json();

        mostrarServicios(servicios);
    } catch (error) {
        console.log(error)
    }
}

function mostrarServicios(servicios) {

    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function () {
            seleccionarServicio(servicio)
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    });
}

function seleccionarServicio(servicio) {
    const { id } = servicio
    const { servicios } = cita;
    const servicioDiv = document.querySelector(`[data-id-servicio="${id}"]`);

    //Comprobar si un servicio ya fue agregado o quitarlo
    if (servicios.some(agregado => agregado.id === id)) {
        //Eliminarlo 
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        servicioDiv.classList.remove('seleccionado');
    } else {
        //Agregarlo 
        cita.servicios = [...servicios, servicio]
        servicioDiv.classList.add('seleccionado');
    }
}

function idCliente() {
    cita.id = document.querySelector('#id').value;
}

function nombreCliente() {
    const nombre = document.querySelector('#nombre');
    cita.nombre = nombre.value;
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha')
    inputFecha.addEventListener('input', (e) => {
        const dia = new Date(e.target.value).getUTCDay();

        //No se pueden realizar citas ni sabado ni domingo
        if ([6, 0].includes(dia)) {
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
    });
}


function seleccionarHora() {
    const inputHora = document.querySelector('#hora')
    inputHora.addEventListener('input', (e) => {

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];

        console.log(hora)

        if (hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Hora no válida', 'error', '.formulario');
        } else {
            cita.hora = horaCita;
        }
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    const alertaPrevia = document.querySelector('.alerta');
    if (alertaPrevia) {
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if (desaparece) {
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    //Limpiar el contenido de resumen
    while (resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if (Object.values(cita).includes('') || cita.servicios.length === 0) {
        mostrarAlerta('Faltan datos de servicios, fecha u hora', 'error', '.contenido-resumen', false);
        return;
    }

    //Formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita;

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`

    //Formatear fecha para que se vea mas amigable

    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, mes, dia));

    const opciones = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);
    console.log(fechaFormateada);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Cita:</span> ${fechaFormateada}`

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>hora:</span> ${hora} Horas`

    //Heading para servicio y resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de servicios';
    resumen.appendChild(headingServicios);

    //Iterando y mostrando los servicios 
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    })

    //Heading para Cita y resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de cita';
    resumen.appendChild(headingCita);

    //Boton para crear una cita 
    const reservar = document.createElement('BUTTON');
    reservar.classList.add('boton-azul');
    reservar.textContent = 'Reservar Cita';
    reservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(reservar);
}

async function reservarCita() {
    const { id, fecha, hora, servicios } = cita;

    const idServicios = servicios.map(servicio => servicio.id);

    const datos = new FormData();

    datos.append('usuarioId', id);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('servicios', idServicios);

    try {
        //Peticion a la api
        const url = `${location.origin}/api/citas`;

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if (resultado.resultado) {
            Swal.fire({
                icon: "success",
                title: "Cita Creada",
                text: "Tu cita fue creada correctamente",
                confirmButtonText: '¡Entendido!'
            })
            .then(() => {
                window.location.reload();
            })
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "¡Ha ocurrido un error al guardar la cita!"
        });
        console.log(error)
    }



    // console.log([...datos]);
}