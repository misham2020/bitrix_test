<?php

namespace Mail\Manager\Orm;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

/**
 * Class ProfilesTable
 * @package app\Orm
 */
class AddressesTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'y_addresses';
    }

    /**
     * Returns entity map definition.
     * @return array
     * @throws \Exception
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID',
            ]),
            new Entity\StringField('CITY', [
                'validation' => [__CLASS__, 'validateName'],
                'title' => 'Город'/*Loc::getMessage('YLAB_ADDRESESS_CITY_FIELD')*/,
            ]),
            new Entity\StringField('STREET', [
                'validation' => [__CLASS__, 'validateName'],
                'title' => 'Улица'/*Loc::getMessage('YLAB_ADDRESESS_STREET_FIELD')*/,
            ]),
            new Entity\StringField('HOUSE', [
                'validation' => [__CLASS__, 'validateName'],
                'title' => 'Дом'/* Loc::getMessage('YLAB_ADDRESESS_HOUSE_FIELD') */,
            ]),
            new Entity\StringField('FLAT', [
                'validation' => [__CLASS__, 'validateName'],
                'title' => 'Квартира'/*Loc::getMessage('YLAB_ADDRESESS_FLAT_FIELD')*/,
            ]),

        ];
    }

    /**
     * Returns validators for NAME field.
     * @return array
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public static function validateName()
    {
        return [
            new Entity\Validator\Length(null, 255),
        ];
    }
}
