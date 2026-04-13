<?php
class VistaBase {
    public function renderInicio($titulo = "IglesiaApp") {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title><?= $titulo ?></title>
            <link href="../static/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <nav name="barraNavegacion" class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.php">IglesiaApp</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="PMiembro.php">Miembros</a></li>
                        <li class="nav-item"><a class="nav-link" href="PMatrimonio.php">Matrimonios</a></li>
                        <li class="nav-item"><a class="nav-link" href="PBautismo.php">Bautismos</a></li>
                        <li class="nav-item"><a class="nav-link" href="PCurso.php">Cursos</a></li>
                        <li class="nav-item"><a class="nav-link" href="PMinisterio.php">Ministerios</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
        <?php
    }

    public function renderFin() {
        ?>
        </div>
        </body>
        </html>
        <?php
    }
}
