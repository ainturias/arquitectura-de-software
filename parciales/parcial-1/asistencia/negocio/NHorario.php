<?php
require_once __DIR__ . '/../datos/DHorario.php';

// Librería para generar la imagen del código QR
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class NHorario
{
    private DHorario $datosHorario;

    public function __construct()
    {
        $this->datosHorario = new DHorario();
    }

    // Crea un nuevo horario verificando que no haya choque
    public function crear(int $id_aula, int $id_grupo, int $dia, string $hora_inicio, string $hora_fin): string
    {
        $hayChoque = $this->datosHorario->existeChoqueAula($id_aula, $dia, $hora_inicio, $hora_fin);
        if ($hayChoque) {
            return "El aula ya está ocupada en ese horario.";
        }
        $this->datosHorario->setIdAula($id_aula);
        $this->datosHorario->setIdGrupo($id_grupo);
        $this->datosHorario->setDiaSemana($dia);
        $this->datosHorario->setHoraInicio($hora_inicio);
        $this->datosHorario->setHoraFin($hora_fin);
        return $this->datosHorario->crear() ? "ok" : "Error al crear horario.";
    }

    // Edita un horario existente verificando choques
    public function editar(int $id, int $id_aula, int $id_grupo, int $dia, string $hora_inicio, string $hora_fin): string
    {
        $hayChoque = $this->datosHorario->existeChoqueAula($id_aula, $dia, $hora_inicio, $hora_fin, $id);
        if ($hayChoque) {
            return "El aula ya está ocupada en ese horario.";
        }
        $this->datosHorario->setId($id);
        $this->datosHorario->setIdAula($id_aula);
        $this->datosHorario->setIdGrupo($id_grupo);
        $this->datosHorario->setDiaSemana($dia);
        $this->datosHorario->setHoraInicio($hora_inicio);
        $this->datosHorario->setHoraFin($hora_fin);
        return $this->datosHorario->editar() ? "ok" : "Error al editar horario.";
    }

    // Elimina un horario por su id
    public function eliminar(int $id): bool
    {
        $this->datosHorario->setId($id);
        return $this->datosHorario->eliminar();
    }

    // Lista todos los horarios
    public function listar(): array
    {
        return $this->datosHorario->listar();
    }

    // Genera la imagen QR para un horario específico
    // El QR codifica la URL con el id_horario para que el estudiante lo escanee
    public function generarQrImagen(int $id_horario): ?string
    {
        $baseUrl = "http://localhost:8000/presentacion/PAsistencia.php";
        $urlCompleta = $baseUrl . "?id_horario=" . $id_horario;

        try {
            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $urlCompleta,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10
            );

            $result = $builder->build();
            return $result->getDataUri();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // Obtiene toda la información necesaria para el QR (Imagen + Etiqueta de texto)
    public function obtenerDatoCompletoQR(int $id_horario): array
    {
        $h = $this->datosHorario->buscarPorId($id_horario);
        if (!$h) return ['id' => null, 'imagen' => null, 'etiqueta' => ''];

        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $diaNombre = $dias[$h['dia_semana']] ?? '';

        return [
            'id'       => $h['id_horario'],
            'imagen'   => $this->generarQrImagen($id_horario),
            'etiqueta' => "{$h['sigla']} - {$h['grupo_nombre']} / {$diaNombre} {$h['hora_inicio']}-{$h['hora_fin']}"
        ];
    }
}
