<?php
session_start();
require_once('../orm/orm.php');
require_once('../orm/dataBase.php');
require_once('../orm/libro.php');  // Asegúrate de que esta clase exista y tenga deleteById

if (isset($_GET['id'])) {
    try {
        $db = new Database();
        $cnn = $db->getConnection();
        $libroModelo = new Libro($cnn);

        $success = $libroModelo->deleteById($_GET['id']);

        if ($success) {
            $_SESSION['mensaje'] = "Libro eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el libro";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

header('Location: libros.php');
exit();
?>
