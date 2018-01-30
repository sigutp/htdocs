<!-- SPRÁVA UŽIVATELŮ ADMINISTRÁTOREM -->

<?php 
    // nacteni hlavicky stranky
    include("zaklad.php");
    head("Správa uživatelů");
?>


<?php 
    // načtení souboru s funkcemi
    //include("database.class.php");
    //$PDOObj = new Database();
?>

<?php
           
    if(!$PDOObj->isUserLogged()){ // neni prihlasen
   ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
    <b>Tato stránka je dostupná pouze přihlášeným uživatelům.</b>
    <?php
   ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
    } else { // je prihlasen
        //print_r($_SESSION["user"]);
        if($_SESSION["user"]["idprava"]!=1){ // neni admin
   ///////////// PRO PRIHLASENE UZIVATELE - NENI ADMIN ///////////////                
?>
        <b>Správu uživatelů mohou provádět pouze uživatelé s právem Administrátor.</b>
        <?php
   ///////////// KONEC: PRO PRIHLASENE UZIVATELE - NENI ADMIN ///////////////                
        } else { // je admin
   ///////////// PRO PRIHLASENE UZIVATELE - JE ADMIN ///////////////                            
            // zpracovani odeslanych formularu
            if(isset($_POST["potvrzeni"]) && isset($_POST["user-id"])){
                // zadost o smazani uzivatele
                if($_POST["user-id"]!=""){
                    $res = $PDOObj->deleteUser($_POST["user-id"]);
                    if($res){
                        echo "<b>Uživatel s ID:".$_POST["user-id"]." byl smazán.</b><br><br>";
                    } else {
                        echo "<b>Uživatele s ID:".$_POST["user-id"]." se nepodařilo smazat!</b><br><br>";
                    }
                } else {
                    echo "<b>Neznámé ID uživatele. Mazání nebylo provedeno!</b><br><br>";
                }
            }
            
            // zablokování uživatele
            if (isset($_POST["block_user"])){
                if($PDOObj->blockUser($_POST["user-id"])){
                    echo "<div class='alert alert-success alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'><span class='glyphicon glyphicon-remove'></span></a>
                        Uživatel úspěšně zablokován.
                    </div>";
                }
            }
            
            // odblokování uživatele
            if (isset($_POST["unblock_user"])){
                if($PDOObj->unblockUser($_POST["user-id"])){
                    echo "<div class='alert alert-success alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'><span class='glyphicon glyphicon-remove'></span></a>
                        Uživatel úspěšně odblokován.
                    </div>";
                }
            }
            
            // změna práv uživatele
            if (isset($_POST["change_role"])){
                if($PDOObj->changeRole($_POST['user-id'], $_POST['user_role'])){
                    echo "<div class='alert alert-success alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'><span class='glyphicon glyphicon-remove'></span></a>
                        Práva uživatele úspěšně změněna.
                    </div>";
                };
            }

?>
            <h3>Seznam uživatelů</h3>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Jméno</th>
                        <th>E-mail</th>
                        <th colspan=2>Právo</th>
                        <th colspan=2>Akce</th>
                    </tr>
                    <?php  
                $users = $PDOObj->allUsersInfo(); // vsichni uzivatele
                foreach($users as $u){
                    if($u["iduzivatel"]!=$_SESSION["user"]["iduzivatel"]){ // aktualni uzivatele nevypisuju
                        echo "<tr><td class='col-md-1'>$u[iduzivatel]</td><td class='col-md-1'>$u[login]</td><td class='col-md-1'>$u[jmeno]</td><td class='col-md-1'>$u[email]</td>
                                
                                    <form action='' method='POST'>";
                                        ?>
                    <td class='col-md-1'>
                        <select name='user_role'>                                               
                                                <option value='1' <?php if ($u['idprava'] == 1) echo "selected"; ?>>Administrátor</option>";
                                                <option value='2' <?php if ($u['idprava'] == 2) echo "selected"; ?>>Recenzent</option>";
                                                <option value='3' <?php if ($u['idprava'] == 3) echo "selected"; ?>>Autor</option>";                
                                            </select>
                    </td>

                    <?php echo "
                                        <input type='hidden' name='user-id' value='$u[iduzivatel]'>
                                        <td class='col-md-1'><input type='submit' name='change_role' value='Změnit'></td>
                                        <td class='col-md-1'>";
                                        
                                        if ($u["blocked"]==0){
                                            echo "<input type='submit' name='block_user' value='Zablokovat'>";
                                        } else {
                                            echo "<input type='submit' name='unblock_user' value='Odblokovat'>";
                                        }
                        echo            "<td class='col-md-1'><input type='submit' name='potvrzeni' value='Smazat'></td>
                                    </form>
                                
                              </tr>";
                    }
                }
            ?>
                </table>
                <?php

   ///////////// KONEC: PRO PRIHLASENE UZIVATELE - JE ADMIN ///////////////                
        }
    }
?>

                    <?php
    // paticka
    foot();
?>
