<?php 
// nacteni hlavicky stranky
include("zaklad.php");
include("upload.php");
$PDOObj = new Database();

head("Příspěvky");


$articles = $PDOObj->getArticles();

echo "<table>" .
    "<tr>
        <th>Název</th>
        <th>Autoři</th> 
        <th>Čas</th>
    </tr>";

while($row = $articles->fetch()){
    echo 
        "<tr>" .
            "<td>" . $row['name'] . "</td>" .
            "<td>" . $row['authors'] . "</td>" . 
            "<td>" . $row['time'] . "</td>" .
            "<td>
                <form action='index.php?page=6' method='POST'>
                    <input type='hidden' name='article_id' value=$row[id] />
                    <input type='submit' name='editButton' value='Upravit' article_id='$row[id]'>                    
                </form>               
            </td>" .
            "<td>
                <form action='' method='POST'>
                    <input type='hidden' name='article_id' value=$row[id] />
                    <input type='submit' name='deleteButton' value='Odstranit'>                    
                </form>
            </td>" .
        "</tr>";
}

echo "</table>";
?> 

<?php
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

<div>
    <a href="index.php?page=4">Nový článek</a>    
</div>
