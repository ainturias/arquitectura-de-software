<?php
// Gestión de Horarios - Capa de Presentación (CU Complejo)
// Incluye la generación del QR (el QR pertenece al horario)
require_once 'VistaBase.php';
require_once '../negocio/NHorario.php';
require_once '../negocio/NAula.php';
require_once '../negocio/NGrupo.php';

class PHorario extends VistaBase {
    private NHorario $negocioHorario;
    private NAula $negocioAula;
    private NGrupo $negocioGrupo;

    // Nombres de los días para mostrar en la tabla
    private array $diasSemana = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
        4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];

    public function __construct() {
        $this->negocioHorario = new NHorario();
        $this->negocioAula = new NAula();
        $this->negocioGrupo = new NGrupo();
    }

    // Procesa las acciones del formulario (Enrutador)
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $id_aula = isset($_POST['id_aula']) && is_numeric($_POST['id_aula']) ? (int)$_POST['id_aula'] : null;
            $id_grupo = isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo']) ? (int)$_POST['id_grupo'] : null;
            $dia = isset($_POST['dia_semana']) && is_numeric($_POST['dia_semana']) ? (int)$_POST['dia_semana'] : null;
            $hora_inicio = $_POST['hora_inicio'] ?? '';
            $hora_fin = $_POST['hora_fin'] ?? '';

            switch ($accion) {
                case 'crear':
                    $this->crear($id_aula, $id_grupo, $dia, $hora_inicio, $hora_fin);
                    break;
                case 'editar':
                    $this->editar($id, $id_aula, $id_grupo, $dia, $hora_inicio, $hora_fin);
                    break;
                case 'eliminar':
                    $this->eliminar($id);
                    break;
            }
        }
    }

    // Método que activa la creación en el Negocio (Detalle Procedimental)
    private function crear(?int $id_aula, ?int $id_grupo, ?int $dia, string $inicio, string $fin): void {
        if ($id_aula && $id_grupo && $dia && $inicio && $fin) {
            $resultado = $this->negocioHorario->crear($id_aula, $id_grupo, $dia, $inicio, $fin);
            echo $resultado === 'ok'
                ? "<p class='alert alert-success'>Horario creado exitosamente.</p>"
                : "<p class='alert alert-danger'>$resultado</p>";
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    // Método que activa la edición en el Negocio
    private function editar(?int $id, ?int $id_aula, ?int $id_grupo, ?int $dia, string $inicio, string $fin): void {
        if ($id !== null && $id_aula && $id_grupo && $dia && $inicio && $fin) {
            $resultado = $this->negocioHorario->editar($id, $id_aula, $id_grupo, $dia, $inicio, $fin);
            echo $resultado === 'ok'
                ? "<p class='alert alert-success'>Horario editado exitosamente.</p>"
                : "<p class='alert alert-danger'>$resultado</p>";
        }
    }

    // Método que activa la eliminación en el Negocio
    private function eliminar(?int $id): void {
        if ($id !== null) {
            echo $this->negocioHorario->eliminar($id)
                ? "<p class='alert alert-success'>Horario eliminado exitosamente.</p>"
                : "<p class='alert alert-danger'>Error al eliminar horario.</p>";
        }
    }

    // Muestra la vista completa: formulario + tabla + QR al costado
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Horarios");
        $horarios = $this->negocioHorario->listar();
        $aulas = $this->negocioAula->listar();
        $grupos = $this->negocioGrupo->listar();

        // Si se pidió ver el QR de un horario, generamos la imagen
        $qrImagen = null;
        $qrHorarioId = null;
        $qrInfo = '';
        if (isset($_GET['qr']) && is_numeric($_GET['qr'])) {
            $qrHorarioId = (int)$_GET['qr'];
            $qrImagen = $this->negocioHorario->generarQrImagen($qrHorarioId);
            // Buscamos la info del horario para mostrar junto al QR
            foreach ($horarios as $h) {
                if ($h['id_horario'] == $qrHorarioId) {
                    $qrInfo = $h['sigla'] . ' - ' . $h['grupo_nombre'] . ' / ' . ($this->diasSemana[$h['dia_semana']] ?? '') . ' ' . $h['hora_inicio'] . '-' . $h['hora_fin'];
                    break;
                }
            }
        }
?>
        <!-- CSS para impresión: solo muestra el QR -->
        <style>
            @media print {
                body * { visibility: hidden; }
                #qr-print, #qr-print * { visibility: visible; }
                #qr-print {
                    position: absolute; left: 0; top: 0;
                    width: 100%; text-align: center; padding-top: 40px;
                }
            }
        </style>

        <h2>Gestionar Horarios</h2>

        <!-- Formulario -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-1">
                            <input name="id" id="inputId" class="form-control" placeholder="ID" readonly>
                        </div>
                        <div class="col-md-2">
                            <select name="id_aula" id="selectAula" class="form-select" required>
                                <option value="">-- Aula --</option>
                                <?php foreach ($aulas as $a): ?>
                                    <option value="<?= $a['id_aula'] ?>"><?= htmlspecialchars($a['codigo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="id_grupo" id="selectGrupo" class="form-select" required>
                                <option value="">-- Grupo --</option>
                                <?php foreach ($grupos as $g): ?>
                                    <option value="<?= $g['id_grupo'] ?>">
                                        <?= htmlspecialchars($g['materia'] . ' / ' . $g['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="dia_semana" id="selectDia" class="form-select" required>
                                <option value="">-- Día --</option>
                                <?php foreach ($this->diasSemana as $num => $nombre): ?>
                                    <option value="<?= $num ?>"><?= $nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="time" name="hora_inicio" id="inputHoraInicio" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <input type="time" name="hora_fin" id="inputHoraFin" class="form-control" required>
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

        <!-- Tabla y QR lado a lado -->
        <div class="row">
            <!-- Listado de horarios (izquierda) -->
            <div class="<?= $qrImagen ? 'col-md-7' : 'col-12' ?>">
                <div class="card">
                    <div class="card-header"><strong>Listado de Horarios</strong></div>
                    <div class="card-body p-0">
                        <?php if (!empty($horarios)): ?>
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>ID</th><th>Aula</th><th>Materia</th><th>Grupo</th><th>Día</th><th>Hora</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($horarios as $h): ?>
                                        <tr class="<?= ($qrHorarioId == $h['id_horario']) ? 'table-active' : '' ?>">
                                            <td><?= $h['id_horario'] ?></td>
                                            <td><?= htmlspecialchars($h['aula_codigo']) ?></td>
                                            <td><?= htmlspecialchars($h['sigla'] . ' - ' . $h['nombre_materia']) ?></td>
                                            <td><?= htmlspecialchars($h['grupo_nombre']) ?></td>
                                            <td><?= $this->diasSemana[$h['dia_semana']] ?? $h['dia_semana'] ?></td>
                                            <td><?= $h['hora_inicio'] ?> - <?= $h['hora_fin'] ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                                    data-id="<?= $h['id_horario'] ?>"
                                                    data-aula="<?= $h['id_aula'] ?>"
                                                    data-grupo="<?= $h['id_grupo'] ?>"
                                                    data-dia="<?= $h['dia_semana'] ?>"
                                                    data-inicio="<?= $h['hora_inicio'] ?>"
                                                    data-fin="<?= $h['hora_fin'] ?>">
                                                    Seleccionar
                                                </button>
                                                <a href="PHorario.php?qr=<?= $h['id_horario'] ?>" class="btn btn-sm btn-outline-info">Ver QR</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted p-3">No hay horarios registrados.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php // Si se generó un QR, lo mostramos al costado derecho ?>
            <?php if ($qrImagen): ?>
                <div class="col-md-5">
                    <div class="card border-primary shadow-sm" id="qr-print">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #1a237e, #283593);">
                            <strong>📷 QR - Horario #<?= $qrHorarioId ?></strong>
                        </div>
                        <div class="card-body text-center bg-white p-4">
                            <img src="<?= $qrImagen ?>" alt="QR Code" class="img-fluid border p-2 bg-light shadow-sm" style="max-width: 220px; border-radius: 10px;">
                            <p class="mt-3 fw-bold mb-1"><?= htmlspecialchars($qrInfo) ?></p>
                            <small class="text-muted">Escanea para marcar asistencia</small>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-secondary" onclick="window.print()">🖨️ Imprimir QR</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <script>
            // Llena el formulario al seleccionar un horario
            function seleccionar(btn) {
                document.getElementById('inputId').value = btn.dataset.id;
                document.getElementById('selectAula').value = btn.dataset.aula;
                document.getElementById('selectGrupo').value = btn.dataset.grupo;
                document.getElementById('selectDia').value = btn.dataset.dia;
                document.getElementById('inputHoraInicio').value = btn.dataset.inicio;
                document.getElementById('inputHoraFin').value = btn.dataset.fin;
            }
        </script>
<?php
        $this->renderFin();
    }
}

// Ejecuta la vista
$vista = new PHorario();
$vista->procesarFormulario();
$vista->mostrarVista();
