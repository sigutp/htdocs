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
        $res = $PDOObj->userLogin($_REQUEST["login"],$_REQUEST["heslo"]);    
        if(!$res){
            echo "<b>Přihlášení se nezdařilo!<b><br><br>";
        } else {
            header("Location: index.php?page=0");
        }
        
    }
    
    // je uzivatel aktualne prihlasen
    if(!$PDOObj->isUserLogged()){ // neni prihlasen
   ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
        <b>Přihlášení uživatele</b>
        <form action="" method="POST">
            <table>
                <tr><td>Login:</td><td><input type="text" name="login"></td></tr>
                <tr><td>Heslo:</td><td><input type="password" name="heslo"></td></tr>
            </table>
            <input type="hidden" name="action" value="login">
            <input type="submit" name="potvrzeni" value="Přihlásit">
        </form>
        
<?php           
   ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
    } else { // je prihlasen
   ///////////// PRO PRIHLASENE UZIVATELE ///////////////        
?>
        <b>Přihlášený uživatel</b><br>
<?php echo "Jméno: ".$_SESSION["user"]["jmeno"]."<br>
            Login: ".$_SESSION["user"]["login"]."<br>
            E-mail: ".$_SESSION["user"]["email"]."<br>
            Právo: ".$_SESSION["user"]["nazev"]."<br>";        
?>
        <br>
        
        Odhlášení uživatele:
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
             