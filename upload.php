<!-- PRÁCE SE SOUBORY -->

<?php

/**
* Nahraje soubor dle atributů uvedených v proměnné $_POST.
* Vloží informace o článku a souboru do databáze.
*/
function upload(){
          
    if (uploadFile()){
        global $PDOObj;
        $name = $_POST['name'];
        $authors = $_POST['authors'];
        $abstract = $_POST['abstract'];
        $fileName = basename($_FILES["pdfFile"]["name"]);
      
        $PDOObj->addArticle($name, $authors, $abstract, $fileName);
    }
}

/**
* Zkontroluje, jestli soubor není moc velký.
* Zkontroluje, jestli je soubor ve formátu PDF.
* Nahraje soubor dle atributů uvedených v proměnné $_POST.
* Přemístí soubor do složky articles, podsložky označené loginem právě přihlášeného uživatele.
* @return úspěšnost operace
*/
function uploadFile(){
    $name = $_POST['name'];
    $authors = $_POST['authors'];
    $abstract = $_POST['abstract'];
    $fileName = basename($_FILES["pdfFile"]["name"]);
        
    $target_dir = "articles/" . $_SESSION["user"]["login"] . "/" ;
    
    // pokud slozka jeste neexistuje, zalozi ji
    if (!file_exists ($target_dir)){
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . $fileName;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Soubor již existuje.";
        $uploadOk = 0;
    }
    // Check file size
    // 100 MB
    if ($_FILES["pdfFile"]["size"] > 100000000000) {
        echo $_FILES["pdfFile"]["size"];
        echo "Soubor je příliš velký.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($fileType != "pdf") {
        echo "Lze nahrávat pouze soubory ve formátu PDF.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Soubor nebyl nahrán.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $target_file)) {           
            echo "<div class='alert alert-success alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'><span class='glyphicon glyphicon-remove'></span></a>
                        Soubor <strong>". basename( $_FILES["pdfFile"]["name"]). "</strong> byl úspěšně nahrán.
                    </div>";
            return true;
        } else {
            echo "<div class='alert alert-danger alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'><span class='glyphicon glyphicon-remove'></span></a>
                        Soubor nebyl nahrán.
                    </div>";
            return false;
        }
    }
}

/**
* Odstraní soubor.
* @param fileName název souboru
*/
function deleteFile($fileName){            
    $target_dir = "articles/" . $_SESSION["user"]["login"] . "/" ;
    $target_file = $target_dir . $fileName;
        
    chmod($target_file, 0777);       
    if(unlink($target_file)){
        echo "<div class='alert alert-success alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'><span class='glyphicon glyphicon-remove'></span></a>
                        Odstranění bylo úspěšné.
                    </div>";
    }        
}

/**
* Znovu nahraje článek.
* Pokud se změnil soubor, nahradí se novým.
* Aktualizuje údaje v databázi.
* @param article_id ID článku
*/
function reupload($article_id){
    global $PDOObj;
    $name = $_POST['name'];
    $authors = $_POST['authors'];
    $abstract = $_POST['abstract'];
    $fileName = $_POST['fileName'];
    
    if(empty($_FILES['pdfFile'])){
        // nedelej nic
    } else {
        uploadFile();
        
        $oldData = $PDOObj->fetchArticle($article_id);        
        deleteFile($oldData['fileName']);
        
        $fileName = basename($_FILES["pdfFile"]["name"]);       
    }
    
    $PDOObj->updateArticle($article_id, $name, $authors, $abstract, $fileName);
}
?>
