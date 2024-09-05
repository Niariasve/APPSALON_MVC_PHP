<h1 class="nombre-pagina">Recuperar Password</h1>
<p class="descripcion-pagina">Coloca tu nueva contraseña acontinuacion</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<?php if($error) return; ?>

<form class="formulario" method="POST" action="">
    <div class="campo">
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Tu nuevo password"
        />
    </div>
    <input type="submit" class="boton-azul" value="Reestablecer password">
</form>

<div class="acciones">
    <a href="/">Iniciar Sesión</a>
</div>

