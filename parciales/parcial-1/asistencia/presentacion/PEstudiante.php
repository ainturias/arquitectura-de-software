<?php
require_once 'VistaBase.php';
require_once '../negocio/NEstudiante.php';

class PEstudiante extends VistaBase
{
    private NEstudiante $negocioEstudiante;

    private ?int $id = null;
    private string $nombre = '';
    private string $apellido = '';
    private string $registro = '';
    private string $mensaje = '';

    public function __construct()
    {
        $this->negocioEstudiante = new NEstudiante();
    }

    // Enrutador de acciones
    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            $this->id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;
            $this->nombre = trim($_POST['nombre'] ?? '');
            $this->apellido = trim($_POST['apellido'] ?? '');
            $this->registro = trim($_POST['registro'] ?? '');

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
        if ($this->nombre && $this->apellido && $this->registro) {
            $this->negocioEstudiante->crear($this->nombre, $this->apellido, $this->registro);
        }
    }

    private function editar(): void
    {
        if ($this->id !== null && $this->nombre && $this->apellido && $this->registro) {
            $this->negocioEstudiante->editar($this->id, $this->nombre, $this->apellido, $this->registro);
        }
    }

    private function eliminar(): void
    {
        if ($this->id !== null) {
            try {
                $this->negocioEstudiante->eliminar($this->id);
            } catch (Exception $e) {
                $this->mensaje = "<div class='alert alert-danger'>No se puede eliminar: el estudiante tiene inscripciones o asistencias asociadas.</div>";
            }
        }
    }

    private function listar(): array
    {
        return $this->negocioEstudiante->listar();
    }

    public function mostrarVista(): void
    {
        $estudiantes = $this->listar();
        $this->renderInicio("Gestión de Estudiantes");
        ?>
        <div class="container-fluid py-3">
            <h2 class="mb-4">Gestionar Estudiantes</h2>

            <?php if ($this->mensaje): ?>
                <?= $this->mensaje ?>
            <?php endif; ?>

            <!-- Formulario de Entrada -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-1">
                                <label class="text-muted small fw-bold">ID</label>
                                <input name="id" id="inputId" class="form-control bg-light" placeholder="ID" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small fw-bold">NOMBRE</label>
                                <input name="nombre" id="inputNombre" class="form-control" placeholder="Ej: Juan"
                                    maxlength="80">
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">APELLIDO</label>
                                <input name="apellido" id="inputApellido" class="form-control" placeholder="Ej: Pérez"
                                    maxlength="80">
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">REGISTRO</label>
                                <input name="registro" id="inputRegistro" class="form-control" placeholder="Ej: 219012345"
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
                    <strong>LISTADO DE ESTUDIANTES</strong>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($estudiantes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>NOMBRE</th>
                                        <th>APELLIDO</th>
                                        <th>REGISTRO</th>
                                        <th class="text-end">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estudiantes as $e): ?>
                                        <tr>
                                            <td><?= $e['id_estudiante'] ?></td>
                                            <td><?= htmlspecialchars($e['nombre']) ?></td>
                                            <td><strong><?= htmlspecialchars($e['apellido']) ?></strong></td>
                                            <td><?= htmlspecialchars($e['registro']) ?></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                                    data-id="<?= $e['id_estudiante'] ?>"
                                                    data-nombre="<?= htmlspecialchars($e['nombre']) ?>"
                                                    data-apellido="<?= htmlspecialchars($e['apellido']) ?>"
                                                    data-registro="<?= htmlspecialchars($e['registro']) ?>">
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
                document.getElementById('inputNombre').value = btn.dataset.nombre;
                document.getElementById('inputApellido').value = btn.dataset.apellido;
                document.getElementById('inputRegistro').value = btn.dataset.registro;
            }
        </script>
        <?php
        $this->renderFin();
    }
}

// Inicialización y ejecución
$vista = new PEstudiante();
$vista->procesarFormulario();
$vista->mostrarVista();
