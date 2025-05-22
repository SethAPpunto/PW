<?php
class CopiaLibro {
    private $cnn;
    
    public function __construct($cnn) {
        $this->cnn = $cnn;
    }
    
    public function getAll($libro_id = null, $estado = null) {
        $query = "SELECT * FROM copias_libros WHERE 1=1";
        $params = [];
        
        if ($libro_id) {
            $query .= " AND libro_id = ?";
            $params[] = $libro_id;
        }
        
        if ($estado) {
            $query .= " AND estado = ?";
            $params[] = $estado;
        }
        
        $stmt = $this->cnn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->cnn->prepare("SELECT * FROM copias_libros WHERE copia_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->cnn->prepare("INSERT INTO copias_libros 
            (libro_id, codigo_copia, estado, fecha_adquisicion, ubicacion, notas) 
            VALUES (?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $data['libro_id'],
            $data['codigo_copia'],
            $data['estado'],
            $data['fecha_adquisicion'],
            $data['ubicacion'],
            $data['notas']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->cnn->prepare("UPDATE copias_libros SET 
            libro_id = ?,
            codigo_copia = ?,
            estado = ?,
            fecha_adquisicion = ?,
            ubicacion = ?,
            notas = ?
            WHERE copia_id = ?");
        
        return $stmt->execute([
            $data['libro_id'],
            $data['codigo_copia'],
            $data['estado'],
            $data['fecha_adquisicion'],
            $data['ubicacion'],
            $data['notas'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->cnn->prepare("DELETE FROM copias_libros WHERE copia_id = ?");
        return $stmt->execute([$id]);
    }
}
?>