<?php
// Capa de Datos - Detalle de Inscripción (CU Transaccional)
// Maneja los estudiantes inscritos dentro de un lote de inscripción
require_once __DIR__ . '/../config/Conexion.php';

class DDetalleInscripcion
{
    private PDO $con;

    private ?int $id_detalle = null;
    private ?int $id_inscripcion = null;
    private ?int $id_estudiante = null;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setIdDetalle(int $id): void
    {
        $this->id_detalle = $id;
    }

    public function setIdInscripcion(int $id): void
    {
        $this->id_inscripcion = $id;
    }

    public function setIdEstudiante(int $id): void
    {
        $this->id_estudiante = $id;
    }

    // Inscribe un estudiante en el detalle de inscripción
    public function inscribir(): bool
    {
        $sql = "INSERT INTO detalle_inscripcion (id_inscripcion, id_estudiante) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        try {
            return $stmt->execute([$this->id_inscripcion, $this->id_estudiante]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Quita a un estudiante de un grupo (busca en qué detalle está)
    public function desinscribir(int $id_estudiante, int $id_grupo): bool
    {
        $sql = "DELETE FROM detalle_inscripcion
                WHERE id_estudiante = ?
                AND id_inscripcion IN (
                    SELECT id_inscripcion FROM inscripcion WHERE id_grupo = ?
                )";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id_estudiante, $id_grupo]);
        return $stmt->rowCount() > 0;
    }

    // Lista los estudiantes inscritos en un grupo
    public function listarEstudiantesDeGrupo(int $id_grupo): array
    {
        $sql = "SELECT DISTINCT e.id_estudiante,
                       e.apellido,
                       e.nombre,
                       (e.apellido || ' ' || e.nombre) AS nombre_completo,
                       e.registro
                FROM detalle_inscripcion di
                JOIN inscripcion i ON di.id_inscripcion = i.id_inscripcion
                JOIN estudiante e ON di.id_estudiante = e.id_estudiante
                WHERE i.id_grupo = ?
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id_grupo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
