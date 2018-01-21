<?php 
// nacteni hlavicky stranky
include("zaklad.php");
include("upload.php");
head("Nový článek");
?>

<?php

if(!$PDOObj->isUserLogged()){ // neni prihlasen
    ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
    <b>Tato stránka je dostupná pouze přihlášeným uživatelům.</b>
    <?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
} else {
?>
<div>
    <a href="index.php?page=5">Zpět</a>
</div>
        
        <b>Nový příspěvek</b>
        <form action="" method="POST" enctype="multipart/form-data">
            <table>
                <tr>
                    <td>Název příspěvku:</td>
                    <td><input type="text" name="name" id="name" required></td>
                </tr>
                <tr>
                    <td>Autoři</td>
                    <td><input type="text" name="authors" id="authors" required></td>
                </tr>
                <tr>
                    <td>Abstrakt</td>
                    <td><input type="text" name="abstract" id="abstract" required></td>
                </tr>
                <tr>
                    <td>PDF soubor</td>
                    <td><input type="file" name="pdfFile" id="pdfFile" accept="application/pdf"></td>
                </tr>
            </table>

            <input type="submit" name="submission" value="Uložit">
        </form>


        <?php

    
    if(isset($_POST['submission'])){
       upload();     
    }
}
?>