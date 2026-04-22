<?php
// Gestión de Inscripciones - Capa de Presentación (CU Transaccional)
// Patrón: Grupo (cabecera) + Inscripcion (detalle) con foreach
require_once 'VistaBase.php';
require_once '../negocio/NInscripcion.php';
require_once '../negocio/NGrupo.php';
require_once '../negocio/NEstudiante.php';

class PInscripcion extends VistaBase
{
    private NInscripcion $negocioInscripcion;
    private NGrupo $negocioGrupo;
    private NEstudiante $negocioEstudiante;

    // Atributos de estado
    private ?int $id_grupo = null;
    private ?int $id_estudiante = null;
    private string $mensaje = '';
    private string $tipoAlerta = '';

    public function __construct()
    {
        $this->negocioInscripcion = new NInscripcion();
        $this->negocioGrupo = new NGrupo();
        $this->negocioEstudiante = new NEstudiante();
    }

    // Métodos auxiliares para la vista
    private function obtenerDisponibles(int $id_grupo): array
    {
        return $this->negocioEstudiante->listarNoInscritosEnGrupo($id_grupo);
    }

    private function obtenerInscritos(int $id_grupo): array
    {
        return $this->negocioInscripcion->listarEstudiantesDeGrupo($id_grupo);
    }

    // Enrutador: captura los datos del formulario y ejecuta la acción
    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $this->id_grupo = isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo']) ? (int) $_POST['id_grupo'] : null;
            $this->id_estudiante = isset($_POST['id_estudiante']) && is_numeric($_POST['id_estudiante']) ? (int) $_POST['id_estudiante'] : null;

