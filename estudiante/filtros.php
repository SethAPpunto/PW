<?php
session_start();
require_once '../orm/dataBase.php';
require_once '../orm/libro.php';

$db = new Database();
$conn = $db->getConnection();

$libro = new libro($conn);

$editoriales = $libro->obtenerValoresUnicos('editorial');
$autores = $libro->obtenerValoresUnicos('autor');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Biblioteca - Filtros con AJAX</title>
    <link rel="stylesheet" href="../css/index.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* Estilos iguales que antes, para mantener diseño */
        main {
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .resultados, .filtros {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-height: 80vh;
            overflow-y: auto;
        }
        .resultados { flex: 3; }
        .filtros { flex: 1; }
        h3 { margin-top: 0; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; }
        .libro-item { padding: 12px 10px; border-bottom: 1px solid #ddd; transition: background-color 0.2s ease; }
        .libro-item:last-child { border-bottom: none; }
        .libro-item:hover { background-color: #f9f9f9; }
        .libro-item h4 { margin: 0 0 6px; font-size: 18px; }
        .libro-item p { margin: 3px 0; font-size: 14px; color: #555; }
        .filtro-group { margin-bottom: 20px; }
        .filtro-group label { display: block; margin-bottom: 6px; font-weight: bold; }
        .filtro-group select, .filtro-group input[type="radio"] { margin-right: 8px; }
    </style>
</head>
<body>
    <?php include '../Maestras/Includes/header.php'; ?>
    <?php include '../Maestras/Includes/nav_estudiante.php'; ?>

    <main>
        <section class="resultados">
            <h3>Libros Disponibles</h3>
            <div id="libros-container">
                <!-- Libros aparecerán aquí -->
            </div>
        </section>

        <aside class="filtros">
            <h3>Filtros de búsqueda</h3>

            <div class="filtro-group">
                <label>Ordenar por:</label>
                <input type="radio" name="filtro" value="fecha" id="filtro-fecha" /> <label for="filtro-fecha">Fecha de publicación</label><br />
                <input type="radio" name="filtro" value="alfabetico" id="filtro-alfabetico" /> <label for="filtro-alfabetico">Orden alfabético</label><br />
                <input type="radio" name="filtro" value="editorial" id="filtro-editorial" /> <label for="filtro-editorial">Editorial</label><br />
                <input type="radio" name="filtro" value="autor" id="filtro-autor" /> <label for="filtro-autor">Autor</label><br />
            </div>

            <div class="filtro-group" id="select-editorial" style="display:none;">
                <label for="editorial">Selecciona Editorial:</label>
                <select id="editorial">
                    <option value="">-- Elige editorial --</option>
                    <?php foreach ($editoriales as $editorial): ?>
                        <option value="<?= htmlspecialchars($editorial) ?>"><?= htmlspecialchars($editorial) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filtro-group" id="select-autor" style="display:none;">
                <label for="autor">Selecciona Autor:</label>
                <select id="autor">
                    <option value="">-- Elige autor --</option>
                    <?php foreach ($autores as $autor): ?>
                        <option value="<?= htmlspecialchars($autor) ?>"><?= htmlspecialchars($autor) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </aside>
    </main>

    <?php include '../Maestras/Includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filtroRadios = document.querySelectorAll('input[name="filtro"]');
            const editorialSelect = document.getElementById('editorial');
            const autorSelect = document.getElementById('autor');
            const contEditorial = document.getElementById('select-editorial');
            const contAutor = document.getElementById('select-autor');
            const librosContainer = document.getElementById('libros-container');

            function cargarLibros(filtro = '', valor = '') {
                librosContainer.innerHTML = '<p>Cargando libros...</p>';

                fetch(`../orm/ajax_libros.php?filtro=${encodeURIComponent(filtro)}&valor=${encodeURIComponent(valor)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            librosContainer.innerHTML = '<p>No se encontraron libros.</p>';
                            return;
                        }

                        let html = '';
                        data.forEach(libro => {
                            html += `
                            <div class="libro-item">
                                <h4>${libro.titulo}</h4>
                                <p><strong>Autor:</strong> ${libro.autor}</p>
                                <p><strong>Categoría:</strong> ${libro.categoria}</p>
                                <p><strong>Año:</strong> ${libro.anio_publicacion}</p>
                            </div>
                            `;
                        });

                        librosContainer.innerHTML = html;
                    })
                    .catch(() => {
                        librosContainer.innerHTML = '<p>Error al cargar los libros.</p>';
                    });
            }

            // Inicial: filtro alfabético
            cargarLibros('alfabetico');

            filtroRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const filtroSeleccionado = this.value;

                    if (filtroSeleccionado === 'editorial') {
                        contEditorial.style.display = 'block';
                        contAutor.style.display = 'none';
                        cargarLibros('editorial', editorialSelect.value);
                    } else if (filtroSeleccionado === 'autor') {
                        contEditorial.style.display = 'none';
                        contAutor.style.display = 'block';
                        cargarLibros('autor', autorSelect.value);
                    } else {
                        contEditorial.style.display = 'none';
                        contAutor.style.display = 'none';
                        cargarLibros(filtroSeleccionado);
                    }
                });
            });

            editorialSelect.addEventListener('change', function () {
                const filtroSeleccionado = document.querySelector('input[name="filtro"]:checked');
                if (filtroSeleccionado && filtroSeleccionado.value === 'editorial') {
                    cargarLibros('editorial', this.value);
                }
            });

            autorSelect.addEventListener('change', function () {
                const filtroSeleccionado = document.querySelector('input[name="filtro"]:checked');
                if (filtroSeleccionado && filtroSeleccionado.value === 'autor') {
                    cargarLibros('autor', this.value);
                }
            });
        });
    </script>
</body>
</html>
