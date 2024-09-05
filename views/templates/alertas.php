<?php foreach($alertas as $tipo => $alerta) { ?>
    <?php foreach($alerta as $mensaje) { ?>
        <div class="alerta <?php echo $tipo ?>">
            <?php echo $mensaje ?>
        </div>
    <?php } ?>
<?php } ?>