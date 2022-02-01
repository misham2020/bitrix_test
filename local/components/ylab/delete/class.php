<?php

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Mail\Manager\Orm\EmailsTable;

class Delete extends CBitrixComponent
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
        $id = intval($_GET['id']);
        $result = EmailsTable::delete($id);

        return $result;
    }


    public function executeComponent()
    {
        $this -> includeComponentLang('class.php');

        $this -> checkModules();

        $result = $this->var1();

        if ($result->isSuccess())
        {
            $this->arResult='Запись была удалена';
        }
        else
        {
            $error=$result->getErrorMessages();
            $this->arResult='Произошла ошибка при удалении: <pre>'.var_export($error,true).'</pre>';
        }

        $this->includeComponentTemplate();
    }
};