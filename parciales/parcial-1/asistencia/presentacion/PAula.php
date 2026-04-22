<?php
require_once 'VistaBase.php';
require_once '../negocio/NAula.php';

class PAula extends VistaBase
{
    private NAula $negocioAula;

    // Atributos de clase (Estado de la Vista)
    private ?int $id = null;
    private string $codigo = '';
    private string $mensaje = '';

    public function __construct()
    {
        $this->negocioAula = new NAula();
    }

    // Enrutador de acciones
    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            // Capturamos el estado actual de la UI
            $this->id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;
            $this->codigo = trim($_POST['codigo'] ?? '');

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
        if ($this->codigo) {
            $this->negocioAula->crear($this->codigo);
        }
    }

    private function editar(): void
    {
        if ($this->id !== null && $this->codigo) {
            $this->negocioAula->editar($this->id, $this->codigo);
        }
    }

    private function eliminar(): void
    {
        if ($this->id !== null) {
            try {
                $this->negocioAula->eliminar($this->id);
            } catch (Exception $e) {
                $this->mensaje = "<div class='alert alert-danger'>No se puede eliminar: el aula tiene horarios asociados.</div>";
            }
        } else {
            $this->mensaje = "<div class='alert alert-warning'>Seleccione un aula de la tabla primero.</div>";
        }
    }

    private function listar(): array
    {
        return $this->negocioAula->listar();
    }

    public function mostrarVista(): void
    {
        $aulas = $this->listar();
        $this->renderInicio("Gestión de Aulas");
        ?>
        <div class="container-fluid py-3">
            <h2 class="mb-4">Gestionar Aulas</h2>

            <?php if ($this->mensaje): ?>
                <?= $this->mensaje ?>
            <?php endif; ?>

            <!-- Formulario de Entrada -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="text-muted small fw-bold">ID</label>
                                <input name="id" id="inputId" class="form-control bg-light" placeholder="ID" readonly>
                            </div>
                            <div class="col-md-9">
                                <label class="text-muted small fw-bold">CÓDIGO</label>
                                <input name="codigo" id="inputCodigo" class="form-control" placeholder="Ej: Aula-15"
                                    maxlength="40">
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
                    <strong>LISTADO DE AULAS</strong>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($aulas)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>CÓDIGO</th>
                                        <th class="text-end">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($aulas as $a): ?>
                                        <tr>
                                            <td><?= $a['id_aula'] ?></td>
                                            <td><strong><?= htmlspecialchars($a['codigo']) ?></strong></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                                    data-id="<?= $a['id_aula'] ?>" data-codigo="<?= htmlspecialchars($a['codigo']) ?>">
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
                document.getElementById('inputCodigo').value = btn.dataset.codigo;
            }
        </script>
        <?php
        $this->renderFin();
    }
}

// Inicialización y ejecución
$vista = new PAula();
$vista->procesarFormulario();
$vista->mostrarVista();
