<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Factura</title>
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
    <h2 class="title">Editar Factura #<?php echo $factura['id']; ?></h2>

    <form method="post" action="<?php echo BASE_URL; ?>index.php?c=Facturas&a=actualizar" class="form-container">
        <input type="hidden" name="id" value="<?php echo $factura['id']; ?>">

        <label>Proveedor:</label>
        <select name="proveedor" required style="width: 500px; padding:8px; font-size:16px, border-radius: 16px;">
            <option value="">-- Seleccione --</option>
            <?php foreach($proveedores as $p): ?>
                <option value="<?php echo $p['id']; ?>" <?php if($factura['proveedor']==$p['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($p['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="2"><?php echo htmlspecialchars($factura['descripcion']); ?></textarea>

        <label>Número de Factura:</label>
        <input type="text" name="numero" value="<?php echo htmlspecialchars($factura['numero']); ?>" required>

        <label>Cantidad:</label>
        <input type="number" step="0.01" name="cantidad" value="<?php echo $factura['cantidad']; ?>" required>

        <label>Estatus:</label>
        <select name="estatus" required>
            <option value="1" <?php if($factura['estatus']=='Se debe') echo 'selected'; ?>>Se debe</option>
            <option value="0" <?php if($factura['estatus']=='Pagada') echo 'selected'; ?>>Pagada</option>
        </select>

        <label>Fecha Emisión:</label>
        <input type="date" name="fecha1" value="<?php echo $factura['fecha1']; ?>">

        <label>Fecha Vencimiento:</label>
        <input type="date" name="fecha2" value="<?php echo $factura['fecha2']; ?>">

        <label>Cheque:</label>
        <input type="text" name="cheque" value="<?php echo $factura['cheque']; ?>">

        <label>Suma Total:</label>
        <input type="number" step="0.01" name="suma" value="<?php echo $factura['suma']; ?>">

        <input type="hidden" name="contrarecibo" value="<?php echo $factura['contrarecibo']; ?>">

        <div class="form-buttons">
            <button type="submit" class="btn-action">Guardar</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=Facturas&a=index" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>

