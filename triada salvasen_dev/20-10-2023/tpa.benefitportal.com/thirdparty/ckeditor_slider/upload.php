<?php	
  $HOST = "https://" . $_SERVER['HTTP_HOST'];
	if(isset($_FILES['upload']) && $_FILES['upload']['name'] != ""){
    $HOST = "https://" . $_SERVER['HTTP_HOST'];
		$path = dirname(__DIR__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'ckeditor'.DIRECTORY_SEPARATOR;    
		move_uploaded_file($_FILES['upload']['tmp_name'], $path.$_FILES['upload']['name']);
		echo "<img src='".$HOST."/uploads/ckeditor/".$_FILES['upload']['name']."' height='150' />";
	}
?>