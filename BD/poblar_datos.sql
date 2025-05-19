-- Insertar usuarios
-- contraseña: hash indice, ejemplo: hash1. en orden sucesivo

INSERT INTO usuario (RUT_usuario, nombre, email, tipo_usuario, password_hash) VALUES
('10000001', 'Ana Autor', 'ana.autor@example.com', 'A', '$2y$10$xfhEVTTKbZY3r6v7PEt4L.v4Z1etXERrrTkePzrd4Qnv9ALBZn0hi'),
('10000002', 'Bruno Autor', 'bruno.autor@example.com', 'A', '$2y$10$hKn9PIWJoH09vfyibG04GuqZh/dGCgQcpZ0mtz7UvZz6cBtTteEiO'),
('10000003', 'Carla Autor', 'carla.autor@example.com', 'A', '$2y$10$5BFPuCdn.1ax5YFazrJ24upwG5iAj8BccelNUxAu8o5UeZpeGh4eW'),
('10000004', 'Diego Autor', 'diego.autor@example.com', 'A', '$2y$10$n0piEQ61SnP7QpO1FAVetubk5cgZaXwl3TejZWIt55BOAK2InRZzC'),
('10000005', 'Elena Autor', 'elena.autor@example.com', 'A', '$2y$10$uAYbnKrNwPNR/WeJ7NhEheCIKz8KkBHDZ8C4mSRO7idFCwCGlC.Ja'),
('20000001', 'Rafa Revisor', 'rafa.revisor@example.com', 'R', '$2y$10$YkZGyYq/I83FTUObgx5jtu6tljSoEVCXKKhk3RtaqCMUSYihkAZFq'),
('20000002', 'Sofía Revisor', 'sofia.revisor@example.com', 'R', '$2y$10$tT4TyKZp5.mfQ86D0yPgSu9Ncc.DJ7cp.mOS.y6zFzwUj.8ckgwUq'),
('20000003', 'Tomás Revisor', 'tomas.revisor@example.com', 'R', '$2y$10$2pgAIFNKhvDn9HZ.QZG2Ae.x8MSPjM5W25DPdrL5FlOr2Oj8tuNya'),
('20000004', 'Valentina Revisor', 'valentina.revisor@example.com', 'R', '$2y$10$Hbm1gaCe.dYX40jN6O4UluBKmI52z6HK.s6X9Kv7vGmdX6eFcpCi2'),
('20000005', 'Walter Revisor', 'walter.revisor@example.com', 'R', '$2y$10$fD.RCZuDdTtAz6ChG6.DNeZuVb0dcJpncdv7E9LZKg68eMdyjbA2S'),
-- contraseña admin: 'admin'
('1', 'admin', 'admin@admin', 'J', '$2y$10$w/axXKBggYpBrFEtvw56veS8vbl7t61RSjzm1NyAk48V.ddevUPbe');

-- Insertar tópicos
INSERT INTO topico (nombre_topico) VALUES
('IA'), ('Redes'), ('Seguridad'), ('Bases de Datos'), ('Computación Cuántica');

-- Insertar artículos
INSERT INTO articulo (titulo, fecha_envio, resumen) VALUES
('Artículo sobre IA', '2025-05-01', 'Resumen 1'),
('Redes Avanzadas', '2025-05-02', 'Resumen 2'),
('Criptografía Moderna', '2025-05-03', 'Resumen 3'),
('Big Data y BD', '2025-05-04', 'Resumen 4'),
('Algoritmos Cuánticos', '2025-05-05', 'Resumen 5'),
('Aprendizaje Profundo', '2025-05-06', 'Resumen 6'),
('Seguridad en IoT', '2025-05-07', 'Resumen 7'),
('Gestión de Datos', '2025-05-08', 'Resumen 8'),
('Quantum Networking', '2025-05-09', 'Resumen 9'),
('Redes 6G', '2025-05-10', 'Resumen 10');

-- Asignar autores a artículos
INSERT INTO articulo_autor VALUES
(1, '10000001', TRUE), (2, '10000002', TRUE), (3, '10000003', TRUE),
(4, '10000004', TRUE), (5, '10000005', TRUE), (6, '10000001', FALSE),
(7, '10000002', FALSE), (8, '10000003', FALSE), (9, '10000004', FALSE),
(10, '10000005', FALSE);

-- Asignar revisores a artículos
INSERT INTO articulo_revisor (ID_articulo, RUT_revisor) VALUES
(1, '20000001'), -- Tópico 1 → Revisor 20000001 (ok)
(1, '20000002'), -- Tópico 1 → Revisor 20000002 (ok)

(2, '20000002'), -- Tópico 2 → Revisor 20000002 (ok)
(3, '20000003'), -- Tópico 3 → Revisor 20000003 (ok)
(4, '20000004'), -- Tópico 4 → Revisor 20000004 (ok)
(5, '20000005'), -- Tópico 5 → Revisor 20000005 (ok)
(6, '20000001'), -- Tópico 1 → Revisor 20000001 (ok)
(7, '20000003'), -- Tópico 3 → Revisor 20000003 (ok)
(8, '20000004'), -- Tópico 4 → Revisor 20000004 (ok)
(9, '20000002'), -- Tópico 5 → Revisor 20000002 (tiene tópico 5 también, ok)
(10, '20000004');-- Tópico 2 → Revisor 20000004 (no tenía tópico 2, así que NO OK)

-- Asignar tópicos a artículos
INSERT INTO articulo_topico (ID_articulo, ID_topico) VALUES
(1, 1),  -- Artículo 1: IA
(1, 2),  -- Artículo 1: redes

(2, 2),  -- Redes
(3, 3),  -- Seguridad
(4, 4),  -- Bases de datos
(5, 5),  -- Computación cuántica
(6, 1),  -- IA
(7, 3),  -- Seguridad
(8, 4),  -- Bases de datos
(9, 5),  -- Computación cuántica
(10, 2); -- Redes

-- Asignar tópicos a revisores
INSERT INTO revisor_topico (RUT_revisor, ID_topico) VALUES
('20000001', 1),  -- Rafa revisor: IA
('20000002', 2),  -- Sofía: Redes
('20000003', 3),  -- Tomás: Seguridad
('20000004', 4),  -- Valentina: BD
('20000005', 5),  -- Walter: Cuántica

-- Revisores con más de un tópico (si revisan más de un artículo de distinto tema)
('20000001', 3),  -- Rafa también sabe de Seguridad
('20000002', 5),  -- Sofía también sabe de Cuántica
('20000004', 1);  -- Valentina también sabe de IA