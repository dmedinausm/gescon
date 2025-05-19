-- un function para que cuente los revisores de un articulo, así marcarlo cuando tiene <2

DROP FUNCTION IF EXISTS contar_revisores;

CREATE FUNCTION contar_revisores(ID INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE cantidad INT;

    SELECT COUNT(*) INTO cantidad
    FROM articulo_revisor
    WHERE ID_articulo = ID;

    RETURN cantidad;
END;
