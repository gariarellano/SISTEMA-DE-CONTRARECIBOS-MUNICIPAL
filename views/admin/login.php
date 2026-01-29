<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login Admin</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=1.8">
<style>
.login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f9f9f9;
}

.login-box {
    background: #fff;
    padding: 40px 50px;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    text-align: center;
    width: 420px;
}

.login-box img {
    height: 90px;
    margin-bottom: 15px;
}

.login-box h2 {
    color: #E6007E;
    font-size: 22px;
    margin-bottom: 5px;
}

.login-box h3 {
    color: #444;
    margin-bottom: 25px;
    font-weight: bold;
}

.form-group {
    text-align: left;
    margin-bottom: 20px;
}

.form-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

.form-group input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: #f4f4f4;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 38px;
    cursor: pointer;
}

.btn-login {
    width: 100%;
    background: #E6007E;
    border: none;
    color: white;
    padding: 12px;
    font-size: 16px;
    border-radius: 6px;
    font-weight: bold;
    margin-top: 10px;
    cursor: pointer;
}

.btn-login:hover {
    background: #970656ff;
}
</style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-box">
        <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo">
        <h2>Sistema de Facturas y Contra Recibos</h2>
        <h3>Iniciar Sesión</h3>

        <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST" action="">
            
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" required>
            </div>

            <div class="form-group" style="position:relative;">
                <label>Contraseña:</label>
                <input type="password" name="password" id="password" required>
                <span class="toggle-password" onclick="togglePassword()">👁</span>
            </div>

            <button type="submit" class="btn-login">Ingresar</button>

        </form>
    </div>
</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
