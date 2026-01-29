<?php 
if(session_status() === PHP_SESSION_NONE) session_start();
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish');
date_default_timezone_set('America/Mexico_City');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contrarecibos</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=1.8">
<style>
.factura-item {
    padding: 2px 4px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
.factura-item:last-child {
    border-bottom: none;
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
    <h2 class="title">Contrarecibos</h2>

    <!-- PANEL DE BÚSQUEDA -->
    <section class="search-panel">
        <div class="search-row">
            <label><input type="radio" name="tipoBusqueda" value="id" checked> ID</label>
            <label><input type="radio" name="tipoBusqueda" value="proveedor"> Proveedor</label>
            <label><input type="radio" name="tipoBusqueda" value="factura"> Factura</label>

            <input type="text" id="txtBuscar" placeholder="Escribe algo…" autocomplete="off" list="sugerencias">
            <datalist id="sugerencias"></datalist>

            <span style="margin-left:10px; font-size:14px; color:#555;">
                *Escribe para filtrar resultados*
            </span>
        </div>

        <div class="filter-row">
            <select id="mes">
                <option value="">Mes</option>
                <?php
                $meses = [
                 1 => "Enero", 2 => "Febrero", 3 => "Marzo",
                 4 => "Abril", 5 => "Mayo", 6 => "Junio",
                 7 => "Julio", 8 => "Agosto", 9 => "Septiembre",
                 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
                ];

             foreach ($meses as $num => $nombre):
                ?>
             <option value="<?= $num ?>"><?= $nombre ?></option>
             <?php endforeach; ?>
            </select>

            <select id="anio">
                <option value="">Año</option>
                <?php $yearNow = date('Y'); for($y=$yearNow; $y>=$yearNow-10; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </section>

   <div class="action-buttons">
    <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=agregar" class="btn-action">Agregar ➕</a>
</div>

</main>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Proveedor</th>
                <th>Facturas</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="contrarecibosBody">
        <?php if (!empty($contrarecibos)): ?>
            <?php foreach ($contrarecibos as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['id'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['proveedor'] ?? ''); ?></td>

                    <td>
                        <?php if (!empty($c['facturas'])): ?>
                            <?php foreach ($c['facturas'] as $f): ?>
                                <div class="factura-item">
                                    #<?php echo htmlspecialchars($f['numero'] ?? ''); ?> |
                                    <?php echo htmlspecialchars($f['fecha'] ?? ''); ?> |
                                    $<?php echo number_format($f['cantidad'] ?? 0, 2); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <em>Sin facturas</em>
                        <?php endif; ?>
                    </td>

                    <td><?php echo htmlspecialchars($c['fecha'] ?? ''); ?></td>

                    <td> 
                        <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=edit&id=<?php echo $c['id']; ?>" class="btn-icon">✏️</a>
                        <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=delete&id=<?php echo $c['id']; ?>" class="btn-icon" onclick="return confirm('¿Eliminar este contrarecibo?')">🗑️</a>
                        <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=pdf&id=<?php echo $c['id']; ?>" class="btn-icon">📄</a>
                    </td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No hay registros</td></tr>
        <?php endif; ?>
        </tbody>
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
const mesSelect = document.getElementById('mes');
const anioSelect = document.getElementById('anio');

// ------------------------------
// PAGINACIÓN GLOBAL
// ------------------------------
let currentPage = 1;
const pageSize = 10;
let filteredList = []; // ← Aquí se guarda el resultado filtrado actual

function renderPagination() {
    const total = filteredList.length;
    const totalPages = Math.ceil(total / pageSize) || 1;

    const start = (currentPage - 1) * pageSize;
    const end = Math.min(start + pageSize, total);

    const rowsToShow = filteredList.slice(start, end);
    renderRows(rowsToShow);

    document.getElementById("pageInfo").textContent =
        `Página ${currentPage} de ${totalPages}`;

    document.getElementById("prevPage").disabled = currentPage === 1;
    document.getElementById("nextPage").disabled = currentPage === totalPages;
}

document.getElementById("prevPage").addEventListener("click", () => {
    currentPage--;
    renderPagination();
});
document.getElementById("nextPage").addEventListener("click", () => {
    currentPage++;
    renderPagination();
});

// ------------------------------
// PARSE FECHA
// ------------------------------
function parseFecha(fechaStr) {
    if (!fechaStr) return null;
    const [y, m, d] = fechaStr.split("-").map(Number);
    return {
        year: y,
        month: m,
        day: d,
        ts: new Date(y, m - 1, d).getTime()
    };
}

// ------------------------------
// PREPARAR DATOS
// ------------------------------
const allContrarecibos = <?php echo json_encode($contrarecibos); ?>.map(c => {
    const f = parseFecha(c.fecha);
    return {
        ...c,
        _id: String(c.id),
        _proveedor: c.proveedor ? c.proveedor.toLowerCase() : "",
        _fechaObj: f,
        _fechaTs: f ? f.ts : null,
        _facturas: Array.isArray(c.facturas)
           ? c.facturas.map(f => f.numero).join(",").toLowerCase()
           : ""
    };
});

function escapeHtml(s){return (s==null)?'':String(s).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}
function getTipoBusqueda(){ for(const r of tipoRadios) if(r.checked) return r.value; return 'proveedor'; }
function debounce(fn, delay){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), delay); } }

// ------------------------------
// RENDER FILAS
// ------------------------------
function renderRows(list){
    const tbody = document.getElementById("contrarecibosBody");

    if(list.length === 0){
        tbody.innerHTML = '<tr><td colspan="5">No se encontraron contrarecibos</td></tr>';
        return;
    }

    tbody.innerHTML = list.map(c => {
        const facturasHTML = (c.facturas && c.facturas.length)
            ? c.facturas.map(f => `<div class="factura-item">#${escapeHtml(f.numero)} | ${escapeHtml(f.fecha)} | $${Number(f.cantidad).toLocaleString('es-MX',{minimumFractionDigits:2})}</div>`).join('')
            : '<em>Sin facturas</em>';
        return `
        <tr>
            <td>${escapeHtml(c.id)}</td>
            <td>${escapeHtml(c.proveedor)}</td>
            <td>${facturasHTML}</td>
            <td>${escapeHtml(c.fecha)}</td>
            <td>
                <a href="${BASE_URL}index.php?c=Contrarecibos&a=edit&id=${encodeURIComponent(c.id)}" class="btn-icon">✏️</a>
                <a href="${BASE_URL}index.php?c=Contrarecibos&a=delete&id=${encodeURIComponent(c.id)}" class="btn-icon" onclick="return confirm('¿Eliminar este contrarecibo?')">🗑️</a>
                <a href="${BASE_URL}index.php?c=Contrarecibos&a=pdf&id=${encodeURIComponent(c.id)}" class="btn-icon">📄</a>
            </td>
        </tr>`;
    }).join('');
}

// ------------------------------
// FILTRO GLOBAL + RESETEA PAGINACIÓN
// ------------------------------
function doFilterLocal(){
    const term = txtBuscar.value.trim().toLowerCase();
    const tipo = getTipoBusqueda();
    const mes = mesSelect.value;
    const anio = anioSelect.value;

    filteredList = allContrarecibos.filter(c => {
        let match = true;

        if(term){
            if(tipo==='id') match = c._id.includes(term);
            else if(tipo==='proveedor') match = c._proveedor.includes(term);
            else if(tipo==='factura') match = c._facturas.includes(term);
        }

        if(match && mes && c._fechaTs){ 
            match = (new Date(c._fechaTs).getMonth()+1 == mes); 
        }
        if(match && anio && c._fechaTs){ 
            match = (new Date(c._fechaTs).getFullYear() == anio); 
        }

        return match;
    });

    currentPage = 1;   // ← SIEMPRE reinicia a página 1
    renderPagination();
}

// ------------------------------
// AUTOCOMPLETADO
// ------------------------------
const debouncedAutocomplete = debounce(()=>{
    const term = txtBuscar.value.trim().toLowerCase();
    const tipo = getTipoBusqueda();
    if(!term){ datalist.innerHTML=''; return; }

    let items = [];
    if(tipo==='id') items = allContrarecibos.map(c=>c._id).filter(v=>v.includes(term));
    else if(tipo==='proveedor') items = allContrarecibos.map(c=>c._proveedor).filter(v=>v.includes(term));
    else if(tipo==='factura') items = allContrarecibos.map(c=>c._facturas).filter(v=>v.includes(term));

    items = [...new Set(items)].slice(0,10);
    datalist.innerHTML = items.map(i=>`<option value="${escapeHtml(i)}">`).join('');
},200);

// ------------------------------
// EVENTOS
// ------------------------------
txtBuscar.addEventListener('input',debouncedAutocomplete);
txtBuscar.addEventListener('input',debounce(doFilterLocal,200));

for(const r of tipoRadios)
    r.addEventListener('change', ()=>{ txtBuscar.value=''; doFilterLocal(); datalist.innerHTML=''; });

[mesSelect,anioSelect].forEach(s=>s.addEventListener('change', doFilterLocal));

// ------------------------------
// PRIMER RENDER
// ------------------------------
doFilterLocal();
</script>


</body>
</html>

