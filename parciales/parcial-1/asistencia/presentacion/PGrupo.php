<?php
require_once 'VistaBase.php';
require_once '../negocio/NGrupo.php';
require_once '../negocio/NMateria.php';

class PGrupo extends VistaBase
{
    private NGrupo $negocioGrupo;
    private NMateria $negocioMateria;

    private ?int $id_grupo = null;
    private ?int $id_materia = null;
    private string $nombre = '';
    private string $mensaje = '';

    public function __construct()
    {
        $this->negocioGrupo = new NGrupo();
        $this->negocioMateria = new NMateria();
    }

    // Enrutador de acciones
    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            $this->id_grupo = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;
            $this->id_materia = isset($_POST['id_materia']) && is_numeric($_POST['id_materia']) ? (int) $_POST['id_materia'] : null;
            $this->nombre = trim($_POST['nombre'] ?? '');

            switch ($accion) {
                case 'crear':
                    $this->crear();
                    break;
                case 'editar':
                    $this->editar();
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
            }
        }
    }

    private function crear(): void
    {
        if ($this->id_materia && $this->nombre) {
            $this->negocioGrupo->crear($this->id_materia, $this->nombre);
        }
    }

    private function editar(): void
    {
        if ($this->id_grupo !== null && $this->id_materia && $this->nombre) {
            $this->negocioGrupo->editar($this->id_grupo, $this->id_materia, $this->nombre);
        }
    }

    private function eliminar(): void
    {
        if ($this->id_grupo !== null) {
            try {
                $this->negocioGrupo->eliminar($this->id_grupo);
            } catch (Exception $e) {
                $this->mensaje = "<div class='alert alert-danger'>No se puede eliminar: el grupo tiene horarios o inscripciones asociadas.</div>";
            }
        }
    }

    private function listarGrupos(): array
    {
        return $this->negocioGrupo->listar();
    }

    private function listarMaterias(): array
    {
        return $this->negocioMateria->listar();
    }

    public function mostrarVista(): void
    {
        $grupos = $this->listarGrupos();
        $materias = $this->listarMaterias();
        $this->renderInicio("Gestión de Grupos");
        ?>
        <div class="container-fluid py-3">
            <h2 class="mb-4">Gestionar Grupos</h2>

            <?php if ($this->mensaje): ?>
                <?= $this->mensaje ?>
            <?php endif; ?>

            <!-- Formulario de Entrada -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="text-muted small fw-bold">ID</label>
                                <input name="id" id="inputId" class="form-control bg-light" placeholder="ID" readonly>
                            </div>
                            <div class="col-md-5">
                                <label class="text-muted small fw-bold">MATERIA</label>
                                <select name="id_materia" id="selectMateria" class="form-select">
                                    <option value="">-- Seleccionar Materia --</option>
                                    <?php foreach ($materias as $m): ?>
                                        <option value="<?= $m['id_materia'] ?>">
                                            <?= htmlspecialchars($m['sigla'] . ' - ' . $m['nombre_materia']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="text-muted small fw-bold">NOMBRE DEL GRUPO</label>
                                <input name="nombre" id="inputNombre" class="form-control" placeholder="Ej: SA, SB"
                                    maxlength="80">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button name="accion" value="crear" class="btn btn-success px-4">CREAR</button>
                            <button name="accion" value="editar" class="btn btn-warning px-4 text-white">EDITAR</button>
                            <button name="accion" value="eliminar" class="btn btn-danger px-4">ELIMINAR</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <strong>LISTADO DE GRUPOS</strong>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($grupos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>MATERIA</th>
                                        <th>GRUPO</th>
                                        <th class="text-end">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($grupos as $g): ?>
                                        <tr>
                                            <td><?= $g['id_grupo'] ?></td>
                                            <td><?= htmlspecialchars($g['materia']) ?></td>
                                            <td><strong><?= htmlspecialchars($g['nombre']) ?></strong></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                                    data-id="<?= $g['id_grupo'] ?>" data-materia="<?= $g['id_materia'] ?>"
                                                    data-nombre="<?= htmlspecialchars($g['nombre']) ?>">
                                                    SELECCIONAR
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center py-5 text-muted">No existen registros.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
            function seleccionar(btn) {
                document.getElementById('inputId').value = btn.dataset.id;
                document.getElementById('selectMateria').value = btn.dataset.materia;
                document.getElementById('inputNombre').value = btn.dataset.nombre;
            }
        </script>
        <?php
        $this->renderFin();
    }
}

// Inicialización y ejecución
$vista = new PGrupo();
$vista->procesarFormulario();
$vista->mostrarVista();
