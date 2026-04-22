<?php
require_once 'VistaBase.php';
require_once '../negocio/NHorario.php';
require_once '../negocio/NAula.php';
require_once '../negocio/NGrupo.php';

class PHorario extends VistaBase
{
    private NHorario $negocioHorario;
    private NAula $negocioAula;
    private NGrupo $negocioGrupo;

    private ?int $id_horario = null;
    private ?int $id_aula = null;
    private ?int $id_grupo = null;
    private ?int $dia_semana = null;
    private string $hora_inicio = '';
    private string $hora_fin = '';
    private string $mensaje = '';

    // Atributos para el QR
    private ?string $qrImagen = null;
    private ?int $qrHorarioId = null;
    private string $qrInfo = '';

    private array $diasSemana = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
        4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];

    public function __construct()
    {
        $this->negocioHorario = new NHorario();
        $this->negocioAula = new NAula();
        $this->negocioGrupo = new NGrupo();
    }

    // Enrutador de acciones
    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            $this->id_horario = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;
            $this->id_aula = isset($_POST['id_aula']) && is_numeric($_POST['id_aula']) ? (int) $_POST['id_aula'] : null;
            $this->id_grupo = isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo']) ? (int) $_POST['id_grupo'] : null;
            $this->dia_semana = isset($_POST['dia_semana']) && is_numeric($_POST['dia_semana']) ? (int) $_POST['dia_semana'] : null;
            $this->hora_inicio = $_POST['hora_inicio'] ?? '';
            $this->hora_fin = $_POST['hora_fin'] ?? '';

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
        if ($this->id_aula && $this->id_grupo && $this->dia_semana && $this->hora_inicio && $this->hora_fin) {
            $resultado = $this->negocioHorario->crear($this->id_aula, $this->id_grupo, $this->dia_semana, $this->hora_inicio, $this->hora_fin);
            if ($resultado !== 'ok') {
                $this->mensaje = "<div class='alert alert-danger'>$resultado</div>";
            }
        }
    }

    private function editar(): void
    {
        if ($this->id_horario !== null && $this->id_aula && $this->id_grupo && $this->dia_semana && $this->hora_inicio && $this->hora_fin) {
            $resultado = $this->negocioHorario->editar($this->id_horario, $this->id_aula, $this->id_grupo, $this->dia_semana, $this->hora_inicio, $this->hora_fin);
            if ($resultado !== 'ok') {
                $this->mensaje = "<div class='alert alert-danger'>$resultado</div>";
            }
        }
    }

    private function eliminar(): void
    {
        if ($this->id_horario !== null) {
            try {
                $this->negocioHorario->eliminar($this->id_horario);
            } catch (Exception $e) {
                $this->mensaje = "<div class='alert alert-danger'>No se puede eliminar: el horario tiene asistencias asociadas.</div>";
            }
        }
    }

    private function listarHorarios(): array
    {
        return $this->negocioHorario->listar();
    }

    private function listarAulas(): array
    {
        return $this->negocioAula->listar();
    }

    private function listarGrupos(): array
    {
        return $this->negocioGrupo->listar();
    }

    /**
     * Prepara los datos necesarios para mostrar el código QR al costado de la tabla.
     * Se activa cuando un usuario hace click en el botón "VER QR" (pasa un parámetro 'qr' por URL).
     */
    private function prepararQR(array $horarios): void
    {
        // 1. Verificamos si en la URL existe el parámetro '?qr=ID'
        if (isset($_GET['qr']) && is_numeric($_GET['qr'])) {
            $id = (int)$_GET['qr'];

            // 2. Pedimos al negocio toda la información ya procesada (Imagen + Texto)
            $data = $this->negocioHorario->obtenerDatoCompletoQR($id);

            // 3. Cargamos los atributos de la vista
            $this->qrHorarioId = $data['id'];
            $this->qrImagen    = $data['imagen'];
            $this->qrInfo      = $data['etiqueta'];
        }
    }

    public function mostrarVista(): void
    {
        $horarios = $this->listarHorarios();
        $aulas = $this->listarAulas();
        $grupos = $this->listarGrupos();
        $this->prepararQR($horarios);

        $this->renderInicio("Gestión de Horarios");
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

        <div class="container-fluid py-3">
            <h2 class="mb-4">Gestionar Horarios</h2>

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
                            <div class="col-md-2">
                                <label class="text-muted small fw-bold">AULA</label>
                                <select name="id_aula" id="selectAula" class="form-select">
                                    <option value="">-- Aula --</option>
                                    <?php foreach ($aulas as $a): ?>
                                        <option value="<?= $a['id_aula'] ?>"><?= htmlspecialchars($a['codigo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small fw-bold">GRUPO</label>
                                <select name="id_grupo" id="selectGrupo" class="form-select">
                                    <option value="">-- Grupo --</option>
                                    <?php foreach ($grupos as $g): ?>
                                        <option value="<?= $g['id_grupo'] ?>">
                                            <?= htmlspecialchars($g['materia'] . ' / ' . $g['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="text-muted small fw-bold">DÍA</label>
                                <select name="dia_semana" id="selectDia" class="form-select">
                                    <option value="">-- Día --</option>
                                    <?php foreach ($this->diasSemana as $num => $nombre): ?>
                                        <option value="<?= $num ?>"><?= $nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="text-muted small fw-bold">HORA INICIO</label>
                                <input type="time" name="hora_inicio" id="inputHoraInicio" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="text-muted small fw-bold">HORA FIN</label>
                                <input type="time" name="hora_fin" id="inputHoraFin" class="form-control">
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

            <!-- Tabla y QR lado a lado -->
            <div class="row">
                <!-- Listado de horarios -->
            <div class="<?= $this->qrImagen ? 'col-md-7' : 'col-12' ?>">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">
                        <strong>LISTADO DE HORARIOS</strong>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($horarios)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>AULA</th>
                                            <th>MATERIA</th>
                                            <th>GRUPO</th>
                                            <th>DÍA</th>
                                            <th>HORA</th>
                                            <th class="text-end">ACCIÓN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($horarios as $h): ?>
                                            <tr class="<?= ($this->qrHorarioId == $h['id_horario']) ? 'table-active' : '' ?>">
                                                <td><?= $h['id_horario'] ?></td>
                                                <td><?= htmlspecialchars($h['aula_codigo']) ?></td>
                                                <td><?= htmlspecialchars($h['sigla'] . ' - ' . $h['nombre_materia']) ?></td>
                                                <td><strong><?= htmlspecialchars($h['grupo_nombre']) ?></strong></td>
                                                <td><?= $this->diasSemana[$h['dia_semana']] ?? $h['dia_semana'] ?></td>
                                                <td><?= $h['hora_inicio'] ?> - <?= $h['hora_fin'] ?></td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                                        data-id="<?= $h['id_horario'] ?>" data-aula="<?= $h['id_aula'] ?>"
                                                        data-grupo="<?= $h['id_grupo'] ?>" data-dia="<?= $h['dia_semana'] ?>"
                                                        data-inicio="<?= $h['hora_inicio'] ?>" data-fin="<?= $h['hora_fin'] ?>">
                                                        SELECCIONAR
                                                    </button>
                                                    <a href="PHorario.php?qr=<?= $h['id_horario'] ?>" class="btn btn-sm btn-outline-info">VER QR</a>
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

            <?php if ($this->qrImagen): ?>
                <div class="col-md-5">
                    <div class="card border-primary shadow-sm" id="qr-print">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #1a237e, #283593);">
                            <strong>QR - Horario #<?= $this->qrHorarioId ?></strong>
                        </div>
                        <div class="card-body text-center bg-white p-4">
                            <img src="<?= $this->qrImagen ?>" alt="QR Code" class="img-fluid border p-2 bg-light shadow-sm" style="max-width: 220px; border-radius: 10px;">
                            <p class="mt-3 fw-bold mb-1"><?= htmlspecialchars($this->qrInfo) ?></p>
                            <small class="text-muted">Escanea para marcar asistencia</small>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-secondary" onclick="window.print()">Imprimir QR</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        </div>

        <script>
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

// Inicialización y ejecución
$vista = new PHorario();
$vista->procesarFormulario();
$vista->mostrarVista();
