CREATE TABLE mensaje_revisor (
    RUT_usuario VARCHAR(10) PRIMARY KEY,
    mostrar_mensaje BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_mensaje_revisor_usuario FOREIGN KEY (RUT_usuario) REFERENCES usuario(RUT_usuario)
);


DELIMITER $$

CREATE TRIGGER trigger_promocion_revisor
AFTER UPDATE ON usuario
FOR EACH ROW
BEGIN
    IF OLD.tipo_usuario = 'A' AND NEW.tipo_usuario = 'R' THEN
        INSERT INTO mensaje_revisor (RUT_usuario, mostrar_mensaje)
        VALUES (NEW.RUT_usuario, TRUE)
        ON DUPLICATE KEY UPDATE mostrar_mensaje = TRUE;
    END IF;
END$$

DELIMITER ;