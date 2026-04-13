<?php
// File: negocio/NEstudiante.php
require_once __DIR__ . '/../datos/DEstudiante.php';
require_once __DIR__ . '/../datos/DAsistencia.php';

class NEstudiante
{
    private DEstudiante $datosEstudiante;
    private DAsistencia $dAsistencia;

    public function __construct()
    {
        $this->datosEstudiante = new DEstudiante();
        $this->dAsistencia = new DAsistencia();
    }

    // CRUD
    public function crear(string $nombre, string $apellido, string $correo, ?string $registro, string $password): bool
    {
        if ($nombre === '' || $apellido === '' || $correo === '' || $password === ''){
            return false;
        }
        
        $this->datosEstudiante->setNombre($nombre);
        $this->datosEstudiante->setApellido($apellido);
        $this->datosEstudiante->setCorreo($correo);
        $this->datosEstudiante->setRegistro($registro);
        $this->datosEstudiante->setTipoUsuario('estudiante');
        $this->datosEstudiante->setPassword($password);

        return $this->datosEstudiante->crear();

    }

    public function editar(int $id, string $nombre, string $apellido, string $correo, ?string $registro, ?string $password): bool
    {
        if ($id <= 0 || $nombre === '' || $apellido === '' || $correo === '') return false;

        $this->datosEstudiante->setId($id);
        $this->datosEstudiante->setNombre($nombre);
        $this->datosEstudiante->setApellido($apellido);
        $this->datosEstudiante->setCorreo($correo);
        $this->datosEstudiante->setRegistro($registro);
        $this->datosEstudiante->setTipoUsuario('estudiante');
        $this->datosEstudiante->setPassword($password);
        return $this->datosEstudiante->editar();

    }

    public function eliminar(int $id): bool
    {
        if ($id <= 0) return false;
        $this->datosEstudiante->setId($id);
        return $this->datosEstudiante->eliminar();
    }

    public function listar(): array
    {
        return $this->datosEstudiante->listar();
    }

    // Asistencia
    public function listarAsistencia(int $id_estudiante, ?string $fini, ?string $ffin): array
    {
        return $this->dAsistencia->listarPorEstudiante($id_estudiante, $fini, $ffin);
    }
}
