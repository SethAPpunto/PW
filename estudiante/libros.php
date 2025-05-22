<?php
session_start();
require_once '../orm/dataBase.php';

class LibrosDisponibles
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function obtenerLibrosPorDisponibilidad($disponibilidad)
    {
        $sql = "SELECT * FROM libros WHERE disponibilidad = :disponibilidad ORDER BY fecha_ingreso DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['disponibilidad' => $disponibilidad]);
        return $stmt->fetchAll();
    }
}

$librosApp = new LibrosDisponibles();
$librosDisponibles = $librosApp->obtenerLibrosPorDisponibilidad('Sí');
$librosNoDisponibles = $librosApp->obtenerLibrosPorDisponibilidad('No');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistema de Gestión</title>
    <link rel="stylesheet" href="../css/index.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        main {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .lista-libros {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-height: 80vh;
            overflow-y: auto;
        }

        .lista-libros h3 {
            margin-top: 0;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 5px;
        }

        .libro-item {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.2s ease;
        }

        .libro-item:last-child {
            border-bottom: none;
        }

        .libro-item:hover {
            background-color: #f9f9f9;
        }

        .libro-item h4 {
            margin: 0 0 6px;
            font-size: 18px;
        }

        .libro-item p {
            margin: 3px 0;
            font-size: 14px;
            color: #555;
        }

        /* Estilo para libros no disponibles */
        .lista-no-disponibles h3 {
            border-color: #f44336;
        }

    </style>
</head>

<body>
    <?php include '../Maestras/Includes/header.php'; ?>
    <?php include '../Maestras/Includes/nav_estudiante.php'; ?>

    <main>
        <section class="lista-libros lista-disponibles">
            <h3>Libros Disponibles</h3>
            <?php if (count($librosDisponibles) > 0): ?>
                <?php foreach ($librosDisponibles as $libro): ?>
                    <div class="libro-item">
                        <h4><?= htmlspecialchars($libro['titulo']) ?></h4>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($libro['autor']) ?></p>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($libro['categoria']) ?></p>
                        <p><strong>Año:</strong> <?= htmlspecialchars($libro['anio_publicacion']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay libros disponibles actualmente.</p>
            <?php endif; ?>
        </section>

        <section class="lista-libros lista-no-disponibles">
            <h3>Libros No Disponibles</h3>
            <?php if (count($librosNoDisponibles) > 0): ?>
                <?php foreach ($librosNoDisponibles as $libro): ?>
                    <div class="libro-item">
                        <h4><?= htmlspecialchars($libro['titulo']) ?></h4>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($libro['autor']) ?></p>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($libro['categoria']) ?></p>
                        <p><strong>Año:</strong> <?= htmlspecialchars($libro['anio_publicacion']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay libros no disponibles.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../Maestras/Includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navItems = document.querySelectorAll('.nav-item');
            const currentPage = window.location.pathname.split('/').pop();

            navItems.forEach(item => {
                const link = item.querySelector('a');
                if (link && link.getAttribute('href') === currentPage) {
                    item.classList.add('active');
                }

                item.addEventListener('click', function () {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>

</html>
