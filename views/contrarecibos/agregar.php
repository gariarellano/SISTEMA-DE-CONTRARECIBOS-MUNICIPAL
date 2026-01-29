<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . '/../../config.php');

// Corrección: evitar error si el controlador no definió la variable
$proveedoresPendientes = $proveedoresPendientes ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Contrarecibo</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?">
    <style>
        .main-container { 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 20px; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
        }
        h2 { 
            color: #c2185b; 
            margin-bottom: 20px; 
            text-align:center; 
        }
        .select-box { 
            margin-bottom: 20px; 
            text-align:center; 
        }

        /* Contenedor de facturas */
        .facturas-container {
            display: flex;
            justify-content: center; 
            gap: 10px; 
            margin-top: 20px;
        }

        /* Cada lista de facturas */
        .factura-list {
            flex: 0 0 45%; 
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fafafa;
            max-height: 400px; /* Altura fija para scroll */
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Encabezado fijo */
        .factura-list h3 { 
            position: sticky;
            top: 0;
            background-color: #c2185b; 
            color: white; 
            padding: 8px; 
            margin: 0 0 10px 0;
            text-align: center; 
            z-index: 1;
            border-radius: 8px 8px 0 0;
        }

        /* Items de factura */
        .factura-item { 
            padding: 6px; 
            border-bottom: 1px solid #eee; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            font-size:14px; 
        }
        .factura-item:last-child { border-bottom: none; }
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
    <h2>Agregar Contrarecibo</h2>

    <!-- FORMULARIO -->
    <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=store">

        <div class="select-box">
            <label for="proveedor"><strong>Seleccionar proveedor con facturas pendientes:</strong></label><br>
            <br>
            <select id="proveedor" name="proveedor" required style="width: 500px; padding:8px; font-size:16px; border-radius: 16px;">
                <option value="">-- Seleccione un proveedor --</option>

                <?php if (!empty($proveedoresPendientes)): ?>
                    <?php foreach ($proveedoresPendientes as $p): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">(No hay proveedores con facturas pendientes)</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="facturas-container">
            <div class="factura-list">
                <h3>Facturas Pendientes</h3>
                <div id="facturas-pendientes"><em>Seleccione un proveedor</em></div>
            </div>

            <div class="factura-list">
                <h3>Facturas Pagadas</h3>
                <div id="facturas-pagadas"><em>Seleccione un proveedor</em></div>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="form-buttons">
            <button type="submit" class="btn-action">
                Guardar
            </button>

            <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=index" 
               class="btn-cancel">
                Cancelar
            </a>
        </div>

    </form>
</div>

<script>
document.getElementById('proveedor').addEventListener('change', function() {
    const id = this.value;
    const pendientesDiv = document.getElementById('facturas-pendientes');
    const pagadasDiv = document.getElementById('facturas-pagadas');

    pendientesDiv.innerHTML = "Cargando...";
    pagadasDiv.innerHTML = "Cargando...";

    if (!id) {
        pendientesDiv.innerHTML = "<em>Seleccione un proveedor</em>";
        pagadasDiv.innerHTML = "<em>Seleccione un proveedor</em>";
        return;
    }

    fetch(`<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=getFacturasProveedor&id=${id}`)
        .then(res => res.json())
        .then(data => {
            pendientesDiv.innerHTML = "";
            pagadasDiv.innerHTML = "";

            // Facturas pendientes
            if (data.pendientes && data.pendientes.length) {
                data.pendientes.forEach(f => {
                    pendientesDiv.innerHTML += `
                        <div class="factura-item">
                            <label>
                                <input type="checkbox" name="facturas[]" value="${f.id}">
                                <strong>${f.numero}</strong> - $${parseFloat(f.cantidad).toFixed(2)}
                            </label>
                            <span>${f.fecha_factura}</span>
                        </div>`;
                });
            } else {
                pendientesDiv.innerHTML = "<em>No hay facturas pendientes</em>";
            }

            // Facturas pagadas
            if (data.pagadas && data.pagadas.length) {
                data.pagadas.forEach(f => {
                    pagadasDiv.innerHTML += `
                        <div class="factura-item">
                            <span><strong>${f.numero}</strong> - $${parseFloat(f.cantidad).toFixed(2)}</span>
                            <span>${f.fecha_factura}</span>
                        </div>`;
                });
            } else {
                pagadasDiv.innerHTML = "<em>No hay facturas pagadas</em>";
            }
        })
        .catch(err => {
            pendientesDiv.innerHTML = "Error al cargar.";
            pagadasDiv.innerHTML = "Error al cargar.";
            console.error(err);
        });
});
</script>

</body>
</html>
