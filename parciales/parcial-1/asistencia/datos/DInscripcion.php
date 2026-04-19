<?php
// Capa de Datos - Inscripcion (Composición de Grupo)
// Maneja el detalle de qué estudiantes pertenecen a un grupo
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

    // Lista los estudiantes que están inscritos en el grupo
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

    // Lista los estudiantes que aún no están inscritos en el grupo
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

    // Inscribe un estudiante en el grupo
    public function inscribir(): bool {
        $sql = "INSERT INTO inscripcion (id_estudiante, id_grupo) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        try {
            return $stmt->execute([$this->id_estudiante, $this->id_grupo]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Quita a un estudiante del grupo
    public function desinscribir(): bool {
        $sql = "DELETE FROM inscripcion WHERE id_grupo=? AND id_estudiante=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo, $this->id_estudiante]);
        return $stmt->rowCount() > 0;
    }

    // Crea un estudiante nuevo y lo inscribe al grupo en un solo paso
    // Se usa cuando se importa un CSV con estudiantes
    public function crearEstudianteEInscribir(string $registro, string $nombre, string $apellido): bool {
        // Primero creamos al estudiante si no existe
        $sqlEst = "INSERT INTO estudiante (nombre, apellido, registro) VALUES (?, ?, ?)
                   ON CONFLICT (registro) DO NOTHING";
        $stmtEst = $this->con->prepare($sqlEst);
        $stmtEst->execute([$nombre, $apellido, $registro]);

        // Obtenemos el id del estudiante (ya existía o se acaba de crear)
        $sqlId = "SELECT id_estudiante FROM estudiante WHERE registro = ?";
        $stmtId = $this->con->prepare($sqlId);
        $stmtId->execute([$registro]);
        $row = $stmtId->fetch(PDO::FETCH_ASSOC);

        if (!$row) return false;

        // Inscribimos al estudiante en el grupo
        $this->id_estudiante = (int)$row['id_estudiante'];
        return $this->inscribir();
    }
}
