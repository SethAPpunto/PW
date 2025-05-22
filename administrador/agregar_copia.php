<?php
session_start();
require_once('../orm/dataBase.php');
require_once('../orm/orm.php');
require_once('../orm/libro.php');
require_once('../orm/copia_libro.php');

$db = new Database();
$cnn = $db->getConnection();
$libroModelo = new Libro($cnn);
$copiaModelo = new CopiaLibro($cnn);

$libros = $libroModelo->getAll();
$mensaje = '';
$error = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $datos = [
            'libro_id' => $_POST['libro_id'],
            'codigo_copia' => $_POST['codigo_copia'],
            'estado' => $_POST['estado'],
            'fecha_adquisicion' => $_POST['fecha_adquisicion'],
            'ubicacion' => $_POST['ubicacion'],
            'notas' => $_POST['notas']
        ];

        if ($copiaModelo->create($datos)) {
            $_SESSION['mensaje'] = 'Copia de libro agregada exitosamente';
            header('Location: gestionar_libros.php');
            exit;
        } else {
            $error = 'Error al agregar la copia del libro';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Copia de Libro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../Maestras/Includes/header.php'; ?>
<?php include '../Maestras/Includes/nav_admin.php'; ?>

<main>
    <div style="max-width: 700px; margin: 3rem auto; background: #fff; padding: 2rem 2.5rem; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.8rem; color: #222;">📚 Agregar Nueva Copia de Libro</h3>

        <?php if ($error): ?>
            <div style="background: #ffe5e5; color: #c0392b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="agregar_copia.php">
            <div style="margin-bottom: 1.5rem;">
                <label for="libro_id" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Libro:</label>
                <div style="position: relative;">
                    <select id="libro_id" name="libro_id" required style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 0.75rem; border-radius: 8px; border: 1px solid #ccc; background-color: #f9f9f9; font-size: 1rem; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"16\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px;">
                        <option value="">Seleccione un libro</option>
                        <?php foreach ($libros as $libro): ?>
                            <option value="<?= $libro['libro_id'] ?>" <?= isset($_POST['libro_id']) && $_POST['libro_id'] == $libro['libro_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($libro['titulo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="codigo_copia" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Código de copia:</label>
                <input type="text" id="codigo_copia" name="codigo_copia" required value="<?= htmlspecialchars($_POST['codigo_copia'] ?? '') ?>" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem;">
                <small style="color: #777;">Identificador único para esta copia física (ej. LIB-001-01)</small>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="estado" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Estado inicial:</label>
                <select id="estado" name="estado" required style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 0.75rem; border-radius: 8px; border: 1px solid #ccc; background-color: #f9f9f9; font-size: 1rem; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"16\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px;">
                    <option value="disponible" <?= ($_POST['estado'] ?? '') == 'disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="prestado" <?= ($_POST['estado'] ?? '') == 'prestado' ? 'selected' : '' ?>>Prestado</option>
                    <option value="reservado" <?= ($_POST['estado'] ?? '') == 'reservado' ? 'selected' : '' ?>>Reservado</option>
                    <option value="danado" <?= ($_POST['estado'] ?? '') == 'danado' ? 'selected' : '' ?>>Dañado</option>
                    <option value="perdido" <?= ($_POST['estado'] ?? '') == 'perdido' ? 'selected' : '' ?>>Perdido</option>
                    <option value="en_reparacion" <?= ($_POST['estado'] ?? '') == 'en_reparacion' ? 'selected' : '' ?>>En reparación</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="fecha_adquisicion" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Fecha de adquisición:</label>
                <input type="date" id="fecha_adquisicion" name="fecha_adquisicion" value="<?= htmlspecialchars($_POST['fecha_adquisicion'] ?? date('Y-m-d')) ?>" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="ubicacion" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($_POST['ubicacion'] ?? '') ?>" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem;">
                <small style="color: #777;">Ej: Estante 3, Sección A</small>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="notas" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Notas:</label>
                <textarea id="notas" name="notas" rows="3" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem;"><?= htmlspecialchars($_POST['notas'] ?? '') ?></textarea>
                <small style="color: #777;">Información adicional sobre esta copia</small>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" style="flex: 1; padding: 0.75rem; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; background: linear-gradient(to right, #4facfe, #00f2fe); color: white; cursor: pointer; box-shadow: 0 4px 12px rgba(0, 242, 254, 0.3);">
                    💾 Guardar Copia
                </button>
                <a href="gestionar_libros.php" style="flex: 1; text-align: center; padding: 0.75rem; background: #f44336; color: #fff; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 1rem; box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const codigo = document.getElementById('codigo_copia').value.trim();
        const libro = document.getElementById('libro_id').value;
        if (!codigo) {
            alert('El código de copia es requerido');
            e.preventDefault();
            return;
        }
        if (!libro) {
            alert('Debe seleccionar un libro');
            e.preventDefault();
            return;
        }
    });
</script>
</body>
</html>
