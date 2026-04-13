<?php
require_once __DIR__ . '/../config/Conexion.php';

class DBautismo {
    private ?int $id = null;
    private string $fecha;
    private string $lugar;
    private int $id_miembro;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setFecha(string $fecha): void {
        $this->fecha = $fecha;
    }

    public function setLugar(string $lugar): void {
        $this->lugar = $lugar;
    }

    public function setIdMiembro(int $idMiembro): void {
        $this->id_miembro = $idMiembro;
    }

    // Operaciones
    public function crear(): bool {
        $sql = "INSERT INTO bautismo (fecha, lugar, id_miembro) VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->fecha, $this->lugar, $this->id_miembro]);
    }
    

    public function editar(): bool {
        $sql = "UPDATE bautismo SET fecha = ?, lugar = ?, id_miembro = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->fecha, $this->lugar, $this->id_miembro, $this->id]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool {
        $sql = "DELETE FROM bautismo WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }

    public function getBautismos(): array {
        $sql = "SELECT b.id, b.fecha, b.lugar, b.id_miembro, m.nombre AS nombre_miembro
                FROM bautismo b
                JOIN miembro m ON b.id_miembro = m.id
                ORDER BY b.id ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeBautismoParaMiembro(int $idMiembro): bool {
        $sql = "SELECT COUNT(*) FROM bautismo WHERE id_miembro = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$idMiembro]);
        return $stmt->fetchColumn() > 0;
    }
    
}
