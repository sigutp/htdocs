<!-- PŘIHLÁŠENÍ A ODHLÁŠENÍ -->

<?php 
    // nacteni hlavicky stranky
    include("zaklad.php");
    head("Přihlášení a odhlášení uživatele");
?>


<?php 
    // načtení souboru s funkcemi
    //include("database.class.php");
    //$PDOObj = new Database();
?>

<?php
   //// zpracovani odeslanych formularu
    // odhlaseni uzivatele
    if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="logout"){
        $PDOObj->userLogout();
        header("Location: index.php?page=0");
    }
    // prihlaseni uzivatele
    if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="login"){
        if (!($PDOObj->isUserBlocked($_REQUEST["login"]))){
            $res = $PDOObj->userLogin($_REQUEST["login"],$_REQUEST["heslo"]);    
            if(!$res){
                echo "<b>Přihlášení se nezdařilo!<b><br><br>";
            } else {
                header("Location: index.php?page=10");
            }
        } else {
            echo "
                <div class='alert alert-danger'>
                    <strong>Pozor!</strong> Zablokovaní uživatelé se nemohou přihlásit!
                </div>
            ";
        }
    }
    
    // je uzivatel aktualne prihlasen
    if(!$PDOObj->isUserLogged()){ // neni prihlasen
   ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
    <form action="" method="POST">
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <td class='col-md-1'>Login:</td>
                    <td class='col-md-1'><input type="text" name="login"></td>
                </tr>
                <tr>
                    <td class='col-md-1'>Heslo:</td>
                    <td class='col-md-1'><input type="password" name="heslo"></td>
                </tr>
            </table>
        </div>
        <input type="hidden" name="action" value="login">
        <input type="submit" name="potvrzeni" value="Přihlásit">
    </form>

    <?php           
   ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
    } else { // je prihlasen
   ///////////// PRO PRIHLASENE UZIVATELE ///////////////        
?>
    <h3>Přihlášený uživatel</h3>
    <?php echo "
        <div class='table-responsive'>
            <table class='table'>
                <tr>
                    <td class='col-md-1'>Jméno</td>
                    <td class='col-md-1'>" . $_SESSION['user']['jmeno'] . "</td>
                </tr>
                <tr>
                    <td class='col-md-1'>Login</td>
                    <td class='col-md-1'>" . $_SESSION['user']['login'] . "</td>
                </tr>
                <tr>
                    <td class='col-md-1'>E-mail</td>
                    <td class='col-md-1'>" . $_SESSION['user']['email'] . "</td>
                </tr>
                <tr>
                    <td class='col-md-1'>Práva</td>
                    <td class='col-md-1'>" . $_SESSION['user']['nazev'] . "</td>
                </tr>
            </table>
        </div>
        ";
?>
    <form action="" method="POST">
        <input type="hidden" name="action" value="logout">
        <input type="submit" name="potvrzeni" value="Odhlásit">
    </form>



    <?php
   ///////////// KONEC: PRO PRIHLASENE UZIVATELE ///////////////                
    }
?>

        <?php
    // paticka
    foot();
?>
