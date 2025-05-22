<?php
session_start(); // Asegúrate de iniciar la sesión si vas a usar $_SESSION
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include '../Maestras/Includes/header.php'; ?>
    <?php include '../Maestras/Includes/nav_estudiante.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <section class="welcome-section">
            <h2>Biblioteca Lumina</h2>
            <p>Bienvenido, aquí encontraras una amplia variedad</p>
            <p>de libros, esperemos te guste nuestra biblioteca.</p>
        </section>
    </main>

    <?php include '../Maestras/Includes/footer.php'; ?>

    <script>
        // Activar elementos del menú según la página actual
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            const currentPage = window.location.pathname.split('/').pop();
            
            navItems.forEach(item => {
                const link = item.querySelector('a');
                if (link && link.getAttribute('href') === currentPage) {
                    item.classList.add('active');
                }
                
                item.addEventListener('click', function() {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>

</html>