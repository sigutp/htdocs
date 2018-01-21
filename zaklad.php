<?php 
    // načtení souboru s funkcemi
    include("database.class.php");
    $PDOObj = new Database();
?>

<?php 
// zaklad stranky

/**
 *  Vytvoreni hlavicky stranky.
 *  @param string $title Nazev stranky.
 */
function head($title=""){    
?>
<!doctype>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <style>
            body { background-color: orange; }
            nav { background-color: darkblue; margin-bottom: 10px; padding: 5px; color:lightgray;}
            nav a { color: aliceblue; padding: 5px;}
        </style>
    </head>
    <body>
        <h1><?php echo $title; ?></h1>
        <div>
            <?php
            if (isset($_SESSION["user"])){
                echo "Přihlášen: " . $_SESSION['user']['login'];
            }
            ?>
        </div>
        <nav>Menu: 
            <a href="index.php?page=0">Login/Logout</a> |
            <a href="index.php?page=1">Registrace</a> |
            <a href="index.php?page=2">Sprava osobních údajů</a> |
            <a href="index.php?page=3">Sprava uživatelů</a> | 
            <?php 
            if (isset($_SESSION["user"])){
                echo "<a href='index.php?page=5'>Příspěvky</a>";
            }            
            ?>
            
        </nav>
        <div>
<?php 
}

/**
 *  Vytvoreni paticky.
 */
function foot(){
?>                
        </div>
    </body>
</html>


<?php
    
}


/**
 *  Vytvori selectbox s pravi uzivatelu.
 *  @param array $rights    Vsechna dostupna prava.
 *  @param integer $selected    Zvolena polozka nebo null.
 *  @return string          Vytvoreny selectbox.
 */
function createSelectBox($rights,$selected){
    $res = '<select name="pravo">';
    foreach($rights as $r){
        if($selected!=null && $selected==$r['idprava']){ // toto bylo ve stupu
            $res .= "<option value='".$r['idprava']."' selected>$r[nazev]</option>";    
        } else { // nemam vstup
            $res .= "<option value='".$r['idprava']."'>$r[nazev]</option>";    
        }        
    }
    $res .= "</select>";
    return $res;
}
?>