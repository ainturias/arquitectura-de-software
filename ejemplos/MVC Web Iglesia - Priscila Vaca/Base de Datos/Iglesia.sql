create database Iglesia;
use Iglesia;

-- Tabla: Cargo
CREATE TABLE Cargo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL 
);

-- Tabla: Ministerio
CREATE TABLE Ministerio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla: Miembro
CREATE TABLE Miembro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    cargo_id INT,
	ministerio_id INT,
    FOREIGN KEY (cargo_id) REFERENCES Cargo(id),
    FOREIGN KEY (ministerio_id) REFERENCES Ministerio(id)
);

-- Tabla: Culto
CREATE TABLE Culto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE,
    hora TIME,
    tipo_culto BOOLEAN
);

-- Tabla: ParticipacionMinisterial
CREATE TABLE ParticipacionMinisterial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    culto_id INT,
    ministerio_id INT,
    FOREIGN KEY (culto_id) REFERENCES Culto(id),
    FOREIGN KEY (ministerio_id) REFERENCES Ministerio(id)
);

-- Tabla: Diezmo
CREATE TABLE Diezmo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monto DECIMAL(10,2),
    culto_id INT,
    FOREIGN KEY (culto_id) REFERENCES Culto(id)
);

-- Tabla: Actividad
CREATE TABLE Actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion TEXT,
    tipo_actividad VARCHAR(50)
);

-- Tabla: CertificadoActividad
CREATE TABLE CertificadoActividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE,
    actividad_id INT,
    miembro_id INT,
    FOREIGN KEY (actividad_id) REFERENCES Actividad(id),
    FOREIGN KEY (miembro_id) REFERENCES Miembro(id)
);
