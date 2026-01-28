<?php
$inicio = $_GET['inicio'] ?? '';
$fin    = $_GET['fin'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes del Sistema</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=1.8">
</head>
<body>

<!-- HEADER -->
<div class="header">
    <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=principal" class="btn-back">
        Regresar
    </a>

    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" class="logo" alt="Logo">
    </div>

    <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=logout" class="btn-logout">
        Cerrar sesión
    </a>
</div>

<!-- CONTENIDO -->
<div class="main-container">

    <h2 class="title">Bitácora del Sistema</h2>

    <!-- PANEL DE FILTRO -->
    <div class="search-panel">
        <form method="GET" action="<?php echo BASE_URL; ?>index.php">

            <input type="hidden" name="c" value="Reportes">
            <input type="hidden" name="a" value="index">

            <div class="filter-row">
                <div style="flex:1">
                    <label>Desde</label>
                    <input
                        type="date"
                        name="inicio"
                        value="<?php echo htmlspecialchars($inicio); ?>"
                        required
                    >
                </div>

                <div style="flex:1">
                    <label>Hasta</label>
                    <input
                        type="date"
                        name="fin"
                        value="<?php echo htmlspecialchars($fin); ?>"
                        required
                    >
                </div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-action">
                    Buscar
                </button>

                <?php if (!empty($registros) && $inicio && $fin): ?>
                    <a
                        class="btn-action"
                        target="_blank"
                        href="<?php echo BASE_URL; ?>index.php?c=Reportes&a=bitacoraPdf&inicio=<?php echo urlencode($inicio); ?>&fin=<?php echo urlencode($fin); ?>">
                        Descargar PDF
                    </a>
                <?php endif; ?>
            </div>

        </form>
    </div>

    <!-- TABLA -->
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>

            <?php if (!empty($registros)): ?>
                <?php foreach ($registros as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['usuario_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($r['accion']); ?></td>
                        <td><?php echo htmlspecialchars($r['modulo']); ?></td>
                        <td><?php echo htmlspecialchars($r['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($r['hora']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; font-weight:bold;">
                        No hay registros en el periodo seleccionado
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>

