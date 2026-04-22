<?php
require_once __DIR__ . '/../datos/DAula.php';

class NAula
{
    private DAula $datosAula;

    public function __construct()
    {
        $this->datosAula = new DAula();
    }

    // Crea una nueva aula verificando que no exista el código
    public function crear(string $codigo): bool
    {
        $yaExiste = $this->datosAula->existeCodigo($codigo);
        if ($yaExiste) {
            return false;
        }
        $this->datosAula->setCodigo($codigo);
        return $this->datosAula->crear();
    }

    // Edita el código de un aula existente
    public function editar(int $id, string $codigo): bool
    {
        $this->datosAula->setId($id);
        $this->datosAula->setCodigo($codigo);
        return $this->datosAula->editar();
    }

    // Elimina un aula por su id
    public function eliminar(int $id): bool
    {
        $this->datosAula->setId($id);
        return $this->datosAula->eliminar();
    }

    // Obtiene todas las aulas
    public function listar(): array
    {
        return $this->datosAula->listar();
    }


}
