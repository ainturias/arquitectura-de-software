<?php
// Gestión de Estudiantes - Capa de Presentación
require_once 'VistaBase.php';
require_once '../negocio/NEstudiante.php';

class PEstudiante extends VistaBase {
    private NEstudiante $negocioEstudiante;

    public function __construct() {
        $this->negocioEstudiante = new NEstudiante();
    }

    // Procesa las acciones del formulario (Enrutador de funciones)
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $registro = trim($_POST['registro'] ?? '');

            switch ($accion) {
                case 'crear':
                    $this->crear($nombre, $apellido, $registro);
                    break;
                case 'editar':
                    $this->editar($id, $nombre, $apellido, $registro);
                    break;
                case 'eliminar':
                    $this->eliminar($id);
                    break;
            }
        }
    }

    // Método que activa la creación (Detalle Procedimental en el diagrama)
    private function crear(string $nombre, string $apellido, string $registro): void {
        if ($nombre && $apellido && $registro) {
            echo $this->negocioEstudiante->crear($nombre, $apellido, $registro)
                ? "<p class='alert alert-success'>Estudiante creado exitosamente.</p>"
                : "<p class='alert alert-danger'>Error: el registro ya existe.</p>";
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    // Método que activa la edición
    private function editar(?int $id, string $nombre, string $apellido, string $registro): void {
        if ($id !== null && $nombre && $apellido && $registro) {
            echo $this->negocioEstudiante->editar($id, $nombre, $apellido, $registro)
                ? "<p class='alert alert-success'>Estudiante editado exitosamente.</p>"
                : "<p class='alert alert-danger'>Error al editar estudiante.</p>";
        }
    }

    // Método que activa la eliminación
    private function eliminar(?int $id): void {
        if ($id !== null) {
            echo $this->negocioEstudiante->eliminar($id)
                ? "<p class='alert alert-success'>Estudiante eliminado exitosamente.</p>"
                : "<p class='alert alert-danger'>Error al eliminar estudiante.</p>";
        }
    }

    // Muestra la vista completa
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Estudiantes");
        $estudiantes = $this->negocioEstudiante->listar();
?>
        <h2>Gestionar Estudiantes</h2>

        <!-- Formulario -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <input name="id" id="inputId" class="form-control" placeholder="ID" readonly>
                        </div>
                        <div class="col-md-3">
                            <input name="nombre" id="inputNombre" class="form-control" placeholder="Nombre" maxlength="80" required>
                        </div>
                        <div class="col-md-3">
                            <input name="apellido" id="inputApellido" class="form-control" placeholder="Apellido" maxlength="80" required>
                        </div>
                        <div class="col-md-4">
                            <input name="registro" id="inputRegistro" class="form-control" placeholder="Registro (ej: 219012345)" maxlength="40" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button name="accion" value="crear" class="btn btn-success">Crear</button>
                        <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                        <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de estudiantes -->
        <div class="card">
            <div class="card-header"><strong>Listado de Estudiantes</strong></div>
            <div class="card-body p-0">
                <?php if (!empty($estudiantes)): ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Registro</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estudiantes as $e): ?>
                                <tr>
                                    <td><?= htmlspecialchars($e['id_estudiante']) ?></td>
                                    <td><?= htmlspecialchars($e['nombre']) ?></td>
                                    <td><?= htmlspecialchars($e['apellido']) ?></td>
                                    <td><?= htmlspecialchars($e['registro']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                            data-id="<?= $e['id_estudiante'] ?>"
                                            data-nombre="<?= htmlspecialchars($e['nombre']) ?>"
                                            data-apellido="<?= htmlspecialchars($e['apellido']) ?>"
                                            data-registro="<?= htmlspecialchars($e['registro']) ?>">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted p-3">No hay estudiantes registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
            // Llena el formulario al seleccionar un estudiante
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

// Ejecuta la vista
$vista = new PEstudiante();
$vista->procesarFormulario();
$vista->mostrarVista();
