CREATE OR REPLACE VIEW vista_articulo_info AS
SELECT 
    a.ID_articulo,
    a.titulo,
    GROUP_CONCAT(DISTINCT u_autor.nombre SEPARATOR ', ') AS autores,
    GROUP_CONCAT(DISTINCT t.nombre_topico SEPARATOR ', ') AS topicos,
    GROUP_CONCAT(DISTINCT CONCAT(u_rev.nombre, '||', u_rev.RUT_usuario) SEPARATOR ';;') AS revisores
FROM articulo a
LEFT JOIN articulo_autor aa ON a.ID_articulo = aa.ID_articulo
LEFT JOIN usuario u_autor ON aa.RUT_autor = u_autor.RUT_usuario
LEFT JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
LEFT JOIN topico t ON at.ID_topico = t.ID_topico
LEFT JOIN articulo_revisor ar ON a.ID_articulo = ar.ID_articulo
LEFT JOIN usuario u_rev ON ar.RUT_revisor = u_rev.RUT_usuario
GROUP BY a.ID_articulo;