            switch ($accion) {
                case 'inscribir_lista':
                    $this->inscribirLista();
                    break;
                case 'desinscribir':
                    $this->desinscribir();
                    break;
            }
        }
    }

    // Loop de Composición: inscribe la lista de estudiantes del arreglo
    private function inscribirLista(): void
    {
        $ids = $_POST['estudiantes'] ?? [];
        if (!$this->id_grupo || empty($ids)) {
            $this->mensaje = 'Debe seleccionar un grupo y agregar al menos un estudiante.';
            $this->tipoAlerta = 'warning';
            return;
        }

        // Delegamos al negocio el foreach de composición
        $resultado = $this->negocioInscripcion->inscribirListaPorIds($this->id_grupo, $ids);
        $this->mensaje = "Inscripción completada: {$resultado['ok']} inscrito(s), {$resultado['errores']} error(es).";
        $this->tipoAlerta = $resultado['ok'] > 0 ? 'success' : 'danger';
    }

    // Quita a un estudiante del grupo
    private function desinscribir(): void
    {
        if ($this->id_grupo && $this->id_estudiante) {
            $seElimino = $this->negocioInscripcion->desinscribir($this->id_estudiante, $this->id_grupo);
            $this->mensaje = $seElimino ? 'Estudiante removido del grupo.' : 'Error al remover estudiante.';
            $this->tipoAlerta = $seElimino ? 'success' : 'danger';
        }
    }

    // Obtiene la lista de grupos para el combo de cabecera
    private function listarGrupos(): array
    {
        return $this->negocioGrupo->listar();
    }

    // Determina el grupo seleccionado (GET o POST)
    private function obtenerGrupoSeleccionado(): ?int
    {
        if (isset($_GET['id_grupo']) && is_numeric($_GET['id_grupo'])) {
            return (int) $_GET['id_grupo'];
        }
        return $this->id_grupo;
    }

    // Punto de entrada de la vista
    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Inscripciones");
        $grupos = $this->listarGrupos();
        $id_grupo_sel = $this->obtenerGrupoSeleccionado();
        ?>
        <div class="container-fluid py-3">
            <h2 class="mb-4">Gestionar Inscripciones</h2>

            <!-- Mensaje de retroalimentación -->
            <?php if ($this->mensaje): ?>
                <div class="alert alert-<?= $this->tipoAlerta ?> alert-dismissible fade show">
                    <?= $this->mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- 1. CABECERA: SELECCIÓN DE GRUPO -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #1a237e, #283593);">
                    <strong>1. Seleccionar Grupo (Cabecera)</strong>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-md-9">
                            <select name="id_grupo" class="form-select">
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

            <?php if ($id_grupo_sel):
                $noInscritos = $this->obtenerDisponibles($id_grupo_sel);
                $inscritos = $this->obtenerInscritos($id_grupo_sel);
            ?>

            <!-- 2. AGREGAR ESTUDIANTES (Combo + Agregar uno por uno + Tabla temporal) -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-info text-white">
                    <strong>2. Agregar Estudiantes al Grupo</strong>
                </div>
                <div class="card-body">
                    <form method="POST" id="formInscripcion">
                        <input type="hidden" name="accion" value="inscribir_lista">
                        <input type="hidden" name="id_grupo" value="<?= $id_grupo_sel ?>">

                        <!-- Combo de Estudiantes + Botón Agregar -->
                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-md-9">
                                <label class="form-label text-muted">Estudiantes disponibles</label>
                                <select id="comboEstudiantes" class="form-select">
                                    <option value="">-- Seleccionar estudiante --</option>
                                    <?php foreach ($noInscritos as $e): ?>
                                        <option value="<?= $e['id_estudiante'] ?>"
                                                data-nombre="<?= htmlspecialchars($e['nombre_completo']) ?>"
                                                data-registro="<?= htmlspecialchars($e['registro']) ?>">
                                            <?= htmlspecialchars($e['nombre_completo'] . ' (' . $e['registro'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="agregarEstudiante()">
                                    Agregar Estudiante
                                </button>
                            </div>
                        </div>

                        <!-- Tabla temporal: DETALLE DE INSCRIPCIÓN -->
                        <div id="seccionDetalle" style="display: none;">
                            <h6 class="text-muted mb-2">Detalle de Inscripcion</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-3" id="tablaDetalle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Estudiante</th>
                                            <th>Registro</th>
                                            <th class="text-center" style="width: 80px;">Quitar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDetalle">
                                    </tbody>
                                </table>
                            </div>

                            <!-- Botones de acción -->
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="limpiarDetalle()">Cancelar</button>
                                <button type="submit" class="btn btn-success">Registrar Inscripcion</button>
                            </div>
                        </div>

                        <!-- Hidden inputs para los IDs (se agregan por JS) -->
                        <div id="hiddenInputs"></div>
                    </form>
                </div>
            </div>

            <!-- 3. ESTUDIANTES YA INSCRITOS -->
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
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="accion" value="desinscribir">
                                                    <input type="hidden" name="id_grupo" value="<?= $id_grupo_sel ?>">
                                                    <input type="hidden" name="id_estudiante" value="<?= $e['id_estudiante'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover estudiante?');">Quitar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning m-3 text-center">
                            Aun no hay estudiantes inscritos en este grupo.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php endif; ?>
        </div>

        <!-- JavaScript: Manejo de la tabla temporal (equivale al tableDetalle del ingeniero) -->
        <script>
            function agregarEstudiante() {
                const combo = document.getElementById('comboEstudiantes');
                const selected = combo.options[combo.selectedIndex];

                if (!combo.value) {
                    alert('Seleccione un estudiante.');
                    return;
                }

                const id = selected.value;
                const nombre = selected.getAttribute('data-nombre');
                const registro = selected.getAttribute('data-registro');

                // Agregar fila a la tabla temporal
                const tbody = document.getElementById('tbodyDetalle');
                const fila = document.createElement('tr');
                fila.id = 'fila-' + id;
                fila.innerHTML =
                    '<td>' + nombre + '</td>' +
                    '<td>' + registro + '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-sm btn-outline-danger" onclick="quitarEstudiante(\'' + id + '\', \'' + nombre + ' (' + registro + ')\')">x</button>' +
                    '</td>';
                tbody.appendChild(fila);

                // Agregar hidden input para el POST
                const hiddenDiv = document.getElementById('hiddenInputs');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'estudiantes[]';
                input.value = id;
                input.id = 'input-' + id;
                hiddenDiv.appendChild(input);

                // Quitar del combo
                combo.remove(combo.selectedIndex);
                combo.selectedIndex = 0;

                // Mostrar la seccion de detalle
                document.getElementById('seccionDetalle').style.display = 'block';
            }

            function quitarEstudiante(id, texto) {
                document.getElementById('fila-' + id).remove();
                document.getElementById('input-' + id).remove();

                // Devolver al combo
                const combo = document.getElementById('comboEstudiantes');
                const option = document.createElement('option');
                option.value = id;
                option.textContent = texto;
                combo.appendChild(option);

                // Si no quedan filas, ocultar
                if (document.getElementById('tbodyDetalle').children.length === 0) {
                    document.getElementById('seccionDetalle').style.display = 'none';
                }
            }

            function limpiarDetalle() {
                document.getElementById('tbodyDetalle').innerHTML = '';
                document.getElementById('hiddenInputs').innerHTML = '';
                document.getElementById('seccionDetalle').style.display = 'none';
                location.reload();
            }
        </script>
        <?php
        $this->renderFin();
    }
}

// Inicializacion y ejecucion
$vista = new PInscripcion();
$vista->procesarFormulario();
$vista->mostrarVista();
