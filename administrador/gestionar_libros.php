<?php
session_start();
require_once('../orm/dataBase.php');
require_once('../orm/orm.php');
require_once('../orm/libro.php');
require_once('../orm/copia_libro.php');


try {
    $db = new Database();
    $cnn = $db->getConnection();
    $libroModelo = new Libro($cnn);
    $copiaModelo = new CopiaLibro($cnn);

    $libros = $libroModelo->getAll();

    // Manejar parámetros de filtrado
    $libro_id = $_GET['libro_id'] ?? null;
    $estado = $_GET['estado'] ?? null;

    $copias = $copiaModelo->getAll($libro_id, $estado);

} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    $copias = [];
    $libros = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Copias de Libros</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/libros.css">
</head>

<body>
    <?php include '../Maestras/Includes/header.php'; ?>
    <?php include '../Maestras/Includes/nav_admin.php'; ?>

    <main>
        <div class="table-container">
            <h3>Gestión de Copias de Libros</h3>

            <!-- Filtros -->
            <div class="filters"
                style="background: #ffffff; padding: 2rem; border-radius: 16px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08); max-width: 600px; margin: 2rem auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; border: 1px solid #eaeaea; position: relative;">
                <form method="get">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="libro_id"
                            style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;">Filtrar por
                            libro:</label>
                        <div style="position: relative;">
                            <select id="libro_id" name="libro_id" style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 0.75rem; border-radius: 8px; border: 1px solid #ccc;
                        background-color: #f9f9f9; font-size: 1rem; line-height: 1.5; appearance: none;
                        -webkit-appearance: none; -moz-appearance: none; height: 45px; box-sizing: border-box;
                        background-image: url('data:image/svg+xml;utf8,<svg fill=\" gray\" height=\"16\" viewBox=\"0 0
                                24 24\" width=\"16\" xmlns=\"http://www.w3.org/2000/svg\">
                                <path d=\"M7 10l5 5 5-5z\" /></svg>');
                                background-repeat: no-repeat; background-position: right 10px center; background-size:
                                16px;">
                                <option value="">Todos los libros</option>
                                <?php foreach ($libros as $libro): ?>
                                    <option value="<?= $libro['libro_id'] ?>" <?= $libro_id == $libro['libro_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($libro['titulo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="estado"
                            style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;">Filtrar por
                            estado:</label>
                        <div style="position: relative;">
                            <select id="estado" name="estado" style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 0.75rem; border-radius: 8px; border: 1px solid #ccc;
                        background-color: #f9f9f9; font-size: 1rem; line-height: 1.5; appearance: none;
                        -webkit-appearance: none; -moz-appearance: none; height: 45px; box-sizing: border-box;
                        background-image: url('data:image/svg+xml;utf8,<svg fill=\" gray\" height=\"16\" viewBox=\"0 0
                                24 24\" width=\"16\" xmlns=\"http://www.w3.org/2000/svg\">
                                <path d=\"M7 10l5 5 5-5z\" /></svg>');
                                background-repeat: no-repeat; background-position: right 10px center; background-size:
                                16px;">
                                <option value="">Todos los estados</option>
                                <option value="disponible" <?= $estado == 'disponible' ? 'selected' : '' ?>>Disponible
                                </option>
                                <option value="prestado" <?= $estado == 'prestado' ? 'selected' : '' ?>>Prestado</option>
                                <option value="reservado" <?= $estado == 'reservado' ? 'selected' : '' ?>>Reservado
                                </option>
                                <option value="danado" <?= $estado == 'danado' ? 'selected' : '' ?>>Dañado</option>
                                <option value="perdido" <?= $estado == 'perdido' ? 'selected' : '' ?>>Perdido</option>
                                <option value="en_reparacion" <?= $estado == 'en_reparacion' ? 'selected' : '' ?>>En
                                    reparación</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-filter" style="padding: 0.6rem 1.2rem; font-size: 1rem; font-weight: 600; border-radius: 8px; border: none;
                cursor: pointer; background: linear-gradient(to right, #4facfe, #00f2fe); color: #fff;
                box-shadow: 0 4px 12px rgba(79, 172, 254, 0.4); margin-right: 0.75rem;">
                        Filtrar
                    </button>

                    <a href="gestionar_libros.php" class="btn btn-clear" style="padding: 0.6rem 1.2rem; font-size: 1rem; font-weight: 600; border-radius: 8px; border: none;
                cursor: pointer; background: #f44336; color: #fff; box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4);
                text-decoration: none; display: inline-block;">
                        Limpiar filtros
                    </a>
                </form>
            </div>

            <div class="table-header-actions">
                <a href="agregar_copia.php" class="btn btn-add">
                    <i class="fas fa-plus"></i> Agregar Copia
                </a>
            </div>

            <?php if (!empty($copias)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Ubicación</th>
                            <th>Adquisición</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($copias as $copia): ?>
                            <?php
                            $libro_info = $libroModelo->getById($copia['libro_id']);
                            $estado_class = str_replace('_', '-', $copia['estado']);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($copia['codigo_copia']) ?></td>
                                <td><?= htmlspecialchars($libro_info['titulo']) ?></td>
                                <td><span
                                        class="estado-badge <?= $estado_class ?>"><?= ucfirst(str_replace('_', ' ', $copia['estado'])) ?></span>
                                </td>
                                <td><?= htmlspecialchars($copia['ubicacion']) ?></td>
                                <td><?= htmlspecialchars($copia['fecha_adquisicion']) ?></td>
                                <td class="action-buttons">
                                    <button class="btn btn-edit" onclick="openEditModal(
                                        '<?= $copia['copia_id'] ?>',
                                        '<?= htmlspecialchars($copia['codigo_copia'], ENT_QUOTES) ?>',
                                        '<?= $copia['libro_id'] ?>',
                                        '<?= htmlspecialchars($copia['estado'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($copia['ubicacion'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($copia['fecha_adquisicion'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($copia['notas'], ENT_QUOTES) ?>'
                                    )">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-delete" onclick="confirmDelete(<?= $copia['copia_id'] ?>)">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No se encontraron copias registradas.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal de Edición -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h2>Editar Copia de Libro</h2>
            <form id="editForm" action="actualizar_copia.php" method="POST">
                <input type="hidden" id="editCopiaId" name="copia_id">

                <div class="form-group">
                    <label for="editCodigo">Código de copia:</label>
                    <input type="text" id="editCodigo" name="codigo_copia" required>
                </div>

                <div class="form-group">
                    <label for="editLibroId">Libro:</label>
                    <select id="editLibroId" name="libro_id" required>
                        <?php foreach ($libros as $libro): ?>
                            <option value="<?= $libro['libro_id'] ?>"><?= htmlspecialchars($libro['titulo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editEstado">Estado:</label>
                    <select id="editEstado" name="estado" required>
                        <option value="disponible">Disponible</option>
                        <option value="prestado">Prestado</option>
                        <option value="reservado">Reservado</option>
                        <option value="danado">Dañado</option>
                        <option value="perdido">Perdido</option>
                        <option value="en_reparacion">En reparación</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editUbicacion">Ubicación:</label>
                    <input type="text" id="editUbicacion" name="ubicacion">
                </div>

                <div class="form-group">
                    <label for="editFecha">Fecha adquisición:</label>
                    <input type="date" id="editFecha" name="fecha_adquisicion">
                </div>

                <div class="form-group">
                    <label for="editNotas">Notas:</label>
                    <textarea id="editNotas" name="notas" rows="3"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar esta copia de libro?</p>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closeConfirmModal()">Cancelar</button>
                <button type="button" class="btn btn-delete" id="confirmDeleteBtn">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        // Funciones para manejar los modales (similares a las que ya tienes)
        function openEditModal(id, codigo, libroId, estado, ubicacion, fecha, notas) {
            document.getElementById('editCopiaId').value = id;
            document.getElementById('editCodigo').value = codigo;
            document.getElementById('editLibroId').value = libroId;
            document.getElementById('editEstado').value = estado;
            document.getElementById('editUbicacion').value = ubicacion;
            document.getElementById('editFecha').value = fecha;
            document.getElementById('editNotas').value = notas;

            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        let copiaIdToDelete = null;

        function confirmDelete(id) {
            copiaIdToDelete = id;
            document.getElementById('confirmModal').style.display = 'block';
        }

        function closeConfirmModal() {
            copiaIdToDelete = null;
            document.getElementById('confirmModal').style.display = 'none';
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (copiaIdToDelete) {
                window.location.href = 'eliminar_copia.php?id=' + copiaIdToDelete;
            }
        });

        window.onclick = function (event) {
            const editModal = document.getElementById('editModal');
            const confirmModal = document.getElementById('confirmModal');
            if (event.target == editModal) {
                closeEditModal();
            }
            if (event.target == confirmModal) {
                closeConfirmModal();
            }
        };
    </script>
</body>

</html>