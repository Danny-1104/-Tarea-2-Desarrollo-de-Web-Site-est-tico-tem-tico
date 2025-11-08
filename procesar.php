<?php
// procesar.php
// Archivo para recibir el formulario por POST, sanear datos y guardarlos en un CSV.
// Buenas prácticas: validación mínima, sanitización y escritura segura.

// Forzar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Función simple para sanear texto
function limpiar($valor) {
    return htmlspecialchars(trim($valor), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Recoger y sanear campos
$nombre      = limpiar($_POST['nombre'] ?? '');
$alias       = limpiar($_POST['alias'] ?? '');
$plataforma  = limpiar($_POST['plataforma'] ?? '');
$canal       = limpiar($_POST['canal'] ?? '');
$pais        = limpiar($_POST['pais'] ?? '');
$experiencia = intval($_POST['experiencia'] ?? 0);
$horario     = limpiar($_POST['horario'] ?? '');
$juego       = limpiar($_POST['juego'] ?? '');
$objetivo    = limpiar($_POST['objetivo'] ?? '');
$correo      = filter_var($_POST['correo'] ?? '', FILTER_SANITIZE_EMAIL);

// Validación básica
$errores = [];
if ($nombre === '') { $errores[] = "Nombre requerido."; }
if ($alias === '') { $errores[] = "Alias requerido."; }
if ($canal === '') { $errores[] = "Link del canal requerido."; }
if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) { $errores[] = "Correo inválido."; }

// Si hay errores, mostrar y detener
if (!empty($errores)) {
    echo "<h2>Se encontraron errores:</h2><ul>";
    foreach ($errores as $e) {
        echo "<li>" . $e . "</li>";
    }
    echo "</ul><p><a href='index.html'>&larr; Volver</a></p>";
    exit;
}

// Preparar registro CSV
$archivo = __DIR__ . '/registros.csv';
$linea = [
    date('Y-m-d H:i:s'),
    $nombre,
    $alias,
    $plataforma,
    $canal,
    $pais,
    $experiencia,
    $horario,
    $juego,
    $objetivo,
    $correo
];

// Abrir archivo en modo append (crea si no existe)
$fp = fopen($archivo, 'a');
if ($fp === false) {
    echo "<p>No se pudo abrir el archivo para guardar el registro.</p>";
    exit;
}

// Escribir línea CSV de forma segura
fputcsv($fp, $linea);
fclose($fp);

// Mostrar confirmación al usuario
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Registro recibido - Zona Gamer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body{ font-family: Arial, sans-serif; background:#0b0b12; color:#fff; padding:20px; }
    .box{ background: rgba(0,0,0,0.6); padding:20px; border-radius:8px; border:1px solid #00e5ff; max-width:800px; margin:0 auto; }
    a{ color:#00e5ff; text-decoration:none; font-weight:bold; }
</style>
</head>
<body>
<div class="box">
    <h1>Registro recibido</h1>
    <p>Gracias <strong><?php echo $nombre; ?></strong>, tu solicitud ha sido registrada correctamente.</p>

    <h3>Resumen de los datos enviados</h3>
    <ul>
        <li><strong>Alias:</strong> <?php echo $alias; ?></li>
        <li><strong>Plataforma:</strong> <?php echo $plataforma; ?></li>
        <li><strong>Canal:</strong> <a href="<?php echo $canal; ?>" target="_blank" rel="noopener noreferrer"><?php echo $canal; ?></a></li>
        <li><strong>País:</strong> <?php echo $pais; ?></li>
        <li><strong>Años de experiencia:</strong> <?php echo $experiencia; ?></li>
        <li><strong>Horario:</strong> <?php echo $horario; ?></li>
        <li><strong>Juego principal:</strong> <?php echo $juego; ?></li>
        <li><strong>Objetivo en la comunidad:</strong> <?php echo nl2br($objetivo); ?></li>
        <li><strong>Correo:</strong> <?php echo $correo; ?></li>
    </ul>

    <p><a href="index.html">&larr; Volver al sitio</a></p>
</div>
</body>
</html>
