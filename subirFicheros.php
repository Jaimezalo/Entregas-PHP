<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es" dir="ltr">
	<head>
		<meta charset="utf-8">
		<style type="text/css">
			p{
			 color:red;
			}
		</style>
	</head>
	<body>

<?php

$formulario = <<<FORM
<h2> Subida de ficheros al servidor Web</h2>
<!-- el atributo enctype del form debe valer "multipart/form-data" -->
<!-- el atributo method del form debe valer "post" -->
<form name="f1" enctype="multipart/form-data" action="subirFicheros.php" method="post">

<input type="hidden" name="MAX_FILE_SIZE" value="200000"/>

<input name="archivo1" type="file" /> <br />
<input name="archivo2" type="file" /> <br />

<input type="submit" value="Subir archivo" />
</form>
FORM;

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    echo $formulario;
    
}
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    //if((isset($_POST['archivo1'])or(isset($_POST['archivo2'])){
    
    $codigosErrorSubida= [ 
        0 => 'Subida correcta',
        1 => '<p>El tamaño del archivo excede el admitido por el servidor</p>',  // directiva upload_max_filesize en php.ini
        2 => '<p>El tamaño del archivo excede el admitido por el cliente</p>',  // directiva MAX_FILE_SIZE en el formulario HTML
        3 => '<p>El archivo no se pudo subir completamente</p>',
        4 => '<p>No se seleccionó ningún archivo para ser subido</p>',
        6 => '<p>No existe un directorio temporal donde subir el archivo</p>',
        7 => '<p>No se pudo guardar el archivo en disco</p>',  // permisos
        8 => '<p>Una extensión PHP evito la subida del archivo</p>'  // extensión PHP
    ];
    
    $mensaje = '';
    $directorioSubida = '/home/jaime/Escritorio/imguser'; // se reciben el directorio de alojamiento y el archivo
    
    $nombreFichero1   = $_FILES['archivo1']['name'];
    $tipoFichero1     = $_FILES['archivo1']['type'];
    $tamanioFichero1  = $_FILES['archivo1']['size'];
    $temporalFichero1 = $_FILES['archivo1']['tmp_name'];
    $errorFichero1    = $_FILES['archivo1']['error'];
 
    $nombreFichero2   = $_FILES['archivo2']['name'];
    $tipoFichero2     = $_FILES['archivo2']['type'];
    $tamanioFichero2  = $_FILES['archivo2']['size'];
    $temporalFichero2 = $_FILES['archivo2']['tmp_name'];
    $errorFichero2    = $_FILES['archivo2']['error'];
    
//Si no se indica ninguno de los archivos, se recarga el formulario
    
    
    

    
   
    
    $mensaje .= ComprobarTipo($nombreFichero1);
    $mensaje .= ComprobarTipo($nombreFichero2);
    $mensaje .= ComprobarTamanioCliente($tamanioFichero1);
    $mensaje .= ComprobarTamanioCliente($tamanioFichero2);
    $mensaje .= ComprobarTamanioServidor($tamanioFichero1, $tamanioFichero2);
    $mensaje .= ComprobarExiste($nombreFichero1, $directorioSubida);
    $mensaje .= ComprobarExiste($nombreFichero2, $directorioSubida);
    
    

    if ( is_dir($directorioSubida) && is_writable ($directorioSubida)) {
        //Intento mover el archivo temporal al directorio indicado
        if (move_uploaded_file($temporalFichero1,  $directorioSubida .'/'. $nombreFichero1) == true) {
            $mensaje .= 'Archivo '.$nombreFichero1. ' guardado en: '.$directorioSubida .'<br/>';        
        } else {
            $mensaje .= '<p>ERROR '.$errorFichero1 .': '.$codigosErrorSubida[$errorFichero1].'</p>';  
        }
            
        if (move_uploaded_file($temporalFichero2,  $directorioSubida .'/'. $nombreFichero2) == true) {
            $mensaje .= 'Archivo 1 guardado en: ' . $directorioSubida .'/'. $nombreFichero2 . ' <br />';            
        } else {
            $mensaje .= '<p>ERROR '.$errorFichero2 .': '.$codigosErrorSubida[$errorFichero2].'</p>';
        }
    } else {
    $mensaje .= '<p>ERROR: No es un directorio correcto o no se tiene permiso de escritura </p>';        
    }


    $mensaje .= '<h3>RESULTADO FICHERO 1</h3>';
    $mensaje .= "<ul><li> Nombre: $nombreFichero1".'</li>';
    $mensaje .= '<li> Tamaño: '.($tamanioFichero1 / 1024).'KB</li>';
    $mensaje .= "<li> Tipo: $tipoFichero1" . '</li>' ;
    $mensaje .= "<li> Nombre archivo temporal: ".$temporalFichero1.'</li>';
    $mensaje .= "<li> Código de estado: ".$errorFichero1.'</li></ul><hr>';
    
    $mensaje .= '<h3>RESULTADO FICHERO 2</h3>';
    $mensaje .= "<ul><li> Nombre: $nombreFichero2" . '</li>';
    $mensaje .= '<li> Tamaño: '.($tamanioFichero2 / 1024) . ' KB</li>';
    $mensaje .= "<li> Tipo: $tipoFichero2" . '</li>' ;
    $mensaje .= "<li> Nombre archivo temporal: ".$temporalFichero2.'</li>';
    $mensaje .= "<li> Código de estado: ".$errorFichero2.'</li></ul>';
    

    echo $mensaje;
    }

    //Función que comprueba si el tipo de fichero es el requerido
    function ComprobarTipo($fichero) { 
        $explode = explode('.', $fichero);
        $extension = $explode[1];
        
        if (($extension != 'JPG') && ($extension != 'PNG')){      
            if(($extension != 'jpg') && ($extension != 'png')){
            return '<p>El tipo de archivo '.$extension.' no es el requerido</p>';
            }
        }
    }
    
    //Función que comprueba si el tamaño de fichero es el requerido
    function ComprobarTamanioCliente($tamanioFichero) {
     
        if ($tamanioFichero > 200000){
            return '<p>El tamaño del archivo excede el admitido por el cliente</p>';            
        }
    }

    //Función que comprueba si el tamaño total de los fichero es el requerido 
    function ComprobarTamanioServidor($tamanioFichero1, $tamanioFichero2){
        
        $tamanioFicheros = $tamanioFichero1 + $tamanioFichero2;
        
        if ($tamanioFicheros > 300000){
            return '<p>El tamaño del archivo excede el admitido por el servidor</p>';
        }
    }
    
    //Función que comprueba si existe un fichero en el directorio con el mismo nombre
    function ComprobarExiste($nombreFichero, $directorioSubida) {
        $arrayFicheros = scandir($directorioSubida);
        foreach ($arrayFicheros as $valor){
            if($valor == $nombreFichero){
                return '<p>Ya existe un fichero con el nombre:  '.$nombreFichero.'</p>';
            }
        }
    }


?>
</body>
</html>