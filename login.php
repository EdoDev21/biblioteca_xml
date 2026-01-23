<?php
session_start();

require_once 'auth_ad.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $resultado = autenticarUsuarioAD($user, $pass);

    if ($resultado['success']) {
        $_SESSION['usuario_ad'] = $user;
        $_SESSION['rol'] = 'admin';
        
        header('Location: admin.php');
        exit;
    } else {
        $error = $resultado['msg'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrativo</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .login-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .error-msg { color: red; margin-bottom: 1rem; text-align: center; font-size: 0.9rem; }
        .dev-badge { background: #ffc107; color: black; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">Acceso Administrativo</h2>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Usuario (Active Directory)</label>
                <input type="text" name="username" class="form-control" required autofocus placeholder="Ej. administrador">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary full-width">Ingresar</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <a href="index.php" style="color: #666; font-size: 0.9rem;">← Volver al Catálogo Público</a>
        </div>
    </div>
</body>
</html>