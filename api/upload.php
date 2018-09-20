<?php
include_once 'includes.php';

$MAXFILESIZE		= 4500000; // (free limit 4194304 octets ?)
$images_folder		= "../test_images";

$file 	= (object) array(
	"name" 		=> $_SERVER['HTTP_FILE_NAME'],
	"size"		=> $_SERVER['HTTP_FILE_SIZE'],
	"type"		=> $_SERVER['HTTP_FILE_TYPE'],
	"message"	=> $_SERVER['HTTP_MESSAGE'],
);

$extentions	= array(
	"jpeg"	=> "image/jpeg",
	"png"	=> "image/png",
	"gif"	=> "image/gif"
);

$rep = new Response;
$rep->ok();

if (!($file_ext = array_search($file->type, $extentions)))
{
	$rep->nok("Extention du fichier \"$file->name\" invalide (jpg, png, gif acceptés)");
}
if ($rep->status && $file->size > $MAXFILESIZE)
{
	$rep->nok("Taille du fichier \"$file->name\" trop grande (". ($MAXFILESIZE / 1000000) ." MB maximum)");
}

if ($rep->status)
{
	$file_name	= md5(time());
	$file_path	= "$images_folder/$file_name.$file_ext";
	$fd					= fopen($file_path, "w");

	$rep->nok("Une erreur est arrivée pendant l'écriture ");

	if ($fd && fwrite($fd, file_get_contents("php://input")))
	{
		$image_size		= getimagesize($file_path);
		$rep->nok("Image trop grande: ". $image_size[0] ." x ". $image_size[1] ." = ". ($image_size[0] * $image_size[1] / 1000) ."K (max: ". $MAXFILESIZE / 1000 ."K)");
		if ($image_size[0] * $image_size[1] < $MAXFILESIZE)
		{
			$rep->nok("Echec de la creation de la miniature");
			if (create_thumbnail($images_folder, $file_name, $file_ext))
			{
				SQL::start();
				$data = array(
					"name" => $file->message,
					"data" => "{\"filename\": \"$file_name\", \"extention\": \"$file_ext\"}",
					"type" => "image_test"
				);
				$item = new Data($data);
				$query = new Query(EQueryCommand::INSERT);
				$query->exec($item);
				$rep->data = SQL::last_insert_id();
				$rep->ok("Image enregistrée");
				SQL::close();
			}
		}
	}
	fclose($fd);
}

if ($json["status"] === false && file_exists($file_path))
{
	unlink($file_path);
}

/* SENDING RESPONSE */
header('Content-Type: application/json');
echo json_encode($rep);
exit;


function create_thumbnail($folder, $file_name, $file_ext)
{
	$file_path		= "$folder/$file_name.$file_ext";
	$thumb_path		= "$folder/thumbnails/$file_name.jpeg";
	$img_size 		= getimagesize($file_path);

	switch ($file_ext)
	{
		case 'jpeg': $image	= imagecreatefromjpeg($file_path);
			break;
		case 'png': $image	= imagecreatefrompng($file_path);
			break;
		case 'gif':	$image	= imagecreatefromgif($file_path);
			break;
		default: return false;
	}

	$thumb_width 	= 220;
	$thumb_height	= 300;
	$width 				= imagesx($image);
	$height 			= imagesy($image);

	if ($width / $height >= $thumb_width / $thumb_height)
	{
	   $new_height	= $thumb_height;
	   $new_width 	= $width / ($height / $thumb_height);
	}
	else
	{
	   $new_width 	= $thumb_width;
	   $new_height 	= $height / ($width / $thumb_width);
	}

	$thumb 	= imagecreatetruecolor($thumb_width, $thumb_height);
	$white 	= imagecolorallocate($thumb, 255, 255, 255);
	imagefilledrectangle($thumb, 0, 0, $thumb_width, $thumb_height, $white);

	imagecopyresampled(
		$thumb,	$image,
		0 - ($new_width - $thumb_width) / 2,
		0 - ($new_height - $thumb_height) / 2,
		0, 0,
		$new_width, $new_height,
		$width, $height
	);

	imagejpeg($thumb, $thumb_path, 75);
	imagedestroy($image);
	return true;
}


?>
