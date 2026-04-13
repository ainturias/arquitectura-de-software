<?php
// File: datos/DEstudiante.php
require_once __DIR__ . '/../config/Conexion.php';

class DEstudiante
{

    private ?int $id_usuario = null;
    private string $nombre;
    private string $apellido;
    private string $correo;
    private ?string $registro = null;
    private string $tipo_usuario = 'estudiante';
    private string $password;

    private PDO $con;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }


        // --- Setters ---
    public function setId(int $id): void { $this->id_usuario = $id; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setApellido(string $apellido): void { $this->apellido = $apellido; }
    public function setCorreo(string $correo): void { $this->correo = $correo; }
    public function setRegistro(?string $registro): void { $this->registro = $registro; }
    public function setTipoUsuario(string $tipo): void { $this->tipo_usuario = $tipo; }
    public function setPassword(string $password): void { $this->password = $password; }


    // Crear (tipo_usuario = 'estudiante')
    public function crear(): bool
    {
        $sql = "INSERT INTO usuario (nombre, apellido, correo, registro, tipo_usuario, password)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([
                $this->nombre,
                $this->apellido,
                $this->correo,
                $this->registro,
                $this->tipo_usuario,
                $this->password
        ]);

    }

    // Editar: si password viene NULL, no tocarla
    public function editar(): bool
    {

        $sql = "UPDATE usuario
                    SET nombre = ?, apellido = ?, correo = ?, registro = ?,tipo_usuario=?, password = ?
                    WHERE id_usuario = ? AND tipo_usuario = 'estudiante'";
        
        $stmt = $this->con->prepare($sql);

        $stmt->execute([
            $this->nombre,
            $this->apellido,
            $this->correo,
            $this->registro,
            $this->tipo_usuario, // se mantiene estudiante
            $this->password,
            $this->id_usuario
        ]);
        return $stmt->rowCount() > 0;

    }

    public function eliminar(): bool
    {
        $sql = "DELETE FROM usuario WHERE id_usuario = ? AND tipo_usuario = 'estudiante'";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_usuario]);
        return $stmt->rowCount() > 0;

    }

    // Lista solo estudiantes
    public function listar(): array
    {
        $sql = "SELECT id_usuario, nombre, apellido, correo, registro
                FROM usuario
                WHERE tipo_usuario = 'estudiante'
                ORDER BY apellido, nombre";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
