<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
?>
<table border="1">
    <tr>
        <?php
        foreach ($arResult['HEADER'] as $header) {
            ?>
            <td><?= $header ?></td>
            <?php
        }
        ?>
    </tr>
    <?php
    foreach ($arResult['PROFILES'] as $profile) {
      //  echo '<pre>', print_r($profile), '</pre>';
        ?>
        <tr>
            <td><?= $profile['ID'] ?></td>
            <td><?= $profile['NAME'] ?></td>
            <td><?= $profile['EMAIL'] ?></td>
            <td><?= $profile['MAIL_MANAGER_ORM_EMAILS_ADDRESS_CITY'] ?></td>
            <td> <button onclick="window.location.href = 'update?id=<?=$profile['ID']?>'">Редактировать</button></td>
            <td> <button onclick="window.location.href = 'delete?id=<?=$profile['ID']?>'">Удалить</button></td>
        </tr>

        <?php
    }
    ?>
</table>

<button onclick="window.location.href = 'add'">Добавить</button>

