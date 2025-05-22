<?php
require_once 'orm.php';
class libro extends Orm {

    function __construct(PDO $connection) {
        parent::__construct('libro_id', 'libros', $connection);
    }
    
    /**
     * Obtiene el conteo total de libros
     */
    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Obtiene libros por categoría con conteo
     */
    public function getCountByCategory() {
        $sql = "SELECT categoria, COUNT(*) as total 
                FROM {$this->table} 
                GROUP BY categoria 
                ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene los últimos libros añadidos
     */
    public function getLastAdded($limit = 5) {
        $sql = "SELECT libro_id, titulo, autor, editorial, anio_publicacion, fecha_ingreso 
                FROM {$this->table} 
                ORDER BY fecha_ingreso DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene los libros más antiguos por año de publicación
     */
    public function getOldest($limit = 5) {
        $sql = "SELECT libro_id, titulo, autor, editorial, anio_publicacion 
                FROM {$this->table} 
                WHERE anio_publicacion IS NOT NULL
                ORDER BY anio_publicacion ASC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene conteo de libros por editorial
     */
    public function getCountByEditorial() {
        $sql = "SELECT editorial, COUNT(*) as total 
                FROM {$this->table} 
                GROUP BY editorial 
                ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca libros por término (título, autor o editorial)
     */
    public function search($term) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE titulo LIKE :term 
                OR autor LIKE :term 
                OR editorial LIKE :term 
                ORDER BY titulo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':term', "%$term%");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene libros por categoría específica
     */
    public function getByCategory($category) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE categoria = :category 
                ORDER BY titulo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene estadísticas avanzadas de préstamos (si tuvieras tabla préstamos)
     */
    public function getLoanStatistics() {
        // Esto requeriría que tengas una tabla de préstamos relacionada
        $sql = "SELECT l.libro_id, l.titulo, COUNT(p.prestamo_id) as total_prestamos
                FROM {$this->table} l
                LEFT JOIN prestamos p ON l.libro_id = p.libro_id
                GROUP BY l.libro_id
                ORDER BY total_prestamos DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerValoresUnicos($campo) {
    $allowed = ['editorial', 'autor', 'categoria']; // campos permitidos para filtro
    if (!in_array($campo, $allowed)) {
        return [];
    }
    $sql = "SELECT DISTINCT $campo FROM {$this->table} ORDER BY $campo ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

public function obtenerLibrosFiltrados($filtro, $valor = null) {
    $sql = "SELECT * FROM {$this->table} WHERE disponibilidad = 'Sí' ";
    $params = [];

    switch ($filtro) {
        case 'fecha':
            $sql .= " ORDER BY anio_publicacion DESC";
            break;
        case 'alfabetico':
            $sql .= " ORDER BY titulo ASC";
            break;
        case 'editorial':
            if ($valor) {
                $sql .= " AND editorial = :valor ORDER BY titulo ASC";
                $params[':valor'] = $valor;
            } else {
                $sql .= " ORDER BY titulo ASC";
            }
            break;
        case 'autor':
            if ($valor) {
                $sql .= " AND autor = :valor ORDER BY titulo ASC";
                $params[':valor'] = $valor;
            } else {
                $sql .= " ORDER BY titulo ASC";
            }
            break;
        default:
            $sql .= " ORDER BY titulo ASC";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>