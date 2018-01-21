<?php
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
            echo "Soubor ". basename( $_FILES["pdfFile"]["name"]). " byl úspěšně nahrán.";
            return true;
        } else {
            echo "Soubor nebyl nahrán.";
            return false;
        }
    }
}

function deleteFile($fileName){            
    $target_dir = "articles/" . $_SESSION["user"]["login"] . "/" ;
    $target_file = $target_dir . $fileName;
        
    chmod($target_file, 0777);       
    if(unlink($target_file)){
        echo "Odstranění bylo úspěšné";
    }        
}

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
