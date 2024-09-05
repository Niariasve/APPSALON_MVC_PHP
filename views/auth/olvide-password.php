<h1 class="nombre-pagina">Olvide mi Contraseña</h1>
<p class="descripcion-pagina">Reestablece tu contraseña escribiendo tu email y siguiendo las instrucciones</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form class="formulario" method="POST" action="/olvide">
    <div class="campo">
        <label for="email">E-mail</label>
        <input 
            type="email"
            id="email"
            name="email"
            placeholder="Tu E-mail";
        />
    </div>
    <input type="submit" class="boton-azul" value="Enviar Email">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>