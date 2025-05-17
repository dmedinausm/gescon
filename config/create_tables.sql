
-- Tabla de Usuario
CREATE TABLE usuario (
    RUT_usuario VARCHAR(10) PRIMARY KEY,
    nombre VARCHAR(250) NOT NULL,
    email VARCHAR(300) NOT NULL UNIQUE,
    tipo_usuario VARCHAR(1),
    password_hash VARCHAR(300) NOT NULL
); 

-- Tabla de Tópicos
CREATE TABLE topico (
    ID_topico INT AUTO_INCREMENT PRIMARY KEY,
    nombre_topico VARCHAR(25) NOT NULL UNIQUE 
); 


-- Tabla de Artículos
CREATE TABLE articulo (
    ID_articulo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(256) NOT NULL,
    fecha_envio DATE NOT NULL,
    resumen VARCHAR(1024)
); 

CREATE TABLE evaluacion (
    ID_revision INT AUTO_INCREMENT PRIMARY KEY,
    nombre_revision VARCHAR(150) NOT NULL,
    calidad_tecnica INT NOT NULL CHECK (calidad_tecnica BETWEEN 1 AND 5),
    originalidad INT NOT NULL CHECK (originalidad BETWEEN 1 AND 5),
    valoracionGlobal INT NOT NULL CHECK (valoracionGlobal BETWEEN 1 AND 5),
    argumentos VARCHAR(300) NOT NULL,
    comentario VARCHAR(300)
);


-- Tabla de Revisor_Topico
CREATE TABLE revisor_topico (
    RUT_revisor VARCHAR(10) NOT NULL,
    ID_topico INT NOT NULL,
    PRIMARY KEY (RUT_revisor, ID_topico),
    CONSTRAINT fk_revisor_topico_revisor FOREIGN KEY (RUT_revisor) REFERENCES usuario (RUT_usuario),
    CONSTRAINT fk_revisor_topico_topico FOREIGN KEY (ID_topico) REFERENCES topico (ID_topico)
);

-- Tabla de Articulo_Autor
CREATE TABLE articulo_autor (
    ID_articulo INT NOT NULL,
    RUT_autor VARCHAR(10) NOT NULL,
    is_contact BOOLEAN,
    PRIMARY KEY (ID_articulo, RUT_autor),
    CONSTRAINT fk_articulo_autor_articulo FOREIGN KEY (ID_articulo) REFERENCES articulo (ID_articulo),
    CONSTRAINT fk_articulo_autor_autor FOREIGN KEY (RUT_autor) REFERENCES usuario (RUT_usuario)
);

-- Tabla de Articulo_Revisor
CREATE TABLE articulo_revisor (
    RUT_revisor VARCHAR(10) NOT NULL,
    ID_articulo INT NOT NULL,
    PRIMARY KEY (ID_articulo, RUT_revisor),
    CONSTRAINT fk_articulo_revisor_articulo FOREIGN KEY (ID_articulo) REFERENCES articulo (ID_articulo),
    CONSTRAINT fk_articulo_revisor_revisor FOREIGN KEY (RUT_revisor) REFERENCES usuario (RUT_usuario)
);

CREATE TABLE revision_articulo (
    ID_articulo INT NOT NULL,
    ID_revision INT NOT NULL,
    PRIMARY KEY (ID_revision, ID_articulo),
    CONSTRAINT fk_articulo FOREIGN KEY (ID_articulo) REFERENCES articulo (ID_articulo),
    CONSTRAINT fk_revision FOREIGN KEY (ID_revision) REFERENCES evaluacion (ID_revision)
); 

-- Tabla de Articulo_Topico
CREATE TABLE articulo_topico (
    ID_articulo INT NOT NULL,
    ID_topico INT NOT NULL,
    PRIMARY KEY (ID_articulo, ID_topico),
    CONSTRAINT fk_articulo_topico_articulo FOREIGN KEY (ID_articulo) REFERENCES articulo (ID_articulo),
    CONSTRAINT fk_articulo_topico_topico FOREIGN KEY (ID_topico) REFERENCES topico (ID_topico)
);