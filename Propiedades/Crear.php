<?php

//Base de datos
require '../Database/database.php';
$db = conectarDB();

//Consultar la base de datos
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);


//Array con mensajes de errores
$errores = [];
$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedorId = '';

//Ejecutar el código después de que el usuario envia el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo "<pre>";
  var_dump($_POST);
  echo "</pre>";

  $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
  $precio = mysqli_real_escape_string($db, $_POST['precio']);
  $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
  $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
  $wc = mysqli_real_escape_string($db, $_POST['wc']);
  $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
  $vendedorId = mysqli_real_escape_string($db, $_POST['vendedorId']);
  $creado = date('Y/m,d');

  // Asignar file hacia una variable
  $imagen = $_FILES['imagen'];

  if (!$titulo) {
    $errores[] = "Debes añadir un título";
  }
  if (!$precio) {
    $errores[] = "Debes añadir un precio";
  }
  if (strlen($descripcion) < 50) {
    $errores[] = "Debes añadir una descripcion con al menos 50 caracteres";
  }
  if (!$habitaciones) {
    $errores[] = "Debes añadir la cantidad de habitaciones";
  }
  if (!$wc) {
    $errores[] = "Debes añadir la cantidad de baños";
  }
  if (!$estacionamiento) {
    $errores[] = "Debes añadir la cantidad de lugares de estacionamientos";
  }
  if (!$estacionamiento) {
    $errores[] = "Debes elegir un vendedor";
  }
  if (!$imagen['name'] || $imagen['error']) {
    $errores[] = "La imagen es obligatoria";
  }

  //Validar por tamaño (100 kb máximo)
  //$medida = 1000 * 100;

  //if($imagen['size'] = $medida){
  //    $errores[] = "La imagen es muy pesada";
  //}

  //echo "<pre>";
  //var_dump($errores);
  //echo "</pre>";

  //Revisar que el arreglo de errores esté vacío
  if (empty($errores)) {

    //Subida de archivos

    //Crear carpeta
    $carpetaImagenes = '../imagenes/';

    if (!is_dir($carpetaImagenes)) {
      mkdir($carpetaImagenes);
    }

    //Generar un nombre unico
    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

    //Subir la imagen
    move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);

    // Insertar en la base de datos
    $query = " INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, 
        wc, estacionamiento, creado, vendedorId ) 
        VALUES ('$titulo', '$precio', '$nombreImagen','$descripcion', '$habitaciones',
        '$wc', '$estacionamiento', '$creado', '$vendedorId') ";

    echo $query;

    $resultado = mysqli_query($db, $query);

    if ($resultado) {
      //Redireccionar al usuario

      header('Location: /index.php?resultado=1');
    }
  }
}
?>
<a href="../index.php"></a>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bienes Raices</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>

<body>
  <header class="header">
    <div class="barra">
      <a href="../index.php">
        <img src="../img/logo.svg" alt="Logotipo de Bienes Raices" />
      </a>
    </div>
  </header>

  <main class="contenedor seccion">
    <h1>Crear propiedad</h1>

    <?php foreach ($errores as $error) : ?>
      <div class="alerta error">
        <?php echo $error; ?>
      </div>
    <?php endforeach; ?>

    <a href="../index.php" class="boton boton-verde">Volver</a>

    <form class="formulario" method="POST" action="Crear.php" enctype="multipart/form-data">
      <fieldset>
        <legend>Información general</legend>

        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" placeholder="Título de la propiedad" value="<?php echo $titulo ?>">

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" placeholder="Precio de la propiedad" value="<?php echo $precio ?>">

        <label for=" iamgen">Imagen:</label>
        <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion"><?php echo $descripcion ?></textarea>
      </fieldset>

      <fieldset>
        <legend>Información de la propiedad</legend>

        <label for="habitaciones">Habitaciones:</label>
        <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones ?>">

        <label for=" wc">Baños:</label>
        <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc ?>">

        <label for=" estacionamiento">Estacionamiento:</label>
        <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento ?>">

      </fieldset>

      <fieldset>
        <legend>Vendedor</legend>

        <select name=" vendedorId">
          <option value="">>--Seleccione--<<</option>
              <?php while ($row = mysqli_fetch_assoc($resultado)) : ?>
          <option <?php echo $vendedorId === $row["id"] ? 'selected' : ''; ?> value="<?php echo $row['id']; ?>"> <?php echo $row['nombre'] . ' ' . $row['apellido']; ?></option>
        <?php endwhile ?>
        </select>

      </fieldset>

      <input type="submit" value="Crear Propiedad" class="boton boton-verde">

    </form>
  </main>

  <footer class="footer seccion">
    <p class="copyright">
      Todos los derechos Reservados
      <?php echo date('Y'); ?>
      &copy;
    </p>
  </footer>
</body>

</html>
