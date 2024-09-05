<div class="barra">
    <p>Hola: <?php echo $nombre ?? '';?></p>

    <a class="boton-azul" href="/logout">Cerrar Sesión</a>
</div>

<?php if (isset($_SESSION['admin'])) { ?>
    <div class="barra-servicios">
        <a href="/admin" class="boton-azul">Ver Cita</a>
        <a href="/servicios" class="boton-azul">Ver Servicios</a>
        <a href="/servicios/crear" class="boton-azul">Nuevo Servicio</a>
    </div>
<?php } ?>