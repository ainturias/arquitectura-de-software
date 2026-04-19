<?php
// Gestión de Inscripciones - Capa de Presentación (CU Transaccional)
// Implementa el patrón de Composición (Master-Detail) para la carga masiva
require_once 'VistaBase.php';
require_once '../negocio/NInscripcion.php';
require_once '../negocio/NGrupo.php';

class PInscripcion extends VistaBase {
    private NInscripcion $negocioInscripcion;
    private NGrupo $negocioGrupo;

    public function __construct() {
        $this->negocioInscripcion = new NInscripcion();
        $this->negocioGrupo = new NGrupo();
    }

    // Procesa las acciones del formulario (Enrutador)
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id_grupo = isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo']) ? (int)$_POST['id_grupo'] : null;
            $id_estudiante = isset($_POST['id_estudiante']) && is_numeric($_POST['id_estudiante']) ? (int)$_POST['id_estudiante'] : null;

            switch ($accion) {
                case 'desinscribir':
                    $this->desinscribir($id_estudiante, $id_grupo);
                    break;
                case 'importar':
                    $this->importarCSV($id_grupo);
                    break;
            }
        }
    }

    // Método que activa la eliminación de la inscripción en el Negocio
    private function desinscribir(?int $id_estu, ?int $id_g): void {
        if ($id_g && $id_estu) {
            echo $this->negocioInscripcion->desinscribir($id_estu, $id_g)
                ? "<p class='alert alert-success'>Estudiante removido del grupo.</p>"
                : "<p class='alert alert-danger'>Error al remover estudiante.</p>";
        }
    }

    // Procesa la importación del archivo CSV usando NInscripcion (Lógica de Composición)
    private function importarCSV(?int $id_grupo): void {
        if (!$id_grupo) {
            echo "<p class='alert alert-warning'>Seleccione un grupo primero.</p>";
            return;
        }
        if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
            echo "<p class='alert alert-warning'>Seleccione un archivo CSV válido.</p>";
            return;
        }

        $lista = $this->negocioInscripcion->procesarCSV($_FILES['archivo_csv']['tmp_name']);
        if (empty($lista)) {
            echo "<p class='alert alert-warning'>El archivo CSV está vacío o tiene formato incorrecto.</p>";
            return;
        }

        // Llamada al negocio (Loop de composición)
        $resultado = $this->negocioInscripcion->inscribirLista($id_grupo, $lista);
        echo "<p class='alert alert-success'>Importación completada: {$resultado['ok']} inscrito(s), {$resultado['errores']} error(es).</p>";
    }

    // Muestra la vista de inscripciones en formato vertical
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Inscripciones");
        $grupos = $this->negocioGrupo->listar();

        // Obtener el grupo seleccionado
        $id_grupo_sel = isset($_GET['id_grupo']) && is_numeric($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : null;
        if (!$id_grupo_sel && isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo'])) {
            $id_grupo_sel = (int)$_POST['id_grupo'];
        }
?>
        <div class="container-fluid py-3">
            <h2 class="mb-4">Gestionar Inscripciones</h2>

            <!-- 1. SELECCIÓN DE GRUPO (Vertical) -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>1. Seleccionar Grupo</strong>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-md-9">
                            <select name="id_grupo" class="form-select" required>
                                <option value="">-- Materia / Grupo --</option>
                                <?php foreach ($grupos as $g): ?>
                                    <option value="<?= $g['id_grupo'] ?>" <?= $id_grupo_sel == $g['id_grupo'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['materia'] . ' / ' . $g['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Ver Grupo</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. IMPORTAR CSV (Estático - Siempre arriba) -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-info text-white">
                    <strong>2. Importar Estudiantes (CSV)</strong>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_grupo" value="<?= $id_grupo_sel ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-9">
                                <label class="form-label text-muted small">Seleccione archivo (.csv) con formato: registro, nombre, apellido</label>
                                <input type="file" name="archivo_csv" class="form-control" accept=".csv" <?= !$id_grupo_sel ? 'disabled' : '' ?> required>
                            </div>
                            <div class="col-md-3">
                                <button name="accion" value="importar" class="btn btn-info text-white w-100" <?= !$id_grupo_sel ? 'disabled' : '' ?>>
                                    📁 Importar CSV
                                </button>
                            </div>
                        </div>
                        <?php if (!$id_grupo_sel): ?>
                            <div class="mt-2 text-danger small">Debe seleccionar un grupo antes de importar.</div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- 3. LISTADO DE INSCRITOS (Feedback de Composición) -->
            <?php if ($id_grupo_sel): 
                $inscritos = $this->negocioInscripcion->listarEstudiantesDeGrupo($id_grupo_sel);
            ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <strong>Estudiantes Inscritos en el Grupo</strong>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($inscritos)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Registro</th>
                                            <th>Nombre Completo</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inscritos as $e): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e['registro']) ?></td>
                                                <td><?= htmlspecialchars($e['nombre_completo']) ?></td>
                                                <td class="text-center">
                                                    <form method="POST" onsubmit="return confirm('¿Remover estudiante?');">
                                                        <input type="hidden" name="id_grupo" value="<?= $id_grupo_sel ?>">
                                                        <input type="hidden" name="id_estudiante" value="<?= $e['id_estudiante'] ?>">
                                                        <button name="accion" value="desinscribir" class="btn btn-sm btn-outline-danger">Quitar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning m-3 text-center">
                                Aún no hay estudiantes inscritos en este grupo. ¡Usa la opción de importar arriba!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
<?php
        $this->renderFin();
    }
}

// Ejecuta la vista
$vista = new PInscripcion();
$vista->procesarFormulario();
$vista->mostrarVista();
