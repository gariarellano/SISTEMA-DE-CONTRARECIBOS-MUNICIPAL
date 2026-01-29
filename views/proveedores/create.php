<?php 
if(session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Proveedor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?">
</head>
<body>
<header class="header">
    <a href="<?php echo BASE_URL; ?>index.php?c=Proveedores&a=index" class="btn-back">Regresar</a>
    
    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo" class="logo">
    </div>
    <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=logout" class="btn-logout">Cerrar sesión</a>
</header>

<main class="main-container">
    <h2 class="title">Agregar Proveedor</h2>

    <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=Proveedores&a=create" class="form-container">

        <label>RFC:</label>
        <input type="text" id="rfc" name="rfc" placeholder="RFC" required maxlength="13">

        <label>Nombre:</label>
        <input type="text" name="nombre" placeholder="Nombre" required>

        <label>Domicilio:</label>
        <input type="text" name="domicilio" placeholder="Domicilio">

        <label>Teléfono:</label>
        <input type="text" id="telefono" name="telefono" placeholder="Teléfono" maxlength="10">

        <label>Email:</label>
        <input type="email" id="email" name="email" placeholder="Email">

        <label>Fondo:</label>
        <select name="fondo" required style="width: 500px; padding:8px; font-size:16px, border-radius: 16px, color: #333;">
            <option value="">Selecciona un fondo</option>
            <option value="1">Fondo III</option>
            <option value="2">Obra convenida</option>
            <option value="3">Gasto corriente</option>
            <option value="4">Aporte Federal</option>
            <option value="5">Otras aportaciones</option>
        </select>

        <div class="form-buttons">
            <button type="submit" class="btn-action">Guardar</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=index" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</main>

<script>
const rfcInput = document.getElementById('rfc');
const telefonoInput = document.getElementById('telefono');
const emailInput = document.getElementById('email');

// RFC: solo letras y números, mayúsculas, max 13
rfcInput.addEventListener('input', () => {
    rfcInput.value = rfcInput.value.replace(/[^A-Za-z0-9]/g,'').substring(0,13).toUpperCase();
});

// Teléfono: solo números, max 10 dígitos
telefonoInput.addEventListener('input', () => {
    telefonoInput.value = telefonoInput.value.replace(/\D/g,'').substring(0,10);
});

// Email: caracteres válidos y validación básica
emailInput.addEventListener('input', () => {
    emailInput.value = emailInput.value.replace(/[^a-zA-Z0-9@._-]/g,'');
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    emailInput.style.borderColor = (emailInput.value && !regex.test(emailInput.value)) ? 'red' : '#ccc';
});
</script>
</body>
</html>
