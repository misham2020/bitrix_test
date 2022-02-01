<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
    <div>
        <a href="javascript: void(0)">обновление таблицы</a>
    </div> <br><br>

    <form class="add-form" action="index.php" id="form">
        <input type="hidden" name="id" value="<?=$_GET['id']?>"><br>
        Имя<br>
        <input type="text" name="name" value="<?=$arResult["SELECT"]["NAME"]?>"><br>
        EMAIL<br>
        <input type="text" name="mail" value="<?=$arResult["SELECT"]["EMAIL"]?>"><br>
        ADDRESS_ID<br>
        <input type="text" name="address_id" value="<?=$arResult["SELECT"]["ADDRESS_ID"]?>"><br>
        <button type="submit">Отправить форму</button>
    </form>

