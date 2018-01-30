<!-- ZÁKLADNÍ ŠABLONA STRÁNKY OBSAHUJÍCÍ HLAVIČKU, TĚLO A PATIČKU -->

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
<!doctype HTML>
<html lang="cs">

<head>
    <meta charset="utf-8">
    <title>
        <?php echo $title; ?>
    </title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="containter">
        <h1>Konference</h1>
        <h2>
            <?php echo $title; ?>
        </h2>



        <nav class="navbar navbar-default">
            <div class="containter-fluid">
                <div class="navbar-header">

                    <?php
                if (isset($_SESSION["user"])){
                    echo "<a class='navbar-brand'>Přihlášen: " . $_SESSION['user']['login'] . "</a>";
                }
                ?>

                </div>

                <ul class="nav navbar-nav">
                    <?php 
            // vidi pouze neprihlaseni uzivatele
            if (!isset($_SESSION["user"])){
                echo "<li><a href='index.php?page=0'>Přihlásit</a></li>";
                echo "<li><a href='index.php?page=1'>Registrace</a></li>";
                echo "<li class='home'><a href='index.php?page=10'><span class='glyphicon glyphicon-home'></span></a></li>";
            }
                         
            // vidi pouze prihlaseni uzivatele
            if (isset($_SESSION["user"])){
                echo "<li><a href='index.php?page=0'>Odhlásit</a></li>";
                echo "<li class='home'><a href='index.php?page=10'><span class='glyphicon glyphicon-home'></span></a></li>";
                echo "<li><a href='index.php?page=2'>Správa osobních údajů</a></li>";
            }                 
                
            // vidi pouze administratori
            if (isset($_SESSION["user"]) && ($_SESSION["user"]["idprava"] == 1)){
                echo "<li><a href='index.php?page=3'>Správa uživatelů</a></li>";
                echo "<li><a href='index.php?page=7'>Správa recenzí</a></li>";
            }
                         
            // vidi pouze recenzenti
            if (isset($_SESSION["user"]) && ($_SESSION["user"]["idprava"] == 2)){
                echo "<li><a href='index.php?page=8'>Moje recenze</a></li>";                
            } 
                         
            // vidi pouze autori
            if (isset($_SESSION["user"]) && ($_SESSION["user"]["idprava"] == 3)){
                echo "<li><a href='index.php?page=5'>Příspěvky</a></li>";                
            }            
            ?>

                </ul>
            </div>
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
    </div>
</body>

</html>


<?php
    
}
