<!-- NOVÝ ČLÁNEK -->

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
<div class="floating">
    <a class="floating" href="index.php?page=5">Zpět</a>
</div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="table-responsive">
            <table class="table">
                <tr>
                    <td class="col-md-1">Název příspěvku:</td>
                    <td class="col-md-1"><input type="text" name="name" id="name" required></td>
                </tr>
                <tr>
                    <td class="col-md-1">Autoři</td>
                    <td class="col-md-1"><input type="text" name="authors" id="authors" required></td>
                </tr>
                <tr>
                    <td class="col-md-1">Abstrakt</td>
                    <td class="col-md-1"><input type="text" name="abstract" id="abstract" required></td>
                </tr>
                <tr>
                    <td class="col-md-1">PDF soubor</td>
                    <td class="col-md-1"><input type="file" name="pdfFile" id="pdfFile" accept="application/pdf"></td>
                </tr>
            </table>
            </div>
            <input type="submit" name="submission" value="Uložit">
        </form>


        <?php

    
    if(isset($_POST['submission'])){
       upload();     
    }
}
?>

<?php
    foot();
?>