<?php
// Capa de Datos - Inscripcion (parte del CU Transaccional)
require_once __DIR__ . '/../config/Conexion.php';

class DInscripcion {
    private ?int $id_estudiante = null;
    private ?int $id_grupo = null;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setIdEstudiante(int $id): void { $this->id_estudiante = $id; }
    public function setIdGrupo(int $id): void { $this->id_grupo = $id; }

    public function listarEstudiantesDeGrupo(): array {
        $sql = "SELECT e.id_estudiante,
                       (e.apellido || ' ' || e.nombre) AS nombre_completo,
                       e.registro,
                       i.fecha_inscripcion
                FROM inscripcion i
                JOIN estudiante e ON e.id_estudiante = i.id_estudiante
                WHERE i.id_grupo = ?
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarEstudiantesNoInscritos(): array {
        $sql = "SELECT e.id_estudiante,
                       (e.apellido || ' ' || e.nombre) AS nombre_completo,
                       e.registro
                FROM estudiante e
                WHERE NOT EXISTS (
                    SELECT 1 FROM inscripcion i
                    WHERE i.id_estudiante = e.id_estudiante
                      AND i.id_grupo = ?
                )
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inscribir(): bool {
        $sql = "INSERT INTO inscripcion (id_estudiante, id_grupo) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        try {
            return $stmt->execute([$this->id_estudiante, $this->id_grupo]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function desinscribir(): bool {
        $sql = "DELETE FROM inscripcion WHERE id_grupo=? AND id_estudiante=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo, $this->id_estudiante]);
        return $stmt->rowCount() > 0;
    }
}
