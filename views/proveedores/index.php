<?php 
if(session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proveedores</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=1.8">
<style>

    /* Panel de búsqueda */
    .search-panel {
        width: 80%;
        margin: 0 auto 20px auto;
        background: #fce4ec;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        align-items: center;
    }

    .search-row label {
        font-weight: bold;
        color: #444;
    }

    .search-row input[type="radio"] {
        margin-right: 4px;
    }

    .search-row input[type="text"] {
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 200px;
    }

    /* Tabla más ancha */
    .table-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }

    .action-buttons {
        text-align: right;
        width: 97%;
        margin: 10px auto 15px auto;
    }

</style>
</head>
<body>

<header class="header">
    <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=index" class="btn-back">Regresar</a>
    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo" class="logo">
    </div>
    <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=logout" class="btn-logout">Cerrar sesión</a>
</header>

<main class="main-container">
    <h2 class="title">Proveedores</h2>

    <!-- FILTRO DE BÚSQUEDA -->
    <section class="search-panel">
        <div class="search-row">
            <label><input type="radio" name="tipoBusqueda" value="id" checked> ID</label>
            <label><input type="radio" name="tipoBusqueda" value="nombre"> Nombre</label>
            <label><input type="radio" name="tipoBusqueda" value="rfc"> RFC</label>
            <label><input type="radio" name="tipoBusqueda" value="telefono"> Teléfono</label>

            <input type="text" id="txtBuscar" placeholder="Escribe algo…" autocomplete="off" list="sugerencias">
            <datalist id="sugerencias"></datalist>
            <span style="margin-left:10px; font-size:14px; color:#555;">
                *Escribe para filtrar resultados*
            </span>
        </div>
    </section>

    <!-- Botón agregar -->
    <div class="action-buttons">
        <a href="<?php echo BASE_URL; ?>index.php?c=Proveedores&a=create" class="btn-action">Agregar ➕</a>
    </div>

    <!-- TABLA -->
    <div class="table-container">
        <table class="styled-table" id="tabla-proveedores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>RFC</th>
                    <th>Nombre</th>
                    <th>Domicilio</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Fondo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($proveedores)): ?>
                    <?php foreach ($proveedores as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['id']); ?></td>
                            <td><?php echo htmlspecialchars($p['rfc']); ?></td>
                            <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($p['domicilio']); ?></td>
                            <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($p['email']); ?></td>
                            <td><?php echo htmlspecialchars($p['fondo']); ?></td>
                            <td class="acciones-td">
                                <a href="<?php echo BASE_URL; ?>index.php?c=Proveedores&a=edit&id=<?php echo $p['id']; ?>" class="btn-icon">✏️</a>
                                <a href="<?php echo BASE_URL; ?>index.php?c=Proveedores&a=delete&id=<?php echo $p['id']; ?>" class="btn-icon" onclick="return confirm('¿Eliminar este proveedor?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">No hay proveedores registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination-container" style="text-align:center; margin:20px 0;">
    <button id="prevPage" class="page-btn" disabled>‹ Anterior</button>
    <span id="pageInfo" class="page-info">Página 1 de X</span>
    <button id="nextPage" class="page-btn">Siguiente ›</button>
    </div>

</main>

<script>
// --- ELEMENTOS ---
const txtBuscar = document.getElementById("txtBuscar");
const radios = document.getElementsByName("tipoBusqueda");
const filas = Array.from(document.querySelectorAll("#tabla-proveedores tbody tr"));

// --- PAGINACIÓN ---
let currentPage = 1;
const pageSize = 10;

// Obtener tipo de búsqueda
function getTipoBusqueda() {
    for (const r of radios) if (r.checked) return r.value;
    return "nombre";
}

// FILTRAR TODO
function filtrar() {
    const valor = txtBuscar.value.trim().toLowerCase();
    const tipo = getTipoBusqueda();

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll("td");
        let texto = "";

        switch (tipo) {
            case "id": texto = celdas[0].textContent.toLowerCase(); break;
            case "rfc": texto = celdas[1].textContent.toLowerCase(); break;
            case "nombre": texto = celdas[2].textContent.toLowerCase(); break;
            case "telefono": texto = celdas[4].textContent.toLowerCase(); break;
        }

        fila.dataset.visible = texto.includes(valor) ? "1" : "0";
    });

    currentPage = 1; // reinicia página cuando se busca
    renderPage();
}

// MOSTRAR SOLO LA PÁGINA ACTUAL
function renderPage() {
    const visibles = filas.filter(f => f.dataset.visible === "1");
    const totalPages = Math.max(1, Math.ceil(visibles.length / pageSize));

    // Oculta todas
    filas.forEach(f => (f.style.display = "none"));

    // Muestra solo las de esta página
    let start = (currentPage - 1) * pageSize;
    let end = Math.min(start + pageSize, visibles.length);

    for (let i = start; i < end; i++) {
        visibles[i].style.display = "";
    }

    // Actualizar texto
    document.getElementById("pageInfo").textContent =
        `Página ${currentPage} de ${totalPages}`;

    // Botones
    document.getElementById("prevPage").disabled = currentPage === 1;
    document.getElementById("nextPage").disabled = currentPage === totalPages;
}

// EVENTOS
txtBuscar.addEventListener("input", filtrar);
radios.forEach(r => r.addEventListener("change", filtrar));

document.getElementById("prevPage").addEventListener("click", () => {
    if (currentPage > 1) {
        currentPage--;
        renderPage();
    }
});

document.getElementById("nextPage").addEventListener("click", () => {
    const visibles = filas.filter(f => f.dataset.visible === "1");
    const totalPages = Math.ceil(visibles.length / pageSize);

    if (currentPage < totalPages) {
        currentPage++;
        renderPage();
    }
});

// Inicializar
filas.forEach(f => (f.dataset.visible = "1"));
renderPage();
</script>


</body>
</html>

