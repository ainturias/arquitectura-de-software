<?php
require_once 'VistaBase.php';
require_once '../negocio/NMatrimonio.php';
require_once '../negocio/NMiembro.php';

class PMatrimonio extends VistaBase {
    private NMatrimonio $negocioMatrimonio;
    private NMiembro $negocioMiembro;

    public function __construct() {
        $this->negocioMatrimonio = new NMatrimonio();
        $this->negocioMiembro = new NMiembro();
    }

    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $idEsposo = isset($_POST['id_esposo']) ? (int)$_POST['id_esposo'] : null;
            $idEsposa = isset($_POST['id_esposa']) ? (int)$_POST['id_esposa'] : null;
            $fecha = $_POST['fecha'] ?? '';
            $lugar = trim($_POST['lugar'] ?? '');

            switch ($accion) {
                case 'crear':
                    $this->crear($fecha, $lugar, $idEsposo, $idEsposa);
                    break;
                case 'editar':
                    if ($id !== null) $this->editar($id, $fecha, $lugar, $idEsposo, $idEsposa);
                    break;
                case 'eliminar':
                    if ($id !== null) $this->eliminar($id);
                    break;
            }
        }
    }

    private function crear(string $fecha, string $lugar, int $idEsposo, int $idEsposa): void {
        if ($fecha && $lugar && $idEsposo && $idEsposa) {
            if ($this->negocioMatrimonio->crear($fecha, $lugar, $idEsposo, $idEsposa)) {
                echo "<p class='alert alert-success'>Matrimonio registrado correctamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Miembro ya casado o sin bautizar.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    private function editar(int $id, string $fecha, string $lugar, int $idEsposo, int $idEsposa): void {
        if ($fecha && $lugar && $idEsposo && $idEsposa) {
            if ($this->negocioMatrimonio->editar($id, $fecha, $lugar, $idEsposo, $idEsposa)) {
                echo "<p class='alert alert-success'>Matrimonio actualizado correctamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error: Miembro ya casado, sin bautizar o ID Matrimonio inválido.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios para editar.</p>";
        }
    }

    private function eliminar(int $id): void {
        if ($this->negocioMatrimonio->eliminar($id)) {
            echo "<p class='alert alert-success'>Matrimonio eliminado exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al eliminar matrimonio.</p>";
        }
    }

    private function listarMatrimonios(): array {
        return $this->negocioMatrimonio->getMatrimonios();
    }

    private function listarHombres(): array {
        return $this->negocioMiembro->getMiembrosHombres();
    }

    private function listarMujeres(): array {
        return $this->negocioMiembro->getMiembrosMujeres();
    }

    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Matrimonios");
        $matrimonios = $this->listarMatrimonios();
        $hombres = $this->listarHombres();
        $mujeres = $this->listarMujeres();
        ?>
        <h2>Gestionar Matrimonios</h2>
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-1"><input name="id" id="inputId" class="form-control" placeholder="ID"></div>
                <div class="col-md-2"><input name="fecha" id="inputFecha" type="date" class="form-control" title="Fecha del matrimonio" required></div>
                <div class="col-md-3"><input name="lugar" id="inputLugar" class="form-control" placeholder="Lugar" required></div>
                <div class="col-md-3">
                    <select name="id_esposo" id="inputEsposo" class="form-select" required>
                        <option value="">Seleccione esposo</option>
                        <?php foreach ($hombres as $h): ?>
                            <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['nombre']) . " (" . $h['ci'] . ")" ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="id_esposa" id="inputEsposa" class="form-select" required>
                        <option value="">Seleccione esposa</option>
                        <?php foreach ($mujeres as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) . " (" . $m['ci'] . ")" ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <h4>Listado de Matrimonios</h4>
        <?php if (!empty($matrimonios)): ?>
            <table name="listaMatrimonios" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th><th>Fecha</th><th>Lugar</th><th>Esposo</th><th>Esposa</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matrimonios as $mat): ?>
                        <tr>
                            <td><?= $mat['id'] ?></td>
                            <td><?= $mat['fecha'] ?></td>
                            <td><?= htmlspecialchars($mat['lugar']) ?></td>
                            <td><?= htmlspecialchars($mat['nombre_esposo']) ?></td>
                            <td><?= htmlspecialchars($mat['nombre_esposa']) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="seleccionarMatrimonio(this)"
                                    data-id="<?= $mat['id'] ?>"
                                    data-fecha="<?= $mat['fecha'] ?>"
                                    data-lugar="<?= htmlspecialchars($mat['lugar']) ?>"
                                    data-esposo="<?= $mat['id_esposo'] ?>"
                                    data-esposa="<?= $mat['id_esposa'] ?>">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No hay matrimonios registrados aún.</p>
        <?php endif; ?>

        <script>
            function seleccionarMatrimonio(btn) {
                const data = btn.dataset;
                document.getElementById('inputId').value = data.id;
                document.getElementById('inputFecha').value = data.fecha;
                document.getElementById('inputLugar').value = data.lugar;
                document.getElementById('inputEsposo').value = data.esposo;
                document.getElementById('inputEsposa').value = data.esposa;
            }
        </script>
        <?php
        $this->renderFin();
    }
}

$vista = new PMatrimonio();
$vista->procesarFormulario();
$vista->mostrarVista();
