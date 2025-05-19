<?php
include 'config\db.php';

$sql = "
    SELECT u.RUT_usuario, u.nombre, u.email, GROUP_CONCAT(t.nombre_topico SEPARATOR ', ') AS topicos
    FROM usuario u
    LEFT JOIN revisor_topico rt ON u.RUT_usuario = rt.RUT_revisor
    LEFT JOIN topico t ON rt.ID_topico = t.ID_topico
    WHERE u.tipo_usuario = 'R'
    GROUP BY u.RUT_usuario
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Revisores</title>
    <link rel="stylesheet" href="PHP/src/styles/revisores.css">
</head>
<body>

    <div class="container">
        <div class="header">
            <a href="?page=main" class="back-button">← Volver al menú</a>
            <h2>Lista de Revisores</h2>
            <a href="?page=agregar_revisores"class="add-button">+ Añadir miembro</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>RUT</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tópicos</th>
                    <th>Acciones</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['RUT_usuario'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['topicos'] ?: 'Ninguno' ?></td>
                    <td>
                        <a href="?page=editar_revisores&rut=<?= $row['RUT_usuario'] ?>" class="action-link">Editar</a> |
                        <a href="?page=eliminar_revisores&rut=<?= $row['RUT_usuario'] ?>" onclick="return confirm('¿Estás seguro?')" class="action-link delete">Eliminar</a>
                    </td>
                    <td>
                        <a href="?page=detalle_revisor&id=<?= $row['RUT_usuario'] ?>" class="detail-link">Ver Detalle</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
