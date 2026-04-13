<?php
// Capa de Datos - Materia
require_once __DIR__ . '/../config/Conexion.php';

class DMateria {
    private ?int $id = null;
    private string $sigla;
    private string $nombre_materia;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setSigla(string $sigla): void {
        $this->sigla = $sigla;
    }

    public function setNombreMateria(string $nombre): void {
        $this->nombre_materia = $nombre;
    }

    // Operaciones CRUD
    public function crear(): bool {
        $sql = "INSERT INTO materia (sigla, nombre_materia) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->sigla, $this->nombre_materia]);
    }

    public function editar(): bool {
        $sql = "UPDATE materia SET sigla=?, nombre_materia=? WHERE id_materia=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->sigla, $this->nombre_materia, $this->id]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool {
        $sql = "DELETE FROM materia WHERE id_materia=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }

    public function listar(): array {
        $sql = "SELECT * FROM materia ORDER BY id_materia ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeSigla(string $sigla): bool {
        $sql = "SELECT COUNT(*) FROM materia WHERE sigla=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$sigla]);
        return $stmt->fetchColumn() > 0;
    }
}
