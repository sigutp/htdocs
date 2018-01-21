<?php 
// nacteni hlavicky stranky
include("zaklad.php");
include("upload.php");
$PDOObj = new Database();

head("Upravit příspěvek");
?>

<?php

if(!$PDOObj->isUserLogged()){ // neni prihlasen
    ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
    <b>Tato stránka je dostupná pouze přihlášeným uživatelům.</b>
    <?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
} else {
    $id = $_POST['article_id'];    
    $article = $PDOObj->fetchArticle($id);    
?>
<div>
    <a href="index.php?page=5">Zpět</a>
</div>
        
        <b>Upravit příspěvek</b>
        <form action="" method="POST" enctype="multipart/form-data">   
            <input type="hidden" name="article_id" value="<?php echo $id ?>"/>
            <table>
                
                <tr>
                    <td>Datum:</td>
                    <td><?php echo $article['time']?></td>
                </tr>
                <tr>
                    <td>Název příspěvku:</td>
                    <td><input type="text" name="name" id="name" value="<?php echo $article['name']?>" required></td>
                </tr>
                <tr>
                    <td>Autoři</td>
                    <td><input type="text" name="authors" id="authors" value="<?php echo $article['authors']?>" required></td>
                </tr>
                <tr>
                    <td>Abstrakt</td>
                    <td><input type="text" name="abstract" id="abstract" value="<?php echo $article['abstract']?>" required></td>
                </tr>
                <tr>
                    <td>PDF soubor</td>
                    <td><?php echo $article['fileName']?></td>
                </tr>
                <tr>
                    <td>Změnit PDF soubor</td>
                    <td><input type="file" name="pdfFile" id="pdfFile" accept="application/pdf"></td>
                </tr>
            </table>

            <input type="submit" name="submission" value="Uložit">
        </form>


        <?php

    
    if(isset($_POST['submission'])){       
        reupload($id);
        header("Location: index.php?page=5");
    }
}
?>