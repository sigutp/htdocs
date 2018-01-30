<!-- ÚPRAVA ČLÁNKU AUTOREM -->

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
        <!-- formulář pro upravení článku -->
        <div class="floating">
            <a class="floating" href="index.php?page=5">Zpět</a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="article_id" value="<?php echo $id ?>" />
            <div class='table-responsive'>
                <table class='table'>

                    <tr>
                        <td class="col-md-1">Datum</td>
                        <td class="col-md-1">
                            <?php echo $article['time']?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-1">Název příspěvku</td>
                        <td class="col-md-1"><input type="text" name="name" id="name" value="<?php echo $article['name']?>" required></td>
                    </tr>
                    <tr>
                        <td class="col-md-1">Autoři</td>
                        <td class="col-md-1"><input type="text" name="authors" id="authors" value="<?php echo $article['authors']?>" required></td>
                    </tr>
                    <tr>
                        <td class="col-md-1">Abstrakt</td>
                        <td class="col-md-1"><input type="text" name="abstract" id="abstract" value="<?php echo $article['abstract']?>" required></td>
                    </tr>
                    <tr>
                        <td class="col-md-1">PDF soubor</td>
                        <td class="col-md-1">
                            <?php echo $article['fileName']?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-1">Změnit PDF soubor</td>
                        <td class="col-md-1"><input type="file" name="pdfFile" id="pdfFile" accept="application/pdf"></td>
                    </tr>
                </table>
            </div>
            <input type="submit" name="submission" value="Uložit">
        </form>


        <?php

    // po zmáčknutí tlačítka "Uložit"
    if(isset($_POST['submission'])){       
        reupload($id);
        header("Location: index.php?page=5");
    }
}
?>

            <?php
    foot();
?>
