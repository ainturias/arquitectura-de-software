<?php
require_once 'VistaBase.php';
require_once '../negocio/NMateria.php';

class PMateria extends VistaBase
{
    private NMateria $negocioMateria;

    private ?int $id = null;
    private string $sigla = '';
    private string $nombre_materia = '';
    private string $mensaje = '';

    public function __construct()
    {
        $this->negocioMateria = new NMateria();
    }

    // Enrutador de acciones
    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            // Capturamos el estado actual de la UI
            $this->id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;
            $this->sigla = trim($_POST['sigla'] ?? '');
            $this->nombre_materia = trim($_POST['nombre_materia'] ?? '');

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
        if ($this->sigla && $this->nombre_materia) {
            $this->negocioMateria->crear($this->sigla, $this->nombre_materia);
        }
    }

    private function editar(): void
    {
        if ($this->id !== null && $this->sigla && $this->nombre_materia) {
            $this->negocioMateria->editar($this->id, $this->sigla, $this->nombre_materia);
        }
    }

    private function eliminar(): void
    {
        if ($this->id !== null) {
            try {
                $this->negocioMateria->eliminar($this->id);
            } catch (Exception $e) {
                $this->mensaje = "<div class='alert alert-danger'>No se puede eliminar: la materia tiene grupos asociados.</div>";
            }
        }
    }

    private function listar(): array
    {
        return $this->negocioMateria->listar();
    }

    public function mostrarVista(): void
    {
        $materias = $this->listar();
        $this->renderInicio("Gestión de Materias");
        ?>
        <div class="container-fluid py-3">
            <h2 class="mb-4">Gestionar Materias</h2>

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
                            <div class="col-md-3">
                                <label class="text-muted small fw-bold">SIGLA</label>
                                <input name="sigla" id="inputSigla" class="form-control" placeholder="Ej: MAT-101"
                                    maxlength="20">
                            </div>
                            <div class="col-md-7">
                                <label class="text-muted small fw-bold">NOMBRE DE LA MATERIA</label>
                                <input name="nombre_materia" id="inputNombre" class="form-control"
                                    placeholder="Ej: Arquitectura de Software" maxlength="120">
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
                    <strong>LISTADO DE MATERIAS</strong>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($materias)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>SIGLA</th>
                                        <th>NOMBRE</th>
                                        <th class="text-end">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materias as $m): ?>
                                        <tr>
                                            <td><?= $m['id_materia'] ?></td>
                                            <td><strong><?= htmlspecialchars($m['sigla']) ?></strong></td>
                                            <td><?= htmlspecialchars($m['nombre_materia']) ?></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                                    data-id="<?= $m['id_materia'] ?>" data-sigla="<?= htmlspecialchars($m['sigla']) ?>"
                                                    data-nombre="<?= htmlspecialchars($m['nombre_materia']) ?>">
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
                document.getElementById('inputSigla').value = btn.dataset.sigla;
                document.getElementById('inputNombre').value = btn.dataset.nombre;
            }
        </script>
        <?php
        $this->renderFin();
    }
}

// Inicialización y ejecución
$vista = new PMateria();
$vista->procesarFormulario();
$vista->mostrarVista();
