<?php
require_once '../orm/dataBase.php';
require_once '../orm/libro.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$libro = new libro($conn);

$filtro = $_GET['filtro'] ?? '';
$valor = $_GET['valor'] ?? null;

$libros = $libro->obtenerLibrosFiltrados($filtro, $valor);

echo json_encode($libros);
