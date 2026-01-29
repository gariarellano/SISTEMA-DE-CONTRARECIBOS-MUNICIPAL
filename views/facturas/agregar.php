<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Factura</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?">
</head>
<body>
<header class="header">
    <a href="<?php echo BASE_URL; ?>index.php?c=Facturas&a=index" class="btn-back">Regresar</a>
    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo" class="logo">
    </div>
    <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=logout" class="btn-logout">Cerrar sesión</a>
</header>

<main class="main-container">
    <h2 class="title">Agregar Nueva Factura</h2>

    <form method="post" action="<?php echo BASE_URL; ?>index.php?c=Facturas&a=guardar" class="form-container">
        <label>Proveedor:</label>
        <select name="proveedor" required style="width: 500px; padding:8px; font-size:16px, border-radius: 16px;">
            <option value="">-- Seleccione el proveedor--</option>
            <?php foreach($proveedores as $p): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="2"></textarea>

        <label>Número de Factura:</label>
        <input type="text" name="numero" required>

        <label>Cantidad:</label>
        <input type="number" step="0.01" name="cantidad" required>

        <label>Estatus:</label>
        <select name="estatus" required style="width: 500px; padding:8px; font-size:16px, border-radius: 16px;">
            <option value="1">Se debe</option>
            <option value="0">Pagada</option>
        </select>

        <label>Fecha Emisión:</label>
        <input type="date" name="fecha1">

        <label>Fecha Vencimiento:</label>
        <input type="date" name="fecha2">

        <label>Número de Cheque:</label>
        <input type="text" name="cheque">

        <label>Suma Total:</label>
        <input type="number" step="0.01" name="suma">

        <div class="form-buttons">
            <button type="submit" class="btn-action">Guardar</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=Facturas&a=index" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
<style>

</style>

