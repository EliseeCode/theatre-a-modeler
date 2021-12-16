<?php
 $fileName = $_POST["audio-filename"];
 $uploadDirectory = "../uploads/local2web/".$fileName;
		

     if (isset($_FILES["audio-blob"])) {

       
	
        if (!move_uploaded_file($_FILES["audio-blob"]["tmp_name"], $uploadDirectory)) {
            echo("problem moving uploaded file");
        }

        echo($uploadDirectory);
    }


    

?>