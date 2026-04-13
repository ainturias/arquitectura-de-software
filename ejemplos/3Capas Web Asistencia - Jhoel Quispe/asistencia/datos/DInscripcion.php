<?php
require_once __DIR__ . '/../config/Conexion.php';

class DInscripcion {
    private ?int $id_inscripcion = null;
    private ?int $id_estudiante  = null;
    private ?int $id_grupo       = null;
    private ?string $fecha_inscripcion = null;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
        $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // ---------- Setters ----------
    public function setId(int $id): void {
        $this->id_inscripcion = $id;
    }

    public function setIdEstudiante(int $id): void {
        $this->id_estudiante = $id;
    }

    public function setIdGrupo(int $id): void {
        $this->id_grupo = $id;
    }

    public function setFechaInscripcion(?string $fecha): void {
        $this->fecha_inscripcion = $fecha;
    }

    // ---------- Métodos ----------
    /** Listar estudiantes de un grupo */
    public function listarEstudiantesDeGrupo(): array {
        $sql = "SELECT u.id_usuario AS id,
                       u.correo,
                       (u.apellido || ' ' || u.nombre) AS nombre,
                       i.fecha_inscripcion
                FROM inscripcion i
                JOIN usuario u ON u.id_usuario = i.id_estudiante
                WHERE i.id_grupo = ?
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Listar estudiantes que NO están en el grupo */
    public function listarEstudiantesNoAsignados(): array {
        $sql = "SELECT u.id_usuario AS id,
                       (u.apellido || ' ' || u.nombre) AS nombre,
                       u.correo
                FROM usuario u
                WHERE u.tipo_usuario = 'estudiante'
                  AND NOT EXISTS (
                      SELECT 1 FROM inscripcion i
                      WHERE i.id_estudiante = u.id_usuario
                        AND i.id_grupo = ?
                  )
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Verifica si existe ya la inscripción */
    public function existeAsignacion(): bool {
        $sql = "SELECT COUNT(*) FROM inscripcion
                WHERE id_grupo = ? AND id_estudiante = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo, $this->id_estudiante]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Inserta inscripción */
    public function asignarEstudiante(): bool {
        if ($this->fecha_inscripcion) {
            $sql = "INSERT INTO inscripcion (id_estudiante, id_grupo, fecha_inscripcion)
                    VALUES (?, ?, ?)";
            $params = [$this->id_estudiante, $this->id_grupo, $this->fecha_inscripcion];
        } else {
            $sql = "INSERT INTO inscripcion (id_estudiante, id_grupo)
                    VALUES (?, ?)";
            $params = [$this->id_estudiante, $this->id_grupo];
        }
        $stmt = $this->con->prepare($sql);
        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('[DInscripcion asignarEstudiante] ' . $e->getMessage());
            return false;
        }
    }

    /** Elimina inscripción */
    public function quitarEstudiante(): bool {
        $sql = "DELETE FROM inscripcion WHERE id_grupo = ? AND id_estudiante = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo, $this->id_estudiante]);
        return $stmt->rowCount() > 0;
    }
}
