
-- Tabla de Tópicos
CREATE TABLE topico (
    ID_topico INT AUTO_INCREMENT PRIMARY KEY,
    nombre_topico VARCHAR(25) NOT NULL UNIQUE 
); 

-- Tabla de Usuario
CREATE TABLE usuario (
    RUT_usuario VARCHAR(10) PRIMARY KEY,
    nombre VARCHAR(250) NOT NULL,
    email VARCHAR(300) NOT NULL UNIQUE,
    tipo_usuario VARCHAR(1),
    password_hash VARCHAR(300) NOT NULL
); 

-- Tabla de Autores
CREATE TABLE autor (
    RUT_autor VARCHAR(10) PRIMARY KEY,
    CONSTRAINT fk_autor_usuario FOREIGN KEY (RUT_autor) REFERENCES usuario (RUT_usuario)
); 
-- Tabla de Revisores
CREATE TABLE revisor (
    RUT_revisor VARCHAR(10) PRIMARY KEY,
    CONSTRAINT fk_revisor_usuario FOREIGN KEY (RUT_revisor) REFERENCES usuario (RUT_usuario)
); 

-- Tabla de JefeComite
CREATE TABLE jefe_comite (
    RUT_jefe VARCHAR(10) PRIMARY KEY,
    CONSTRAINT fk_revisor_jefe FOREIGN KEY (RUT_jefe) REFERENCES usuario (RUT_usuario)
); 


-- Tabla de Artículos
CREATE TABLE articulo (
    ID_articulo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(256) NOT NULL,
    fecha_envio DATE NOT NULL,
    resumen VARCHAR(1024)
); 

-- Tabla de Revisor_Topico
CREATE TABLE revisor_topico (
    RUT_revisor VARCHAR(10) NOT NULL,
    ID_topico INT NOT NULL,
    PRIMARY KEY (RUT_revisor, ID_topico),
    CONSTRAINT fk_revisor_topico_revisor FOREIGN KEY (RUT_revisor) REFERENCES revisor (RUT_revisor),
    CONSTRAINT fk_revisor_topico_topico FOREIGN KEY (ID_topico) REFERENCES topico (ID_topico)
);

-- Tabla de Articulo_Autor
CREATE TABLE articulo_autor (
    ID_articulo INT NOT NULL,
    RUT_autor VARCHAR(10) NOT NULL,
    CONTACT_autor BOOLEAN,
    PRIMARY KEY (ID_articulo, RUT_autor),
    CONSTRAINT fk_articulo_autor_articulo FOREIGN KEY (ID_articulo) REFERENCES articulo (ID_articulo),
    CONSTRAINT fk_articulo_autor_autor FOREIGN KEY (RUT_autor) REFERENCES autor (RUT_autor)
);

-- Tabla de Articulo_Revisor
CREATE TABLE articulo_revisor (
    ID_articulo INT NOT NULL,
    RUT_revisor VARCHAR(10) NOT NULL,
    PRIMARY KEY (ID_articulo, RUT_revisor),
    CONSTRAINT fk_articulo_revisor_articulo FOREIGN KEY (ID_articulo) REFERENCES articulo (ID_articulo),
    CONSTRAINT fk_articulo_revisor_revisor FOREIGN KEY (RUT_revisor) REFERENCES revisor (RUT_revisor)
);
