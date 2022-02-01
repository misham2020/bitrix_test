<?php

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Mail\Manager\Orm\EmailsTable;


class Add extends CBitrixComponent
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
 
    //Корректное добавление записи
    function var1()
    {
        $result = [];
            $name = $_REQUEST['name'];
            $mail = $_REQUEST['mail'];
            $address_id = intval($_REQUEST['address_id']);
            $data = (array(
                'NAME' => $name,
                'EMAIL' => $mail,
                'ADDRESS_ID' => $address_id,
            ));
            if (isset($name) && isset($mail) && isset($address_id)) {
            $result = EmailsTable::add($data);
            }
            return $result;
        
    }

    public function executeComponent()
    {
        $this -> includeComponentLang('class.php');
        $this -> checkModules();
        $result = $this->var1();
 
        $this->includeComponentTemplate();
    }
};