<?php
// Gestión de Inscripciones y Reporte de Asistencia - Capa de Presentación
// Parte del Caso de Uso Transaccional (Gestionar Asistencia)
require_once 'VistaBase.php';
require_once '../negocio/NAsistencia.php';
require_once '../negocio/NGrupo.php';

class PInscripcion extends VistaBase {
    private NAsistencia $negocioAsistencia;
    private NGrupo $negocioGrupo;

    public function __construct() {
        $this->negocioAsistencia = new NAsistencia();
        $this->negocioGrupo = new NGrupo();
    }

    // Procesa inscribir o desinscribir un estudiante de un grupo
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id_grupo = isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo']) ? (int)$_POST['id_grupo'] : null;
            $id_estudiante = isset($_POST['id_estudiante']) && is_numeric($_POST['id_estudiante']) ? (int)$_POST['id_estudiante'] : null;

            if ($id_grupo && $id_estudiante) {
                switch ($accion) {
                    case 'inscribir':
                        echo $this->negocioAsistencia->inscribir($id_estudiante, $id_grupo)
                            ? "<p class='alert alert-success'>Estudiante inscrito exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al inscribir estudiante.</p>";
                        break;
                    case 'desinscribir':
                        echo $this->negocioAsistencia->desinscribir($id_estudiante, $id_grupo)
                            ? "<p class='alert alert-success'>Estudiante removido del grupo.</p>"
                            : "<p class='alert alert-danger'>Error al remover estudiante.</p>";
                        break;
                }
            }
        }
    }

    // Muestra la vista de inscripciones
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Inscripciones");
        $grupos = $this->negocioGrupo->listar();

        // Obtener el grupo seleccionado (por GET o POST)
        $id_grupo_sel = isset($_GET['id_grupo']) && is_numeric($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : null;
        if (!$id_grupo_sel && isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo'])) {
            $id_grupo_sel = (int)$_POST['id_grupo'];
        }

        // Filtro de fecha para el reporte (opcional)
        $fecha_filtro = $_GET['fecha'] ?? null;
?>
        <h2>Gestionar Inscripciones</h2>

        <!-- Selector de grupo -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <select name="id_grupo" class="form-select" required>
                                <option value="">-- Seleccionar Grupo --</option>
                                <?php foreach ($grupos as $g): ?>
                                    <option value="<?= $g['id_grupo'] ?>" <?= $id_grupo_sel == $g['id_grupo'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['materia'] . ' / ' . $g['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Ver Grupo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($id_grupo_sel):
            // Obtenemos los inscritos y los disponibles para este grupo
            $inscritos = $this->negocioAsistencia->listarEstudiantesDeGrupo($id_grupo_sel);
            $noInscritos = $this->negocioAsistencia->listarEstudiantesNoInscritos($id_grupo_sel);
            // Obtenemos el reporte de asistencia del grupo
            $asistencias = $this->negocioAsistencia->listarPorGrupo($id_grupo_sel, $fecha_filtro);
        ?>
            <div class="row">
                <!-- Estudiantes inscritos -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white"><strong>Estudiantes Inscritos</strong></div>
                        <div class="card-body p-0">
                            <?php if (!empty($inscritos)): ?>
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr><th>Registro</th><th>Nombre</th><th>Acción</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inscritos as $e): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e['registro']) ?></td>
                                                <td><?= htmlspecialchars($e['nombre_completo']) ?></td>
                                                <td>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="id_grupo" value="<?= $id_grupo_sel ?>">
                                                        <input type="hidden" name="id_estudiante" value="<?= $e['id_estudiante'] ?>">
                                                        <button name="accion" value="desinscribir" class="btn btn-sm btn-outline-danger">Quitar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted p-3">No hay estudiantes inscritos.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Estudiantes disponibles para inscribir -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-warning"><strong>Estudiantes Disponibles</strong></div>
                        <div class="card-body p-0">
                            <?php if (!empty($noInscritos)): ?>
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr><th>Registro</th><th>Nombre</th><th>Acción</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($noInscritos as $e): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e['registro']) ?></td>
                                                <td><?= htmlspecialchars($e['nombre_completo']) ?></td>
                                                <td>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="id_grupo" value="<?= $id_grupo_sel ?>">
                                                        <input type="hidden" name="id_estudiante" value="<?= $e['id_estudiante'] ?>">
                                                        <button name="accion" value="inscribir" class="btn btn-sm btn-outline-success">Inscribir</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted p-3">Todos los estudiantes ya están inscritos.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Asistencia del grupo -->
            <div class="card mt-2">
                <div class="card-header" style="background: linear-gradient(135deg, #1a237e, #283593);">
                    <strong class="text-white">📊 Reporte de Asistencia</strong>
                </div>
                <div class="card-body">
                    <!-- Filtro por fecha -->
                    <form method="GET" class="mb-3">
                        <input type="hidden" name="id_grupo" value="<?= $id_grupo_sel ?>">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Filtrar por fecha</label>
                                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($fecha_filtro ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </div>
                            <div class="col-md-2">
                                <a href="PInscripcion.php?id_grupo=<?= $id_grupo_sel ?>" class="btn btn-outline-secondary">Quitar filtro</a>
                            </div>
                        </div>
                    </form>

                    <?php if (!empty($asistencias)): ?>
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estudiante</th>
                                    <th>Registro</th>
                                    <th>Aula</th>
                                    <th>Horario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asistencias as $a): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($a['fecha']) ?></td>
                                        <td><?= htmlspecialchars($a['hora']) ?></td>
                                        <td><?= htmlspecialchars($a['estudiante']) ?></td>
                                        <td><?= htmlspecialchars($a['registro']) ?></td>
                                        <td><?= htmlspecialchars($a['aula']) ?></td>
                                        <td><?= htmlspecialchars($a['horario_rango']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted"><?= $fecha_filtro ? 'No hay registros de asistencia para esa fecha.' : 'Aún no hay registros de asistencia para este grupo.' ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
<?php
        $this->renderFin();
    }
}

// Ejecuta la vista
$vista = new PInscripcion();
$vista->procesarFormulario();
$vista->mostrarVista();
