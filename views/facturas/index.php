<?php 
if(session_status() === PHP_SESSION_NONE) session_start();

setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish', 'es_MX.UTF-8');
date_default_timezone_set('America/Mexico_City');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Facturas</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=1.8">
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
    <h2 class="title">Facturas</h2>

    <!-- PANEL DE BÚSQUEDA -->
    <section class="search-panel">
        <div class="search-row">
            <label><input type="radio" name="tipoBusqueda" value="id" checked> ID</label>
            <label><input type="radio" name="tipoBusqueda" value="proveedor"> Proveedor</label>
            <label><input type="radio" name="tipoBusqueda" value="factura"> Número de Factura</label>
            <label><input type="radio" name="tipoBusqueda" value="cantidad"> Cantidad</label>

            <input type="text" id="txtBuscar" placeholder="Escribe algo…" autocomplete="off" list="sugerencias">
            <datalist id="sugerencias"></datalist>

            <span style="margin-left:10px; font-size:14px; color:#555;">
                *Escribe para filtrar resultados*
            </span>
        </div>

        <div class="filter-row">
            <!-- Meses -->
            <select id="mes">
                <option value="">Mes</option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>

            <select id="proveedorSelect">
                <option value="">Proveedor</option>
                <?php
                if(!empty($proveedores)) {
                    sort($proveedores, SORT_STRING | SORT_FLAG_CASE);
                    foreach($proveedores as $p) {
                        echo "<option value='".htmlspecialchars($p)."'>".htmlspecialchars($p)."</option>";
                    }
                }
                ?>
            </select>

            <select id="anio">
                <option value="">Año</option>
                <?php $yearNow = date('Y'); for($y=$yearNow; $y>=$yearNow-10; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>

            <select id="fondoSelect">
                <option value="">Tipo de fondo</option>
                <?php
                $fondosOpciones = ["Fondo III", "Obra convenida", "Gasto corriente", "Aporte Federal", "Otras aportaciones"];
                foreach($fondosOpciones as $f) {
                    echo "<option value='".htmlspecialchars($f)."'>".htmlspecialchars($f)."</option>";
                }
                ?>
            </select>
        </div>
    </section>

    <div class="action-buttons">
        <a href="<?php echo BASE_URL; ?>index.php?c=Facturas&a=agregar" class="btn-action">Agregar ➕</a>
    </div>
</main>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th class="proveedor">Proveedor</th>
                <th class="descripcion">Descripción</th>
                <th>Número</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="facturasBody"></tbody>
    </table>
</div>

<div class="pagination-container">
    <button id="prevPage" class="page-btn" disabled>‹ Anterior</button>
    <span id="pageInfo" class="page-info">Página 1</span>
    <button id="nextPage" class="page-btn">Siguiente ›</button>
</div>

<script>
const BASE_URL = '<?php echo BASE_URL; ?>';
const txtBuscar = document.getElementById('txtBuscar');
const datalist = document.getElementById('sugerencias');
const tipoRadios = document.getElementsByName('tipoBusqueda');
const proveedorSelect = document.getElementById('proveedorSelect');
const fondoSelect = document.getElementById('fondoSelect');
const mesSelect = document.getElementById('mes');
const anioSelect = document.getElementById('anio');
const facturasBody = document.getElementById('facturasBody');

// Mapa de nombres para los fondos
const fondoMap = {
    "1": "Fondo III",
    "2": "Obra convenida",
    "3": "Gasto corriente",
    "4": "Aporte Federal",
    "5": "Otras aportaciones"
};

// PAGINACIÓN
let currentPage = 1;
const pageSize = 10;
let currentList = [];

function escapeHtml(s){
    return (s==null)?'':String(s).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

function parseFechaObj(fechaStr) {
    if (!fechaStr) return null;
    const parts = fechaStr.split("-");
    if (parts.length < 3) return null;
    const [y, m, d] = parts.map(Number);
    if (!y || !m || !d) return null;
    return new Date(y, m-1, d);
}

const rawFacturas = <?php echo json_encode($facturas); ?> || [];

const allFacturas = rawFacturas.map(f => {
    const fechaStr = f.fecha_factura || f.fecha1 || f.fecha || null;
    const fechaObj = parseFechaObj(fechaStr);

    const fondoNum = (typeof f.fondo !== 'undefined' && f.fondo !== null) ? f.fondo
                    : (typeof f.fondo_numero !== 'undefined' ? f.fondo_numero : (f.fondo_num || ''));

    const proveedorNombre = f.proveedor || f.proveedor_nombre || f.proveedorName || '';

    return {
        ...f,
        id: f.id,
        proveedor: proveedorNombre,
        _proveedor: String(proveedorNombre).toLowerCase(),
        numero: f.numero || '',
        _numero: String(f.numero || '').toLowerCase(),
        descripcion: f.descripcion || '',
        _descripcion: String(f.descripcion || '').toLowerCase(),
        cantidad: f.cantidad || 0,
        _cantidad: String(f.cantidad || ''),
        fecha_factura: fechaStr,
        _fechaObj: fechaObj,
        _fechaTs: fechaObj ? fechaObj.getTime() : null,
        anio: fechaObj ? fechaObj.getFullYear() : (f.anio ? Number(f.anio) : null),
        fondo: String(fondoNum),
        fondoNombre: fondoMap[String(fondoNum)] || (f.fondoNombre || f.fondo_texto || '')
    };
});

function renderPagination() {
    const total = currentList.length;
    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;
    document.getElementById("pageInfo").textContent = `Página ${currentPage} de ${totalPages}`;
    document.getElementById("prevPage").disabled = currentPage === 1;
    document.getElementById("nextPage").disabled = currentPage === totalPages;
}

function renderRows() {
    const start = (currentPage - 1) * pageSize;
    const end = start + pageSize;
    const pageItems = currentList.slice(start, end);

    if (!pageItems.length) {
        facturasBody.innerHTML = `<tr><td colspan="7">No se encontraron facturas</td></tr>`;
        renderPagination();
        return;
    }

    facturasBody.innerHTML = pageItems.map(f => `
        <tr>
            <td>${escapeHtml(f.id)}</td>
            <td>${escapeHtml(f.proveedor)}</td>
            <td>${escapeHtml(f.descripcion)}</td>
            <td>${escapeHtml(f.numero)}</td>
            <td>$${Number(f.cantidad).toLocaleString('es-MX',{minimumFractionDigits:2})}</td>
            <td>${escapeHtml(f.fecha_factura || '')}</td>
            <td>
                <a href="${BASE_URL}index.php?c=Facturas&a=edit&id=${f.id}" class="btn-icon">✏️</a>
                <a href="${BASE_URL}index.php?c=Facturas&a=delete&id=${f.id}" class="btn-icon" onclick="return confirm('¿Eliminar factura?')">🗑️</a>
            </td>
        </tr>
    `).join('');

    renderPagination();
}

function getTipoBusqueda(){ 
    for (let r of tipoRadios) if (r.checked) return r.value;
    return "id";
}

function doFilter() {
    const term = txtBuscar.value.trim().toLowerCase();
    const tipo = getTipoBusqueda();

    const mes = mesSelect.value ? Number(mesSelect.value) : null;
    const anio = anioSelect.value ? Number(anioSelect.value) : null;
    const prov = proveedorSelect.value ? proveedorSelect.value.toLowerCase() : '';
    const fondo = fondoSelect.value ? fondoSelect.value.toLowerCase() : '';

    currentList = allFacturas.filter(f => {
        if (term) {
            if (tipo === "id" && !String(f.id).includes(term)) return false;
            if (tipo === "proveedor" && !f._proveedor.includes(term)) return false;
            if (tipo === "factura" && !f._numero.includes(term)) return false;
            if (tipo === "cantidad" && !f._cantidad.includes(term)) return false;
        }

        if (prov && f._proveedor !== prov) return false;

        if (fondo) {
            if (isNaN(Number(fondo))) {
                if (!f.fondoNombre || f.fondoNombre.toLowerCase() !== fondo) return false;
            } else {
                if (String(f.fondo) !== String(fondo)) return false;
            }
        }

        if (mes && f._fechaObj && (f._fechaObj.getMonth() + 1) !== mes) return false;
        if (mes && !f._fechaObj) return false;

        if (anio && f.anio && Number(f.anio) !== anio) return false;
        if (anio && !f.anio && f._fechaObj && f._fechaObj.getFullYear() !== anio) return false;
        if (anio && !f.anio && !f._fechaObj) return false;

        return true;
    });

    currentPage = 1;
    renderRows();
}

function debounce(fn, d){
    let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args),d); }
}

