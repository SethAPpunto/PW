<?php
session_start();
require_once('../orm/dataBase.php');
require_once('../orm/orm.php');
require_once('../orm/libro.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Libros</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/libros.css">

</head>

<body>

    <?php include '../Maestras/Includes/header.php'; ?>
    <?php include '../Maestras/Includes/nav_admin.php'; ?>

    <main>
        <div class="table-container">
            <h3>Libros</h3>
            <div class="table-header-actions">
                <a href="agregar_libro.php" class="btn btn-add">
                    <i class="fas fa-plus"></i> Agregar Libro
                </a>
            </div>

            <?php
            try {
                $db = new Database();
                $encontrado = $db->verificarDriver();

                if ($encontrado) {
                    $cnn = $db->getConnection();
                    $libroModelo = new Libro($cnn);
                    $libros = $libroModelo->getAll();
                } else {
                    throw new Exception("Driver MySQL no encontrado");
                }
            } catch (Exception $e) {
                echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
                $libros = [];
            }
            ?>

            <?php if (!empty($libros)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Editorial</th>
                            <th>Año</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?= htmlspecialchars($libro['libro_id']) ?></td>
                                <td><?= htmlspecialchars($libro['titulo']) ?></td>
                                <td><?= htmlspecialchars($libro['autor']) ?></td>
                                <td><?= htmlspecialchars($libro['editorial']) ?></td>
                                <td><?= htmlspecialchars($libro['anio_publicacion']) ?></td>
                                <td><?= htmlspecialchars($libro['categoria']) ?></td>

                                <td class="action-buttons">
                                    <button class="btn btn-edit" onclick="openEditModal(
                                        '<?= $libro['libro_id'] ?>',
                                        '<?= htmlspecialchars($libro['titulo'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($libro['autor'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($libro['editorial'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($libro['anio_publicacion'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($libro['categoria'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($libro['descripcion'], ENT_QUOTES) ?>',
                                    )">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-delete" onclick="confirmDelete(<?= $libro['libro_id'] ?>)">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No se encontraron libros registrados.</p>
            <?php endif; ?>
        </div>
        <div class="management-button">
            <a href="gestionar_libros.php" class="btn btn-manage">
                <i class="fas fa-book"></i> Gestionar Copias de Libros
            </a>
        </div>
    </main>
    <!-- Modal de Edición -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h2>Editar Libro</h2>
            <form id="editForm" action="actualizar_libro.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editLibroId" name="libro_id">

                <div class="form-group">
                    <label for="editTitulo">Título:</label>
                    <input type="text" id="editTitulo" name="titulo" required>
                </div>

                <div class="form-group">
                    <label for="editAutor">Autor:</label>
                    <input type="text" id="editAutor" name="autor" required>
                </div>

                <div class="form-group">
                    <label for="editEditorial">Editorial:</label>
                    <input type="text" id="editEditorial" name="editorial" required>
                </div>

                <div class="form-group">
                    <label for="editAnio">Año de publicación:</label>
                    <input type="number" id="editAnio" name="anio_publicacion">
                </div>

                <div class="form-group">
                    <label for="editCategoria">Categoría:</label>
                    <select id="editCategoria" name="categoria" required>
                        <option value="Ficción">Ficción</option>
                        <option value="No Ficción">No Ficción</option>
                        <option value="Ciencia">Ciencia</option>
                        <option value="Tecnología">Tecnología</option>
                        <option value="Biografía">Biografía</option>
                        <option value="Historia">Historia</option>
                        <option value="Autoayuda">Autoayuda</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editDescripcion">Descripción:</label>
                    <textarea id="editDescripcion" name="descripcion" rows="4"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Cancelar</button>
                    <button type="button" class="btn btn-save" onclick="submitEditForm()">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar este libro?</p>
            <div class="modal-footer">
                <button type="button" class="btn btn-save" onclick="closeConfirmModal()">No</button>

                <button type="button" class="btn btn-cancel" id="confirmDeleteBtn">Si</button>
            </div>
        </div>
    </div>

    <script>
        // Función para abrir el modal de edición
        function openEditModal(id, titulo, autor, editorial, anio, categoria, descripcion) {
            document.getElementById('editLibroId').value = id;
            document.getElementById('editTitulo').value = titulo;
            document.getElementById('editAutor').value = autor;
            document.getElementById('editEditorial').value = editorial;
            document.getElementById('editAnio').value = anio;
            document.getElementById('editCategoria').value = categoria;
            document.getElementById('editDescripcion').value = descripcion;

            // No llamamos a updatePortadaStatus porque ya no existe ni se usa

            document.getElementById('editModal').style.display = 'block';
        }

        // Función para cerrar el modal de edición
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Función para enviar el formulario de edición
        function submitEditForm() {
            document.getElementById('editForm').submit();
        }

        // Variable global para almacenar el ID a eliminar
        let libroIdToDelete = null;

        // Función para abrir modal de confirmación de eliminación
        function confirmDelete(id) {
            libroIdToDelete = id;
            document.getElementById('confirmModal').style.display = 'block';
        }

        // Función para cerrar el modal de confirmación
        function closeConfirmModal() {
            libroIdToDelete = null;
            document.getElementById('confirmModal').style.display = 'none';
        }

        // Función para eliminar el libro
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (libroIdToDelete) {
                window.location.href = 'eliminar_libro.php?id=' + libroIdToDelete;
            }
        });

        // Cerrar modales si el usuario hace clic fuera del contenido
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