<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .menu-card { transition: transform 0.15s; border: none; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,.1); }
        .menu-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,.15); }
        .menu-card a { text-decoration: none; color: #333; }
    </style>
</head>
<body>
    <!-- Cabecera principal -->
    <div class="text-white py-4" style="background: linear-gradient(135deg, #1a237e, #283593);">
        <div class="container">
            <h1 class="mb-1">📋 Control de Asistencia</h1>
            <p class="mb-0 opacity-75">Sistema de gestión para la universidad</p>
        </div>
    </div>

    <!-- Menú con tarjetas -->
    <div class="container mt-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card menu-card p-4">
                    <a href="presentacion/PMateria.php">
                        <h5>📚 Materia</h5>
                        <p class="text-muted mb-0">Registrar y gestionar materias</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card menu-card p-4">
                    <a href="presentacion/PAula.php">
                        <h5>🏫 Aula</h5>
                        <p class="text-muted mb-0">Gestionar aulas y códigos QR</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card menu-card p-4">
                    <a href="presentacion/PEstudiante.php">
                        <h5>🎓 Estudiante</h5>
                        <p class="text-muted mb-0">Registrar estudiantes</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card menu-card p-4">
                    <a href="presentacion/PGrupo.php">
                        <h5>👥 Grupo</h5>
                        <p class="text-muted mb-0">Crear grupos por materia</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card menu-card p-4">
                    <a href="presentacion/PHorario.php">
                        <h5>🕐 Horario</h5>
                        <p class="text-muted mb-0">Programar clases en las aulas</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card menu-card p-4">
                    <a href="presentacion/PInscripcion.php">
                        <h5>📝 Inscripción</h5>
                        <p class="text-muted mb-0">Inscribir estudiantes a grupos</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
