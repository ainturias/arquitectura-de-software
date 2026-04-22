<?php
// Capa de Datos - Estudiante
require_once __DIR__ . '/../config/Conexion.php';

class DEstudiante
{
    private PDO $con;

    private ?int $id_estudiante = null;
    private string $nombre;
    private string $apellido;
    private string $registro;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // Funciones Setters
    public function setId(int $id): void
    {
        $this->id_estudiante = $id;
    }
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
    public function setApellido(string $apellido): void
    {
        $this->apellido = $apellido;
    }
    public function setRegistro(string $registro): void
    {
        $this->registro = $registro;
    }

    // Funciones CRUD
    public function crear(): bool
    {
        $sql = "INSERT INTO estudiante (nombre, apellido, registro) VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->nombre, $this->apellido, $this->registro]);
    }

    public function editar(): bool
    {
        $sql = "UPDATE estudiante SET nombre=?, apellido=?, registro=? WHERE id_estudiante=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->nombre, $this->apellido, $this->registro, $this->id_estudiante]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool
    {
        $sql = "DELETE FROM estudiante WHERE id_estudiante=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_estudiante]);
        return $stmt->rowCount() > 0;
    }

    public function listar(): array
    {
        $sql = "SELECT * FROM estudiante ORDER BY apellido, nombre";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeRegistro(string $registro): bool
    {
        $sql = "SELECT COUNT(*) FROM estudiante WHERE registro=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$registro]);
        return $stmt->fetchColumn() > 0;
    }

    // Lista los estudiantes que NO están inscritos en un grupo
    // Usado por PInscripcion para llenar el combo de estudiantes
    public function listarNoInscritosEnGrupo(int $id_grupo): array
    {
        $sql = "SELECT e.id_estudiante,
                       (e.apellido || ' ' || e.nombre) AS nombre_completo,
                       e.registro
                FROM estudiante e
                WHERE NOT EXISTS (
                    SELECT 1 FROM detalle_inscripcion di
                    JOIN inscripcion i ON di.id_inscripcion = i.id_inscripcion
                    WHERE di.id_estudiante = e.id_estudiante
                    AND i.id_grupo = ?
                )
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id_grupo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
