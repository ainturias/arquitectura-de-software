<?php
// Capa de Negocio - Inscripción (COMPOSICIÓN: Grupo compone Inscripciones)
// Similar al patrón Factura -> DetalleFactura del ejemplo del ingeniero
// NInscripcion maneja UNA instancia de DInscripcion y puede recorrer
// una LISTA de estudiantes para inscribirlos (loop de composición)
require_once __DIR__ . '/../datos/DInscripcion.php';

class NInscripcion {
    private DInscripcion $datosInscripcion;

    public function __construct() {
        $this->datosInscripcion = new DInscripcion();
    }

    // Lista los estudiantes inscritos en un grupo
    public function listarEstudiantesDeGrupo(int $id_grupo): array {
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->listarEstudiantesDeGrupo();
    }

    // Lista los estudiantes que no están inscritos en el grupo
    public function listarEstudiantesNoInscritos(int $id_grupo): array {
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->listarEstudiantesNoInscritos();
    }

    // Inscribe un estudiante individual en el grupo
    public function inscribir(int $id_estudiante, int $id_grupo): bool {
        $this->datosInscripcion->setIdEstudiante($id_estudiante);
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->inscribir();
    }

    // Quita a un estudiante del grupo
    public function desinscribir(int $id_estudiante, int $id_grupo): bool {
        $this->datosInscripcion->setIdEstudiante($id_estudiante);
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->desinscribir();
    }

    // =============================================
    // COMPOSICIÓN: Inscribe una LISTA de estudiantes desde un CSV
    // Este es el patrón del ingeniero (NFactura -> loop DDetalleFactura)
    // Recorre cada fila del CSV y llama a DInscripcion para crear y 
    // registrar a cada estudiante en el grupo
    // =============================================
    public function inscribirLista(int $id_grupo, array $listaEstudiantes): array {
        $this->datosInscripcion->setIdGrupo($id_grupo);
        $ok = 0;
        $errores = 0;

        // Loop: recorre la lista e inscribe uno a uno (composición)
        foreach ($listaEstudiantes as $estudiante) {
            $resultado = $this->datosInscripcion->crearEstudianteEInscribir(
                $estudiante['registro'],
                $estudiante['nombre'],
                $estudiante['apellido']
            );
            if ($resultado) {
                $ok++;
            } else {
                $errores++;
            }
        }

        return ['ok' => $ok, 'errores' => $errores];
    }

    // Lee un archivo CSV y devuelve la lista de estudiantes
    // Formato esperado: registro,nombre,apellido
    public function procesarCSV(string $rutaArchivo): array {
        $lista = [];
        $handle = fopen($rutaArchivo, 'r');
        if (!$handle) return [];

        $primeraFila = true;
        while (($fila = fgetcsv($handle, 0, ',')) !== false) {
            // Si la primera fila es encabezado, la saltamos
            if ($primeraFila) {
                $primeraFila = false;
                $texto = strtolower(implode(',', $fila));
                if (str_contains($texto, 'registro') || str_contains($texto, 'nombre')) {
                    continue;
                }
            }
            $registro = trim($fila[0] ?? '');
            $nombre   = trim($fila[1] ?? '');
            $apellido = trim($fila[2] ?? '');
            if ($registro && $nombre && $apellido) {
                $lista[] = [
                    'registro' => $registro,
                    'nombre'   => $nombre,
                    'apellido' => $apellido
                ];
            }
        }
        fclose($handle);
        return $lista;
    }
}
