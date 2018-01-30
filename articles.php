<!-- ČLÁNKY PŘIHLÁŠENÉHO AUTORA -->

<?php 
// nacteni hlavicky stranky
include("zaklad.php");
include("upload.php");
$PDOObj = new Database();

head("Příspěvky");


$articles = $PDOObj->getArticles();

// tabulka článků napsaných přihlášeným autorem
echo "<div class='table-responsive'><table class='table'>" .
    "<tr>
        <th>Název</th>
        <th>Autoři</th> 
        <th>Čas</th>
        <th colspan=2>Akce</th>        
        <th>Stav</th>
        <th>Hodnocení</th>
    </tr>";

while($row = $articles->fetch()){
    echo 
        "<tr>" .
            "<td class='col-md-1'>" . $row['name'] . "</td>" .
            "<td class='col-md-1'>" . $row['authors'] . "</td>" . 
            "<td class='col-md-1'>" . $row['time'] . "</td>" .
            "<td class='col-md-1 buttonField'>
                <form action='index.php?page=6' method='POST'>
                    <input type='hidden' name='article_id' value=$row[id] />
                    <input type='submit' name='editButton' value='Upravit' article_id='$row[id]'>                    
                </form>               
            </td>" .
            "<td class='col-md-1 buttonField'>
                <form action='' method='POST'>
                    <input type='hidden' name='article_id' value=$row[id] />
                    <input type='submit' name='deleteButton' value='Odstranit'>                    
                </form>
            </td>" .
            "<td class='col-md-1'>";
        if ($row['article_state'] == 'fresh'){
            echo "Nehodnocen";
        } else if ($row['article_state'] == 'accepted'){
            echo "Přijat";
        } else if ($row['article_state'] == 'refused'){
            echo "Odmítnut";
        }
        
    echo    "</td>";
        if($row['article_state'] == 'accepted' || $row['article_state'] == 'refused'){
            echo "<td class='col-md-1'>" . round($PDOObj->averageRating($row['id']), 2) . "</td>";
        }
    echo "</tr>";
}

echo "</table>";
?>

<?php

// kaskáda kontroly odstranění článku
if (isset($_POST['deleteButton'])) 
{ 
    $id = $_POST['article_id'];
    echo
        "<div id='delete_article'>
            <form action='' method='POST'>
                <input type='hidden' name='id' value='$id'/>
                <table>
                    <tr>
                        <td>Vážně si přejete odstranit vybraný článek?</td>
                        <td><input type='submit' name='deleteConfirmation' value='Ano'></td>
                    </tr>
                </table>
            </form>
        </div>";        
} 

if (isset($_POST['deleteConfirmation'])){
    deleteFile($PDOObj->deleteArticle($_POST['id'])); 
    header("Location: index.php?page=5");
    }
?>
    <!-- odkaz na nový článek -->
    <div class="floating">
        <a class="floating" href="index.php?page=4">Nový článek</a>
    </div>

    <?php
    foot();
?>
