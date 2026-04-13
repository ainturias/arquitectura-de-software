<?php
// File: presentacion/PMiembro.php
require_once 'VistaBase.php';
require_once '../negocio/NMiembro.php';

class PMiembro extends VistaBase
{
    private NMiembro $negocioMiembro;

    public function __construct()
    {
        $this->negocioMiembro = new NMiembro();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $ci = trim($_POST['ci'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
            $sexo = $_POST['sexo'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');

            switch ($accion) {
                case 'crear':
                    $this->crear($ci, $nombre, $fechaNacimiento, $sexo, $telefono);
                    break;
                case 'editar':
                    if ($id !== null) {
                        $this->editar($id, $ci, $nombre, $fechaNacimiento, $sexo, $telefono);
                    } else {
                        echo "<p class='alert alert-warning'>ID inválido para editar.</p>";
                    }
                    break;
                case 'eliminar':
                    if ($id !== null) {
                        $this->eliminar($id);
                    } else {
                        echo "<p class='alert alert-warning'>ID inválido para eliminar.</p>";
                    }
                    break;
            }
        }
    }

    private function crear(string $ci, string $nombre, string $fechaNacimiento, string $sexo, string $telefono): void
    {
        if ($ci && $nombre && $fechaNacimiento && $sexo && $telefono) {
            if ($this->negocioMiembro->crear($ci, $nombre, $fechaNacimiento, $sexo, $telefono)) {
                echo "<p class='alert alert-success'>Miembro creado exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al crear miembro. El CI ya existe</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    private function editar(int $id, string $ci, string $nombre, string $fechaNacimiento, string $sexo, string $telefono): void
    {
        if ($ci && $nombre && $fechaNacimiento && $sexo && $telefono) {
            if ($this->negocioMiembro->editar($id, $ci, $nombre, $fechaNacimiento, $sexo, $telefono)) {
                echo "<p class='alert alert-success'>Miembro editado exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al editar miembro.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios para editar.</p>";
        }
    }

    private function eliminar(int $id): void
    {
        if ($this->negocioMiembro->eliminar($id)) {
            echo "<p class='alert alert-success'>Miembro eliminado exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al eliminar miembro.</p>";
        }
    }

    private function listarMiembros(): array
    {
        return $this->negocioMiembro->getMiembros();
    }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Miembros");
        $miembros = $this->listarMiembros();
?>
        <h2>Gestionar Miembros</h2>
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2"><input name="id" id="inputId" class="form-control" placeholder="ID"></div>
                <div class="col-md-2"><input name="ci" id="inputCi" class="form-control" placeholder="CI" required></div>
                <div class="col-md-3"><input name="nombre" id="inputNombre" class="form-control" placeholder="Nombre" required></div>
                <div class="col-md-2"><input name="fechaNacimiento" id="inputFecha" class="form-control" title="Fecha de nacimiento" type="date" required></div>
                <div class="col-md-1">
                    <select name="sexo" id="inputSexo" class="form-select" required>
                        <option value="">--</option>
                        <option>M</option>
                        <option>F</option>
                    </select>
                </div>
                <div class="col-md-2"><input name="telefono" id="inputTelefono" class="form-control" placeholder="Teléfono" required></div>
            </div>
            <div class="mt-3">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <h4>Listado de Miembros</h4>
        <?php if (!empty($miembros)): ?>
            <table name="listaMiembros" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>CI</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Sexo</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($miembros as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['id']) ?></td>
                            <td><?= htmlspecialchars($m['ci']) ?></td>
                            <td><?= htmlspecialchars($m['nombre']) ?></td>
                            <td><?= htmlspecialchars($m['fecha_nacimiento']) ?></td>
                            <td><?= htmlspecialchars($m['sexo']) ?></td>
                            <td><?= htmlspecialchars($m['telefono']) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick='seleccionarMiembro(this)'
                                    data-id="<?= $m['id'] ?>"
                                    data-ci="<?= htmlspecialchars($m['ci']) ?>"
                                    data-nombre="<?= htmlspecialchars($m['nombre']) ?>"
                                    data-fecha="<?= $m['fecha_nacimiento'] ?>"
                                    data-sexo="<?= $m['sexo'] ?>"
                                    data-telefono="<?= $m['telefono'] ?>">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No hay miembros registrados aún.</p>
        <?php endif; ?>

        <script>
            function seleccionarMiembro(btn) {
                const fila = btn.dataset;
                document.getElementById('inputId').value = fila.id;
                document.getElementById('inputCi').value = fila.ci;
                document.getElementById('inputNombre').value = fila.nombre;
                document.getElementById('inputFecha').value = fila.fecha;
                document.getElementById('inputSexo').value = fila.sexo;
                document.getElementById('inputTelefono').value = fila.telefono;
            }
        </script>
<?php
        $this->renderFin();
    }
}

$vista = new PMiembro();
$vista->procesarFormulario();
$vista->mostrarVista();