const autoFill = debounce(() => {
    const term = txtBuscar.value.trim().toLowerCase();
    if (!term) { datalist.innerHTML = ""; return; }

    const tipo = getTipoBusqueda();
    let list = [];

    if (tipo === "id") list = allFacturas.map(f => String(f.id));
    if (tipo === "proveedor") list = allFacturas.map(f => f._proveedor);
    if (tipo === "factura") list = allFacturas.map(f => f._numero);
    if (tipo === "cantidad") list = allFacturas.map(f => f._cantidad);

    list = list.filter(v => v && v.includes(term));
    list = [...new Set(list)].slice(0, 10);

    datalist.innerHTML = list.map(i => `<option value="${escapeHtml(i)}">`).join('');
}, 200);

txtBuscar.addEventListener("input", autoFill);
txtBuscar.addEventListener("input", debounce(doFilter, 200));

[mesSelect, anioSelect, proveedorSelect, fondoSelect].forEach(s => s.addEventListener("change", doFilter));
for (let r of tipoRadios) r.addEventListener("change", () => { txtBuscar.value = ""; datalist.innerHTML = ""; doFilter(); });

document.getElementById("prevPage").addEventListener("click", () => {
    if (currentPage > 1) { currentPage--; renderRows(); }
});
document.getElementById("nextPage").addEventListener("click", () => {
    const totalPages = Math.ceil(currentList.length / pageSize) || 1;
    if (currentPage < totalPages) { currentPage++; renderRows(); }
});

currentList = allFacturas.slice();
renderRows();
</script>

</body>
</html>

