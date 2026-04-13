<?php
require_once __DIR__ . '/../config/Conexion.php';

class DMinisterio {
    private ?int $id = null;
    private string $descripcion;
    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function crear(): bool {
        $sql = "INSERT INTO ministerio (descripcion) VALUES (?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->descripcion]);
    }

    public function editar(): bool {
        $sql = "UPDATE ministerio SET descripcion = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->descripcion, $this->id]);
    }

    public function eliminar(): bool {
        $sql = "DELETE FROM ministerio WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->id]);
    }

    public function getMinisterios(): array {
        $sql = "SELECT * FROM ministerio ORDER BY id ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
