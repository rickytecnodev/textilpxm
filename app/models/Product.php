<?php
/**
 * Modelo de Producto
 * Gestiona las operaciones de productos en la base de datos
 */

class Product extends Model {
    private $table = 'products';

    /**
     * Orden por defecto: mayor a menor (ID/orden más alto primero) para que los más recientes aparezcan primero
     */
    private function orderByOrden() {
        return " ORDER BY COALESCE(orden, id) DESC, id DESC";
    }

    /**
     * Obtener todos los productos
     */
    public function getAll($activo = null) {
        if ($activo !== null) {
            $sql = "SELECT * FROM " . $this->table . " WHERE activo = ?" . $this->orderByOrden();
            return $this->fetchAll($sql, [$activo]);
        }
        $sql = "SELECT * FROM " . $this->table . $this->orderByOrden();
        return $this->fetchAll($sql);
    }

    /**
     * Obtener productos activos (para catálogo público)
     */
    public function getActive() {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1" . $this->orderByOrden();
        return $this->fetchAll($sql);
    }

    /**
     * Obtener productos de portada (para home)
     */
    public function getPortada() {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1 AND portada = 1" . $this->orderByOrden();
        return $this->fetchAll($sql);
    }

    /**
     * Obtener un producto por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Obtener productos por categoría
     */
    public function getByCategory($categoria, $activo = 1) {
        $sql = "SELECT * FROM " . $this->table . " WHERE categoria = ? AND activo = ?" . $this->orderByOrden();
        return $this->fetchAll($sql, [$categoria, $activo]);
    }

