<!-- NOVÁ RECENZE -->

<?php 
// nacteni hlavicky stranky
include("zaklad.php");
head("Nová recenze");
?>

<?php

if(!$PDOObj->isUserLogged() && ($_SESSION["user"]["idprava"] == 2)){ // neni prihlasen
    ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
    <b>Tato stránka je dostupná pouze přihlášeným recenzentům.</b>
    <?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
} else {
    if(isset($_POST['submission'])){         
       $PDOObj->submitReview($_POST['review_id_submit'], $_POST['merit'], $_POST['accuracy'], $_POST['language'], $_POST['comment']);
        header("Location: index.php?page=8");
    }    
    
    $article_id = $_POST['article_id'];
    $review_id = $_POST['review_id'];
    
    $article = $PDOObj->fetchArticle($article_id);
    $review = $PDOObj->fetchWholeReview($review_id);
?>
        <div>
            <a href="index.php?page=8">Zpět</a>
        </div>

        <div>
            <h2>Článek</h2>
            <table>
                <tr>
                    <td>Název</td>
                    <td>
                        <?php echo $article['name']?>
                    </td>
                </tr>
                <tr>
                    <td>Autoři</td>
                    <td>
                        <?php echo $article['authors']?>
                    </td>
                </tr>
                <tr>
                    <td>Abstrakt</td>
                    <td>
                        <?php echo $article['abstract']?>
                    </td>
                </tr>
                <tr>
                    <td>Čas</td>
                    <td>
                        <?php echo $article['time']?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $article['fileName']?>
                    </td>
                    <td>
                        <?php 
                        $authorName = $review['author_name'];
                        $fileName = $article['fileName'];
                        $fileName = str_replace(" ", "%20", $fileName);
    
                        echo "<a target='_blank' href=articles/$authorName/$fileName>Stáhnout</a>" ?></td>
                </tr>
            </table>
        </div>

        <div>
            <h2>Recenze</h2>
            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tr>
                        <td>Přínos/Originalita</td>
                        <td>
                            <select name='merit'>                                               
                                <option value='5' <?php if ($review['merit'] == 5) echo "selected"; ?>>5 - Vlastní myšlenka</option>";
                                <option value='4' <?php if ($review['merit'] == 4) echo "selected"; ?>>4 - Mírně inspirovaný</option>";
                                <option value='3' <?php if ($review['merit'] == 3) echo "selected"; ?>>3 - Silně inspirovaný</option>";
                                <option value='2' <?php if ($review['merit'] == 2) echo "selected"; ?>>2 - Silně neoriginální</option>";
                                <option value='1' <?php if ($review['merit'] == 1) echo "selected"; ?>>1 - Převzatá myšlenka</option>";
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Faktická správnost</td>
                        <td>
                            <select name='accuracy'>                                               
                                <option value='5' <?php if ($review['accuracy'] == 5) echo "selected"; ?>>5 - Bezchybný</option>";
                                <option value='4' <?php if ($review['accuracy'] == 4) echo "selected"; ?>>4 - S drobnějšími chybami</option>";
                                <option value='3' <?php if ($review['accuracy'] == 3) echo "selected"; ?>>3 - Nepřesný</option>";
                                <option value='2' <?php if ($review['accuracy'] == 2) echo "selected"; ?>>2 - Značně nepřesný</option>";
                                <option value='1' <?php if ($review['accuracy'] == 1) echo "selected"; ?>>1 - Chybný</option>";
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Jazyková stránka</td>
                        <td>
                            <select name='language'>                                               
                                <option value='5' <?php if ($review['language'] == 5) echo "selected"; ?>>5 - Bezchybný</option>";
                                <option value='4' <?php if ($review['language'] == 4) echo "selected"; ?>>4 - S drobnějšími chybami</option>";
                                <option value='3' <?php if ($review['language'] == 3) echo "selected"; ?>>3 - Občasné hrubky</option>";
                                <option value='2' <?php if ($review['language'] == 2) echo "selected"; ?>>2 - Časté hrubky</option>";
                                <option value='1' <?php if ($review['language'] == 1) echo "selected"; ?>>1 - Nečitelný</option>";
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Poznámky
                        </td>
                        <td>
                            <textarea name='comment' rows='8' cols='32'>
                                <?php echo $review['comment']; ?>    
                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type='hidden' name='review_id_submit' value='<?php echo $review_id ?>' />
                            <input type="submit" name="submission" value="Uložit" />
                        </td>
                        <tr>
                </table>
            </form>
        </div>

        <?php    
    
}
?>

        <?php
    foot();
?>
