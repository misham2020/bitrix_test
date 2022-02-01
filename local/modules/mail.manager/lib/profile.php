<?php

namespace Mail\Manager;

use Mail\Manager\Orm\EmailsTable;
use Mail\Manager\Orm\AddressesTable;
use Bitrix\Main\Localization\Loc;

/**
 * Class Profile
 * @package YLab\Mail
 */
class Profile
{
    /**
     * Список профилей
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getProfiles()
    {
        $result = [];

        $result['HEADER']['ID'] = Loc::getMessage('YLAB_MAIL_MANAGER_PROFILE_HEAD_ID');
        $result['HEADER']['NAME'] = Loc::getMessage('YLAB_MAIL_MANAGER_PROFILE_HEAD_NAME');
        $result['HEADER']['EMAIL'] = Loc::getMessage('YLAB_MAIL_MANAGER_PROFILE_HEAD_EMAIL');
        $result['HEADER']['ADDRESS.CITY'] = Loc::getMessage('YLAB_MAIL_MANAGER_PROFILE_HEAD_CITY');

        $arParams = [
            'select' => [
                'ID',
                'NAME',
                'EMAIL',
                'ADDRESS.CITY'
            ]
        ];

        $oProfiles = EmailsTable::getList($arParams);

        if ($oProfiles->getSelectedRowsCount()) {
            while ($arProfile = $oProfiles->fetch()) {
                $result['PROFILES'][] = $arProfile;
            }
        }
       // echo '<pre>', print_r($result), '</pre>';
        return $result;
    }

    /**
     * Получаем данные выбранного профиля
     * @param $iProfileID
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getProfile($iProfileID)
    {
        $arProfile = EmailsTable::getById($iProfileID)->fetchAll();

        if (isset($arProfile[0]['ID']) && is_numeric($arProfile[0]['ID'])) {
            return $arProfile[0];
        }

        return false;
    }

}