    /**
     * Obtener todas las categorías únicas (solo productos activos)
     * @return array Array de nombres de categorías
     */
    public function getCategories() {
        try {
            $sql = "SELECT DISTINCT categoria FROM " . $this->table . " WHERE activo = 1 ORDER BY categoria";
            $result = $this->fetchAll($sql);
            
            // Extraer solo los nombres de las categorías
            $categories = [];
            foreach ($result as $row) {
                if (isset($row['categoria']) && !empty($row['categoria'])) {
                    $categories[] = $row['categoria'];
                }
            }
            
            return $categories;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener todas las categorías únicas (todos los productos, para admin)
     * @return array Array de nombres de categorías
     */
    public function getCategoriesAll() {
        try {
            $sql = "SELECT DISTINCT categoria FROM " . $this->table . " WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria";
            $result = $this->fetchAll($sql);
            $categories = [];
            foreach ($result as $row) {
                $categories[] = $row['categoria'];
            }
            return $categories;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Agrupar productos por categoría
     * @param array $products Array de productos
     * @return array Array asociativo [categoria => [productos]]
     */
    public function groupByCategory($products) {
        $grouped = [];
        
        foreach ($products as $product) {
            if (!isset($product['categoria']) || empty($product['categoria'])) {
                continue;
            }
            
            $categoria = $product['categoria'];
            if (!isset($grouped[$categoria])) {
                $grouped[$categoria] = [];
            }
            
            $grouped[$categoria][] = $product;
        }
        
        return $grouped;
    }

    /**
     * Crear un nuevo producto
     */
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (nombre, descripcion, categoria, precio, stock, imagen_url, activo, portada, tallas_disponibles) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->query($sql, [
            $data['nombre'],
            $data['descripcion'] ?? '',
            $data['categoria'],
            $data['precio'],
            $data['stock'] ?? 0,
            $data['imagen_url'] ?? '',
            $data['activo'] ?? 1,
            $data['portada'] ?? 0,
            $data['tallas_disponibles'] ?? ''
        ]);

        $id = $this->lastInsertId();
        if ($id) {
            $this->query("UPDATE " . $this->table . " SET orden = ? WHERE id = ?", [$id, $id]);
        }
        return $id;
    }

    /**
     * Actualizar un producto
     */
    public function update($id, $data) {
        $sql = "UPDATE " . $this->table . " SET 
                nombre = ?, 
                descripcion = ?, 
                categoria = ?, 
                precio = ?, 
                stock = ?, 
                imagen_url = ?, 
                activo = ?,
                portada = ?,
                tallas_disponibles = ?
                WHERE id = ?";
        
        $stmt = $this->query($sql, [
            $data['nombre'],
            $data['descripcion'] ?? '',
            $data['categoria'],
            $data['precio'],
            $data['stock'] ?? 0,
            $data['imagen_url'] ?? '',
            $data['activo'] ?? 1,
            $data['portada'] ?? 0,
            $data['tallas_disponibles'] ?? '',
            $id
        ]);

        return $this->rowCount($stmt);
    }

    /**
     * Eliminar un producto (soft delete)
     */
    public function delete($id) {
        $sql = "UPDATE " . $this->table . " SET activo = 0 WHERE id = ?";
        $stmt = $this->query($sql, [$id]);
        return $this->rowCount($stmt);
    }

    /**
     * Eliminar un producto permanentemente
     */
    public function deletePermanent($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->query($sql, [$id]);
        return $this->rowCount($stmt);
    }

    /**
     * Actualizar stock
     */
    public function updateStock($id, $stock) {
        $sql = "UPDATE " . $this->table . " SET stock = ? WHERE id = ?";
        $stmt = $this->query($sql, [$stock, $id]);
        return $this->rowCount($stmt);
    }

    /**
     * Contar productos totales
     */
    public function countAll($activo = null) {
        if ($activo !== null) {
            $sql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE activo = ?";
            $result = $this->fetchOne($sql, [$activo]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM " . $this->table;
            $result = $this->fetchOne($sql);
        }
        return $result['total'] ?? 0;
    }

    /**
     * Buscar productos por nombre o descripción
     * @param string $searchTerm Término de búsqueda
     * @return array Array de productos que coinciden
     */
    public function search($searchTerm) {
        if (empty(trim($searchTerm))) {
            return [];
        }
        
        $searchTerm = '%' . trim($searchTerm) . '%';
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE activo = 1 
                AND (nombre LIKE ? OR descripcion LIKE ?)" . $this->orderByOrden();
        
        return $this->fetchAll($sql, [$searchTerm, $searchTerm]);
    }

    /**
     * Valor de orden usado para mostrar (COALESCE(orden, id))
     */
    public function getOrdenValue($id) {
        $sql = "SELECT COALESCE(orden, id) AS val FROM " . $this->table . " WHERE id = ?";
        $row = $this->fetchOne($sql, [$id]);
        return $row ? (int) $row['val'] : null;
    }

    /**
     * ID del producto con orden inmediatamente menor (más abajo en la lista DESC)
     */
    public function getPreviousByOrden($id) {
        $val = $this->getOrdenValue($id);
        if ($val === null) return null;
        $sql = "SELECT id FROM " . $this->table . " 
                WHERE COALESCE(orden, id) < ? 
                ORDER BY COALESCE(orden, id) DESC 
                LIMIT 1";
        $row = $this->fetchOne($sql, [$val]);
        return $row ? (int) $row['id'] : null;
    }

    /**
     * ID del producto con orden inmediatamente mayor (más arriba en la lista DESC)
     */
    public function getNextByOrden($id) {
        $val = $this->getOrdenValue($id);
        if ($val === null) return null;
        $sql = "SELECT id FROM " . $this->table . " 
                WHERE COALESCE(orden, id) > ? 
                ORDER BY COALESCE(orden, id) ASC 
                LIMIT 1";
        $row = $this->fetchOne($sql, [$val]);
        return $row ? (int) $row['id'] : null;
    }

    /**
     * Intercambiar el orden entre dos productos (por id).
     * Se intercambian los valores de visualización (COALESCE(orden, id)).
     */
    public function swapOrden($id1, $id2) {
        $row1 = $this->fetchOne("SELECT id, orden FROM " . $this->table . " WHERE id = ?", [$id1]);
        $row2 = $this->fetchOne("SELECT id, orden FROM " . $this->table . " WHERE id = ?", [$id2]);
        if (!$row1 || !$row2) return false;
        $display1 = $row1['orden'] !== null ? (int) $row1['orden'] : (int) $row1['id'];
        $display2 = $row2['orden'] !== null ? (int) $row2['orden'] : (int) $row2['id'];
        $this->query("UPDATE " . $this->table . " SET orden = ? WHERE id = ?", [$display2, $id1]);
        $this->query("UPDATE " . $this->table . " SET orden = ? WHERE id = ?", [$display1, $id2]);
        return true;
    }

    /**
     * Mover producto una posición hacia arriba (en lista DESC = intercambiar con el de orden mayor)
     */
    public function subirOrden($id) {
        $nextId = $this->getNextByOrden($id);
        if ($nextId === null) return false;
        return $this->swapOrden($id, $nextId);
    }

    /**
     * Mover producto una posición hacia abajo (en lista DESC = intercambiar con el de orden menor)
     */
    public function bajarOrden($id) {
        $prevId = $this->getPreviousByOrden($id);
        if ($prevId === null) return false;
        return $this->swapOrden($id, $prevId);
    }
}
