<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "index.php?c=Admin&a=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/stylecss?v=1.8">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: white;
            text-align: center;
        }
        header {
            background: #c0c0c0;
            padding: 10px;
            font-weight: bold;
            display: flex;
            justify-content: space-between; 
            align-items: center;
        }
        header .user {
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        header .user::before {
            content: "👤";
            margin-right: 5px;
        }
        header .logout {
            background: #E6007E;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        header .logout:hover {
            background: #E6007E;
        }
        .banner {
            background: #E6007E;
            color: white;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
        }
        .content {
            position: relative;
            padding: 60px 20px;
            min-height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .content img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 500px;
            opacity: 0.07;
            transform: translate(-50%, -50%);
            z-index: 0;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 1000px;
            z-index: 1;
            position: relative;
        }
        .column {
            display: flex;
            flex-direction: column;
            gap: 40px; /* separación vertical */
        }
        .btn {
            background: #ee2292f5;
            color: black;
            font-weight: bold;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            min-width: 180px;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }
        .btn:hover {
            background: #f052a9db;
            color: black;
        }
    </style>
</head>
<body>
    <header>
        <span class="user"><?php echo $_SESSION['usuario']['usuario']; ?></span>
        <a href="<?php echo BASE_URL; ?>index.php?c=Admin&a=logout" class="logout">Cerrar sesión</a>
    </header>

    <div class="banner">
        Bienvenido al panel de administración de facturas
    </div>

    <div class="content">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo">
        <div class="buttons">
            <!-- Columna izquierda -->
            <div class="column">
                <a href="<?php echo BASE_URL; ?>index.php?c=Perfil&a=index" class="btn">Perfil</a>
                <a href="<?php echo BASE_URL; ?>index.php?c=Proveedores&a=index" class="btn">Proveedores</a>
            </div>
            <!-- Columna derecha -->
            <div class="column">
                <a href="<?php echo BASE_URL; ?>index.php?c=Facturas&a=index" class="btn">Facturas</a>
                <a href="<?php echo BASE_URL; ?>index.php?c=Contrarecibos&a=index" class="btn">Contra recibos</a>
            </div>
        </div>
        <?php if (
    isset($_SESSION['usuario']['rol']) &&
    $_SESSION['usuario']['rol'] === 'admin'
): ?>
    <div style="position: absolute; bottom: 40px; width: 100%; text-align: center;">
        <a href="<?php echo BASE_URL; ?>index.php?c=reportes&a=index" class="btn">
            Reportes del sistema
        </a>
    </div>
<?php endif; ?>

    </div>

    

</body>
</html>


