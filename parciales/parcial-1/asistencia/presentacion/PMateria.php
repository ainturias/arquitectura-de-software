<?php
// Gestión de Materias - Capa de Presentación
require_once 'VistaBase.php';
require_once '../negocio/NMateria.php';

class PMateria extends VistaBase {
    private NMateria $negocioMateria;

    public function __construct() {
        $this->negocioMateria = new NMateria();
    }

    // Procesa las acciones del formulario (crear, editar, eliminar)
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $sigla = trim($_POST['sigla'] ?? '');
            $nombre = trim($_POST['nombre_materia'] ?? '');

            switch ($accion) {
                case 'crear':
                    if ($sigla && $nombre) {
                        echo $this->negocioMateria->crear($sigla, $nombre)
                            ? "<p class='alert alert-success'>Materia creada exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error: la sigla ya existe.</p>";
                    } else {
                        echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
                    }
                    break;
                case 'editar':
                    if ($id !== null && $sigla && $nombre) {
                        echo $this->negocioMateria->editar($id, $sigla, $nombre)
                            ? "<p class='alert alert-success'>Materia editada exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al editar materia.</p>";
                    }
                    break;
                case 'eliminar':
                    if ($id !== null) {
                        echo $this->negocioMateria->eliminar($id)
                            ? "<p class='alert alert-success'>Materia eliminada exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al eliminar materia.</p>";
                    }
                    break;
            }
        }
    }

    // Muestra la vista completa: formulario + tabla
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Materias");
        $materias = $this->negocioMateria->listar();
?>
        <h2>Gestionar Materias</h2>

        <!-- Formulario -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <input name="id" id="inputId" class="form-control" placeholder="ID" readonly>
                        </div>
                        <div class="col-md-3">
                            <input name="sigla" id="inputSigla" class="form-control" placeholder="Sigla" maxlength="20" required>
                        </div>
                        <div class="col-md-7">
                            <input name="nombre_materia" id="inputNombre" class="form-control" placeholder="Nombre de la materia" maxlength="120" required>
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

        <!-- Tabla de materias -->
        <div class="card">
            <div class="card-header"><strong>Listado de Materias</strong></div>
            <div class="card-body p-0">
                <?php if (!empty($materias)): ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Sigla</th><th>Nombre</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materias as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars($m['id_materia']) ?></td>
                                    <td><?= htmlspecialchars($m['sigla']) ?></td>
                                    <td><?= htmlspecialchars($m['nombre_materia']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                            data-id="<?= $m['id_materia'] ?>"
                                            data-sigla="<?= htmlspecialchars($m['sigla']) ?>"
                                            data-nombre="<?= htmlspecialchars($m['nombre_materia']) ?>">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted p-3">No hay materias registradas.</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
            // Llena el formulario al seleccionar una fila
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

// Ejecuta la vista
$vista = new PMateria();
$vista->procesarFormulario();
$vista->mostrarVista();
