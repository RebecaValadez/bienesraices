<?php

//Importar la conexón
require 'Database/database.php';
$db = conectarDB();

//Escribir el query
$query = "SELECT * FROM propiedades";

//Consultar la base DB
$resultadoConsulta = mysqli_query($db, $query);

//Muestra un mensaje condicional
$resultado = $_GET['resultado'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $id = filter_var($id, FILTER_VALIDATE_INT);

  if ($id) {

    //Eliminar archivo
    $query = "SELECT imagen FROM propiedades WHERE id = $id";
    $resultado = mysqli_query($db, $query);
    $propiedad = mysqli_fetch_assoc($resultado);
    unlink('imagenes/' . $propiedad['imagen']);

    //Eliminar propiedad
    $query = "DELETE FROM propiedades WHERE id = $id";
    $resultado = mysqli_query($db, $query);

    if ($resultado) {
      header('Location: ?resultado=3');
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bienes Raices</title>
  <link rel="stylesheet" href="CSS/style.css" />
</head>

<body>
  <header class="header">
    <div class="barra">
      <a href="index.php">
        <img src="img/logo.svg" alt="Logotipo de Bienes Raices" />
      </a>
    </div>
  </header>
  <main class="contenedor seccion">
    <h1>Administrador de propiedades en venta</h1>

    <?php if ($resultado == 1) : ?>
      <p class="alerta exito">Propiedad creada correctamente</p>
    <?php elseif ($resultado == 2) : ?>
      <p class="alerta exito">Propiedad actualizada correctamente</p>
    <?php elseif ($resultado == 3) : ?>
      <p class="alerta exito">Propiedad eliminada correctamente</p>
    <?php endif ?>
    <a href="./Propiedades/Crear.php" class="boton boton-verde">Nueva Propiedad</a>

    <table class="propiedades">
      <thead>
        <th>ID</th>
        <th>Título</th>
        <th>Imagen</th>
        <th>Precio</th>
        <th>Acción</th>
      </thead>

      <tbody>
        <!-- Mostrar los resultados -->

        <?php while ($propiedad = mysqli_fetch_assoc($resultadoConsulta)) : ?>
          <tr>
            <td><?php echo $propiedad['id'] ?></td>
            <td><?php echo $propiedad['titulo'] ?></td>
            <td> <img src="imagenes/<?php echo $propiedad['imagen'] ?>" class="imagen-tabla" alt="imagen"> </td>
            <td>$<?php echo $propiedad['precio'] ?></td>
            <td>
              <form method="POST" class="w-100">
                <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">
                <input type="submit" class="boton-rojo-block" value="Eliminar">
              </form>
              <!-- <a href="/Propiedades/Actualizar.php?php echo $propiedad['id'] ?>" class="boton-amarillo-block">Actualizar</a> -->
              <a href="Propiedades/Actualizar.php?id=<?php echo $propiedad['id'] ?>" class="boton-amarillo-block">Actualizar</a>
            </td>
          </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </main>

  <?php
  //Cerrar la conexión esto es opcional
  mysqli_close($db);
  ?>
  <footer class="footer seccion">
    <p class="copyright">
      Todos los derechos Reservados
      <?php echo date('Y'); ?>
      &copy;
    </p>
  </footer>
</body>

</html>