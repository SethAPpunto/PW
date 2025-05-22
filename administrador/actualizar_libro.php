<?php
session_start();
require_once('../orm/orm.php');
require_once('../orm/dataBase.php');
require_once('../orm/libro.php'); // Asegúrate que esta clase exista

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new Database();
        $cnn = $db->getConnection();
        $libroModelo = new Libro($cnn);

        $data = [
            'titulo' => $_POST['titulo'],
            'autor' => $_POST['autor'],
            'editorial' => $_POST['editorial'],
            'anio_publicacion' => $_POST['anio_publicacion'],
            'categoria' => $_POST['categoria'],
            'descripcion' => $_POST['descripcion'],
            // Si quieres actualizar disponibilidad:
            'disponibilidad' => $_POST['disponibilidad'] ?? 'Sí',
            // Si tienes campo portada, puedes agregar también
            //'portada' => $_POST['portada'] ?? null,
        ];

        $success = $libroModelo->update($_POST['libro_id'], $data);

        if ($success) {
            $_SESSION['mensaje'] = "Libro actualizado correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar el libro";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header('Location: libros.php');
    exit();
}
?>
