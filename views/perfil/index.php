<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <style>
        /* ============================
           Contenedor principal
        ============================= */
        .main-wrapper {
            display: flex;
            justify-content: center;     
            align-items: flex-start;
            gap: 25px;                   
            padding: 40px 20px;
            max-width: 1100px;            
            margin: 0 auto;              
        }
        .main-container {
            background: #ffffff;
            padding: 30px;
            width: 480px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 18px;
            max-width: 320px;
            width: 100%;
            margin: 0 auto 22px auto;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #cfcfcf;
            background: #fff;
            font-size: 14px;
            transition: 0.2s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #d1006c;
            outline: none;
            box-shadow: 0 0 4px rgba(209,0,108,0.3);
        }
        .form-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
        .side-panel {
            width: 220px;                  
            background: #ffffff;
            padding: 18px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-left: -8px;
        }
        .side-panel h3 {
            margin-bottom: 10px;
            font-size: 20px;
            font-weight: 700;
            color: #d1006c;
        }
        .side-panel h4 {
            margin-top: 20px;
            font-size: 16px;
            color: #444;
        }
        .user-box {
            padding: 12px;
            background: #f4f4f4;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-name {
            font-weight: 500;
        }
        .delete-user {
            cursor: pointer;
            font-size: 18px;
            opacity: 0.7;
            transition: 0.2s;
        }
        .delete-user:hover {
            opacity: 1;
            color: #E6007E;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            backdrop-filter: blur(3px);
            background: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            width: 380px;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.2);
            animation: fade .2s ease-in-out;
        }
        @keyframes fade {
            from { transform: translateY(-10px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
        .close-btn {
            float: right;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            color: #777;
        }
        .close-btn:hover {
            color: #000;
        }
        .password-input-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
        }
    </style>
</head>
<body>

<?php
$rolUsuario = 'usuario';
$usuarioActual = '';
if(isset($_SESSION['usuario'])){
    if(is_array($_SESSION['usuario'])){
        $rolUsuario = $_SESSION['usuario']['rol'] ?? 'usuario';
        $usuarioActual = $_SESSION['usuario']['usuario'] ?? '';
    } elseif(is_object($_SESSION['usuario'])){
        $rolUsuario = $_SESSION['usuario']->rol ?? 'usuario';
        $usuarioActual = $_SESSION['usuario']->usuario ?? '';
    }
}
?>

<header class="header">
    <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=index" class="btn-back">Regresar</a>
    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo" class="logo">
    </div>
    <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=logout" class="btn-logout">Cerrar sesión</a>
</header>

<div class="main-wrapper">
    <div class="main-container">

        <h2 class="title">Mi Perfil</h2>

        <?php if($rolUsuario === 'admin'): ?>
            <button type="button" class="btn-action" onclick="openModal()">➕ Añadir Usuario</button>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>index.php?c=Perfil&a=actualizar" method="POST" class="form-container">

            <?php if(isset($_GET['ok'])): ?>
                <p style="color:green;font-weight:bold;">✔ Cambios guardados correctamente</p>
            <?php elseif(isset($_GET['error'])): ?>
                <p style="color:red;font-weight:bold;">✘ Las contraseñas no coinciden</p>
            <?php endif; ?>

            <!-- Usuario -->
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario_nuevo" value="<?php echo htmlspecialchars($usuarioActual, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <!-- Contraseña Actual -->
            <div class="form-group">
                <label>Contraseña Actual</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="current_password"
                        value="<?php echo $_SESSION['usuario']['password_plain'] ?? ''; ?>"
                        readonly
                    >
                    <span class="toggle-password" onclick="togglePassword('current_password')">👁</span>
                </div>
            </div>

            <!-- Nueva Contraseña -->
            <div class="form-group">
                <label>Nueva Contraseña</label>
                <input type="password" name="password" id="new_password" required>
            </div>

            <!-- Confirmar Contraseña -->
            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="password_confirm" id="confirm_password" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-action">Actualizar</button>
                <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=index" class="btn-cancel">Cancelar</a>
            </div>

        </form>
    </div>

    <?php if ($rolUsuario === 'admin'): ?>
<div class="side-panel">
    <h3>Usuarios Registrados</h3>

    <div class="user-box">
        <span class="user-name"><?php echo $usuarioActual; ?></span>
    </div>

    <h4>Otros Usuarios:</h4>

    <?php foreach($usuarios as $user): ?>
        <?php
            $nombre = is_array($user) ? $user['usuario'] : $user->usuario;
            if ($nombre !== $usuarioActual):
        ?>
        <div class="user-box">
            <span class="user-name"><?php echo $nombre; ?></span>
            <span class="delete-user" onclick="eliminarUsuario('<?php echo $nombre; ?>')">🗑️</span>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div>

<!-- Modal Crear Usuario -->
<!-- Modal Crear Usuario -->
<?php if($rolUsuario === 'admin'): ?>
<div id="modalNuevoUsuario" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">✖</span>
        <h3>Crear nuevo usuario</h3>

        <form id="formNuevoUsuario" action="<?php echo BASE_URL; ?>index.php?c=Perfil&a=crearUsuario" method="POST" onsubmit="return validarNuevoUsuario()">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="nuevo_usuario" id="nuevo_usuario" value="<?php echo $_POST['nuevo_usuario'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="nuevo_password" id="pass1" required>
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="nuevo_password_confirm" id="pass2" required>
            </div>

            <p id="errorPass" style="color:red; font-weight:bold; text-align:center; display:none;">
                ✘ Las contraseñas no coinciden
            </p>

            <p id="errorUserExist" style="color:red; font-weight:bold; text-align:center; display:none;">
                ✘ El nombre de usuario ya existe
            </p>

            <div style="text-align:center;">
                <button type="submit" class="btn-action">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
// Mostrar / ocultar contraseña
function togglePassword(id){
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

// Abrir modal
function openModal() {
    document.getElementById('modalNuevoUsuario').style.display = 'flex';
}

// Cerrar modal y limpiar campos/errores
function closeModal() {
    const modal = document.getElementById('modalNuevoUsuario');
    modal.style.display = 'none';

    // Limpiar inputs
    const form = document.getElementById('formNuevoUsuario');
    if(form) form.reset();

    // Ocultar errores
    const errorPass = document.getElementById('errorPass');
    const errorUserExist = document.getElementById('errorUserExist');
    if(errorPass) errorPass.style.display = 'none';
    if(errorUserExist) errorUserExist.style.display = 'none';
}

// Eliminar usuario
function eliminarUsuario(usuario){
    if(confirm("¿Eliminar usuario " + usuario + "?")){
        window.location.href = "index.php?c=Perfil&a=eliminar&usuario=" + usuario;
    }
}

// Validar nuevo usuario
function validarNuevoUsuario(){
    const usuarioInput = document.getElementById('nuevo_usuario').value.trim();
    const p1 = document.getElementById('pass1').value;
    const p2 = document.getElementById('pass2').value;
    const errorPass = document.getElementById('errorPass');
    const errorUserExist = document.getElementById('errorUserExist');

    // Limpiar errores
    errorPass.style.display = 'none';
    errorUserExist.style.display = 'none';

    // Validar contraseñas
    if(p1 !== p2){
        errorPass.style.display = 'block';
        return false;
    }

    // Validación de usuario duplicado (frontend rápido, backend obligatorio)
    const usuariosExistentes = [
        <?php foreach($usuarios as $user){
            $nombre = is_array($user) ? $user['usuario'] : $user->usuario;
            echo "'". addslashes($nombre) ."',";
        } ?>
    ];
    if(usuariosExistentes.includes(usuarioInput)){
        errorUserExist.style.display = 'block';
        return false;
    }

    return true;
}

// Abrir modal si hay error al crear usuario
<?php if(isset($_GET['errorUser'])): ?>
document.getElementById('modalNuevoUsuario').style.display = 'flex';
<?php endif; ?>
</script>

</body>
</html>



