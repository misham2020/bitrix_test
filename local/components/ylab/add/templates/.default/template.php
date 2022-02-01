<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
    <div>
        <a href="javascript: void(0)">добавление в таблицу</a>
    </div> <br><br>
    <form class="add-form" action="index.php" id="form">
    Имя<br>
    <input type="text" name="name"><br>
    EMAIL<br>
    <input type="text" name="mail"><br>
    ADDRESS_ID<br>
        <input type="text" name="address_id"><br>
    <button type="submit">Отправить форму</button>
</form>

