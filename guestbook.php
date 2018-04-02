<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 11.03.2018
 * Time: 15:28
 */
include "Dbase.php";
ini_set('display_errors', 'On');
error_reporting('E_ALL');
$guestb = new Dbase;

if (isset($_REQUEST["NewMessage"])) {
    $guestb->NewMessage($_REQUEST["user"], $_REQUEST["msgtext"]);
    unset($_REQUEST["NewMessage"]);
} // новое сообщение - добавить в базу
if (isset($_REQUEST["ChangeMessageTrue"])) {
    $guestb->EditMessage($_REQUEST["id"], $_REQUEST["msgtext"]);
//    unset($_REQUEST['ChangeMessageTrue']);
}    //если меняем - изменить
if (isset($_REQUEST["DelMessage"])) {
    $guestb->DeleteMessage($_REQUEST["id"]);
//    unset($_REQUEST['DelMessage']);
}    // удаляем сообщение
if (isset($_REQUEST["AddCommentTrue"])) {
    $guestb->NewComment($_REQUEST["id"], $_REQUEST["commenttext"]);
//    unset($_REQUEST['AddCommentTrue']);
} // новый камент - добавить в базу
if (isset($_REQUEST["DeleteComment"])) {
    $guestb->DeleteComment($_REQUEST["commentid"]);
}    //если нажали удаление - удалить коммент
?>
<!DOCTYPE html>
<html><body>
    <form name="NewMessage" action="guestbook.php" method="post">
        <h2>Добавить сообщение:</h2>
        Ваше имя:<input type="text" name="user"><br>
        Текст:<textarea name="msgtext" placeholder="Введите свое сообщение"></textarea>
        <input type="hidden" name="NewMessage">
        <input type="submit" value="Отправить">
    </form>

<?php
$rawarr = $guestb->getAllMessages(); // вынимаем все сообщения с каментами
//echo "<pre>" . print_r($rawarr, true) . "</pre>";
$previous_id = '';// если встречаем такой же - пропускаем печать сообщения (а печатаем только комментарии)
foreach ($rawarr as $key => $value) {
    if ($value['messageid'] != $previous_id) { // такое сообщение еще не встречалось
        ?>

        <div id="message" style="border: solid 1px burlywood;">
        <form name="message<?= $key ?>" action="guestbook.php" method="post">
            <b><?= $value['username'] ?></b><br>
            <i><?= $value['messagedate'] ?></i><br>

            <? if (isset($_REQUEST["ChangeMessage"]) && ($_REQUEST['id'] == $value['messageid']))
            { // сообщение будем редактировать - показываем форму
                ?>
                <textarea name="msgtext" autofocus><?= $value['text'] ?></textarea> <br>
                <input type="submit" name="ChangeMessageTrue" value="Отправить">
            <? } else { // сообщение не редактируем, а показываем
                echo $value['text']; ?>
                <br>
                <input type="submit" name="ChangeMessage" value="Изменить">
            <? } // показываем текст или форму редактирования сообщения
            // а в конце - кнопку "Добавить ответ"
            ?>

            <input type="submit" name="DelMessage" value="Удалить">
            <input type="hidden" name="id" value="<?= $value['messageid'] ?>">
            <input type="submit" name="AddComment" value="Ответить"><br>
            <? if (isset($_REQUEST["AddComment"]) && ($_REQUEST['id'] == $value['messageid']))
            { // форма добавления комментария
                ?>
                <textarea name="commenttext" placeholder="Добавьте комментарий" autofocus></textarea> <br>
                <input type="submit" name="AddCommentTrue" value="Отправить">

            <?php } // форма добавления комментария
            ?>
        </form>
        </div> <!--закрываем div message-->
        <?
    }// такое сообщение еще не встречалось - напечатали его
    //выводим только комментарии к текущему сообщению
    ?>
    <div id="comments" style="border: solid 1px beige;  background: #eee; margin-left: 10px ">
    <form name="comment<?= $key.'_'.$value['commentid'] ?>" action="guestbook.php" method="get">

        <?
        // будем добавлять коммент - показываем форму
        { // ничего не добавляем, а
            // должны отобразить все ответы к данному сообщению
            if ($value['comment']) {
                // вот тут
                echo $value['commentid'] . " " . $value['comment'] . " " . $value['commentdate'] . " ";
                echo '<input type="hidden" name="commentid"  value="';
                echo $value['commentid'];
                echo '" >';
                echo '<input type="submit" name="DeleteComment" value="x">';
                echo "<br>\r\n";
            }
        } ?>
    </form>
    </div> <!-- закрываем div comments -->
    <?php

    $previous_id = $value['messageid'];

} //вывести сообщения ?>
</body></html>