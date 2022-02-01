<?php

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Mail\Manager\Orm\EmailsTable;


class Update extends CBitrixComponent
{

    /**
     * проверяет подключение необходиимых модулей
     * @throws LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('mail.manager'))
            throw new Main\LoaderException(Loc::getMessage('MAIL.MANAGER_MODULE_NOT_INSTALLED'));
    }

    function var1()
    {
        $result = [];
        $id = intval($_GET['id']);
        $select = EmailsTable::getList()->fetchCollection();
        $this->arResult["SELECT"] = $select->getByPrimary($id);
        $name = $_REQUEST['name'];
        $mail = $_REQUEST['mail'];
        $address_id = intval($_REQUEST['address_id']);
        $data = array(
            'NAME' => $name,
            'EMAIL' => $mail,
            'ADDRESS_ID' => $address_id,
        );
        if (isset($id)) {
            $result = EmailsTable::update($id, $data);
        }
        return $result;

    }

    public function executeComponent()
    {

        $this->includeComponentLang('class.php');
        $this->checkModules();
        $result = $this->var1();
        $this->includeComponentTemplate();

    }
}

