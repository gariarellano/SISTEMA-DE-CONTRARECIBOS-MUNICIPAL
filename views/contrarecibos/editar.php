<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Contrarecibo</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=<?php echo time(); ?>">
    <style>
        .main-container { max-width: 900px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #c2185b; }
        .readonly-input { background-color: #f3f3f3; border: 1px solid #ccc; padding: 8px; border-radius: 5px; width: 100%; }
        .table-container { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #c2185b; color: white; }
        tr:hover { background: #f9f9f9; }
        .actions { text-align: center; margin-top: 20px; }
        .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-primary { background-color: #c2185b; color: white; }
        input[type="checkbox"]:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }
    .btn-cancel {
    display: inline-block;
    background-color: #777272ff; /* gris suave */
    color: #fff;
    padding: 7px 15px;
    border-radius: 5px;
    text-decoration: none; /* quita subrayado */
    font-size: 15px;
    font-weight: 400;
    border: none;
    cursor: pointer;
}

.btn-cancel:hover {
    background-color: #3a3939ff; /* gris más oscuro */
}
    </style>
</head>
<body>
    <header class="header">
    <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=index" class="btn-back">Regresar</a>
    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo" class="logo">
    </div>
    <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=logout" class="btn-logout">Cerrar sesión</a>
</header>
<div class="main-container">
    <h1>Editar Contrarecibo #<?php echo htmlspecialchars($contrarecibo['id']); ?></h1>
    
    <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=update">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($contrarecibo['id']); ?>">

        <label>Proveedor:</label>
        <input type="text" class="readonly-input" 
               value="<?php echo htmlspecialchars($contrarecibo['proveedor_nombre']); ?>" readonly>

        <label>Fecha:</label>
        <input type="text" class="readonly-input" 
               value="<?php echo htmlspecialchars($contrarecibo['fecha_contrarecibo'] ?? ''); ?>" readonly>

        <div class="table-container">
            <h3>Facturas asociadas</h3>
            <table>
                <thead>
                    <tr>
                        <th>Seleccionar</th>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $facturasAsociadas = array_column($contrarecibo['facturas'], 'id');
                foreach ($facturas as $factura):
                    $checked = in_array($factura['id'], $facturasAsociadas) ? 'checked' : '';
                ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="facturas[]" value="<?php echo $factura['id']; ?>" 
                                    <?php echo $checked; ?>>
                        </td>
                        <td><?php echo htmlspecialchars($factura['id']); ?></td>
                        <td><?php echo htmlspecialchars($factura['fecha_factura'] ?? ''); ?></td>
                        <td>$<?php echo number_format($factura['cantidad'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=index" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[type="checkbox"]').forEach(chk => {
    if (chk.checked) {
        chk.addEventListener('click', function(e) {
            // Permite desmarcar y volver a marcar
            this.toggleAttribute('data-deseleccionado');
        });
    }
});
</script>

</body>
</html>

