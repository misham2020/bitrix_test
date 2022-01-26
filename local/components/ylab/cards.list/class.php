<?php

namespace YLab\Components;

use Bitrix\Iblock\IblockTable;
use \Bitrix\Main\ArgumentException;
use \Bitrix\Main\Grid\Options as GridOptions;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\UI\PageNavigation;
use \CBitrixComponent;
use \CIBlockElement;
use \Exception;
use \Bitrix\Main\UI\Filter\Options;

/**
 * Class CardsListComponent
 * @package YLab\Components
 * Компонент отображения списка элементов нашего ИБ
 */
class CardsListComponent extends CBitrixComponent
{
    /** @var int $idIBlock ID информационного блока */
    private $idIBlock;
    private $cardSecret;

    /** @var string $templateName Имя шаблона компонента */
    private $templateName;

    /**
     * @param $arParams
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function onPrepareComponentParams($arParams)
    {
        Loader::includeModule('iblock');

        $this->templateName = $this->GetTemplateName();

        return $arParams;
    }

    /**
     * Метод executeComponent
     *
     * @return mixed|void
     * @throws Exception
     */
    public function executeComponent()
    {
        $this->idIBlock = self::getIBlockIdByCode($this->arParams['IBLOCK_CODE']);

        if ($this->templateName == 'grid') {
            $this->showByGrid();
        } else {
            $this->arResult['ITEMS'] = $this->getElements();
        }


        $this->includeComponentTemplate();
    }

    /**
     * Получим элементы ИБ
     * @return array
     */
    public function getElements(): array
    {
        $result = [];

        if (!$this->getGridNav()->allRecordsShown()) {
            $arNav['iNumPage'] = $this->getGridNav()->getCurrentPage();
            $arNav['nPageSize'] = $this->getGridNav()->getPageSize();
        } else {
            $arNav = false;
        }

        $arFilter = $this->getGridFilterValues();

        $arCurSort = $this->getObGridParams()->getSorting(['sort' => ['ID' => 'DESC']])['sort'];

        $elements = CIBlockElement::GetList(
            $arCurSort,
            $arFilter,
            false,
            $arNav,
            [
                'ID',
                'IBLOCK_ID',
                'PROPERTY_CARD_NUMBER',
                'PROPERTY_CARD_USER',
                'PROPERTY_CARD_TYPE',
                'PROPERTY_COST_OF_MAINTENANCE',
                'PROPERTY_THE_VALIDITY_PERIOD',
                'PROPERTY_CARD_EXPIRATION_DATE',
            ]
        );

        $this->getGridNav()->setRecordCount($elements->SelectedRowsCount());

        while ($element = $elements->GetNext()) {

            $this->cardSecret = md5($element['PROPERTY_CARD_NUMBER_VALUE']);

            $result[] = [
                'ID' => $element['ID'],
                'CARD_NUMBER' => $element['PROPERTY_CARD_NUMBER_VALUE'],
                'CARD_USER' => $element['PROPERTY_CARD_USER_VALUE'],
                'CARD_TYPE' => $element['PROPERTY_CARD_TYPE_VALUE'],
                'COST_OF_MAINTENANCE' => $element['PROPERTY_COST_OF_MAINTENANCE_VALUE'],
                'THE_VALIDITY_PERIOD' => $element['PROPERTY_THE_VALIDITY_PERIOD_VALUE'],
                'CARD_EXPIRATION_DATE' => $element['PROPERTY_CARD_EXPIRATION_DATE_VALUE'],
                'CARD_SECRET' => $this->cardSecret,

            ];
        }

        return $result;
    }

    /**
     * Отображение через грид
     */
    public function showByGrid()
    {
        $this->arResult['GRID_ID'] = $this->getGridId();

        $this->arResult['GRID_BODY'] = $this->getGridBody();
        $this->arResult['GRID_HEAD'] = $this->getGridHead();

        $this->arResult['GRID_NAV'] = $this->getGridNav();
        $this->arResult['GRID_FILTER'] = $this->getGridFilterParams();

        $this->arResult['BUTTONS']['ADD']['NAME'] = Loc::getMessage('YLAB.CARD.LIST.CLASS.ADD');
    }

    /**
     * Возвращает содержимое (тело) таблицы.
     *
     * @return array
     */
    private function getGridBody(): array
    {
        $arBody = [];

        $arItems = $this->getElements();

        foreach ($arItems as $arItem) {
            $arGridElement = [];

            $arGridElement['data'] = [
                'ID' => $arItem['ID'],
                'CARD_NUMBER' => !empty($arItem['CARD_NUMBER']) ? $arItem['CARD_NUMBER'] : '',
                'CARD_USER' => !empty($arItem['CARD_USER']) ? $arItem['CARD_USER'] : '',
                'CARD_TYPE' => !empty($arItem['CARD_TYPE']) ? $arItem['CARD_TYPE'] : '',
                'COST_OF_MAINTENANCE' => !empty($arItem['COST_OF_MAINTENANCE']) ? $arItem['COST_OF_MAINTENANCE'] : '',
                'THE_VALIDITY_PERIOD' => !empty($arItem['THE_VALIDITY_PERIOD']) ? $arItem['THE_VALIDITY_PERIOD'] : '',
                'CARD_EXPIRATION_DATE' => !empty($arItem['CARD_EXPIRATION_DATE']) ? $arItem['CARD_EXPIRATION_DATE'] : '',
                'CARD_SECRET' => !empty($arItem['CARD_SECRET']) ? $arItem['CARD_SECRET'] : '',
            ];
            $arGridElement['actions'] = [
                [
                    'text' => 'Редактировать',
                    'onclick' => 'document.location.href="/accountant/reports/1/edit/"'
                ],
                [
                    'text' => 'Удалить',
                    'onclick' => 'document.location.href="/accountant/reports/1/delete/"'
                ]
            ];
            $arBody[] = $arGridElement;
        }

        return $arBody;
    }

    /**
     * Возвращает идентификатор грида.
     *
     * @return string
     */
    private function getGridId(): string
    {
        return 'ylab_cards_list_' . $this->idIBlock;
    }

    /**
     * Возращает заголовки таблицы.
     *
     * @return array
     */
    private function getGridHead(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'default' => true,
                'sort' => 'ID',
            ],
            [
                'id' => 'CARD_NUMBER',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.NUMBER'),
                'default' => true,
                'sort' => 'PROPERTY_CARD_NUMBER',
            ],
            [
                'id' => 'CARD_USER',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.USER'),
                'default' => true,
                'sort' => 'PROPERTY_CARD_USER',
            ],
            [
                'id' => 'CARD_TYPE',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.TYPE'),
                'default' => true,
                'sort' => $this->cardSecret,
            ],
            [
                'id' => 'CARD_SECRET',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.SECRET'),
                'default' => true,
                'sort' => 'CARD_SECRET',
            ],
            [
                'id' => 'COST_OF_MAINTENANCE',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.COST'),
                'default' => true,
                'sort' => 'PROPERTY_COST_OF_MAINTENANCE',
            ],
            [
                'id' => 'THE_VALIDITY_PERIOD',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.VALIDITY'),
                'default' => true,
                'sort' => 'PROPERTY_THE_VALIDITY_PERIOD',
            ],
            [
                'id' => 'CARD_EXPIRATION_DATE',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.DATE'),
                'default' => true,
                'sort' => 'PROPERTY_CARD_EXPIRATION_DATE',
            ],
        ];
    }


    /**
     * Метод возвращает ID инфоблока по символьному коду
     *
     * @param $code
     *
     * @return int|void
     * @throws Exception
     */
    public static function getIBlockIdByCode($code)
    {
        $IB = IblockTable::getList([
            'select' => ['ID'],
            'filter' => ['CODE' => $code],
            'limit' => '1',
            'cache' => ['ttl' => 3600],
        ]);
        $return = $IB->fetch();
        if (!$return) {
            throw new Exception('IBlock with code"' . $code . '" not found');
        }

        return $return['ID'];
    }

    /**
     * Возвращает настройки отображения грид фильтра.
     *
     * @return array
     */
    private function getGridFilterParams(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'type' => 'number'
            ],
            [
                'id' => 'PROPERTY_CARD_USER',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.USER'),
                'type' => 'string'
            ],
            [
                'id' => 'PROPERTY_CARD_NUMBER',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.NUMBER'),
                'type' => 'number'
            ],
            [
                'id' => 'PROPERTY_CARD_TYPE_VALUE',
                'name' => Loc::getMessage('YLAB.CARD.LIST.CLASS.TYPE'),
                'type' => 'list',
                'items' => ['Личная' => Loc::getMessage('YLAB.CARD.LIST.CLASS.TYPE_SINGLE'),
                    'Корпоративная' => Loc::getMessage('YLAB.CARD.LIST.CLASS.TYPE_CORPORATE')]
            ],
            ["id" => 'PROPERTY_CARD_EXPIRATION_DATE',
                'type' => 'date',
                "name" => GetMessage('YLAB.CARD.LIST.CLASS.DATE'),
            ],


        ];

    }

    /**
     * Возвращает единственный экземпляр настроек грида.
     *
     * @return GridOptions
     */
    private function getObGridParams(): GridOptions
    {
        return $this->gridOption ?? $this->gridOption = new GridOptions($this->getGridId());
    }

    /**
     * Параметры навигации грида
     *
     * @return PageNavigation
     */
    private function getGridNav(): PageNavigation
    {
        if ($this->gridNav === null) {
            $this->gridNav = new PageNavigation($this->getGridId());
            $this->gridNav->allowAllRecords(true)->setPageSize($this->getObGridParams()->GetNavParams()['nPageSize'])
                ->initFromUri();
        }

        return $this->gridNav;
    }

    /**
     * Возвращает значения грид фильтра.
     *
     * @return array
     */
    public function getGridFilterValues(): array
    {
        $obFilterOption = new Options($this->getGridId());
        $arFilterData = $obFilterOption->getFilter([]);
        $baseFilter = array_intersect_key($arFilterData, array_flip($obFilterOption->getUsedFields()));
        $formatedFilter = $this->prepareFilter($arFilterData, $baseFilter);

        return array_merge(
            $baseFilter,
            $formatedFilter
        );
    }

    /**
     * Подготавливает параметры фильтра
     * @param array $arFilterData
     * @param array $baseFilter
     * @return array
     */
    public function prepareFilter(array $arFilterData, &$baseFilter = []): array
    {
        $arFilter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $this->idIBlock,
        ];

        if (!empty($arFilterData['ID_from'])) {
            $arFilter['>=ID'] = (int)$arFilterData['ID_from'];
        }
        if (!empty($arFilterData['ID_to'])) {
            $arFilter['<=ID'] = (int)$arFilterData['ID_to'];
        }

        if (!empty($arFilterData['PROPERTY_CARD_NUMBER_from'])) {
            $arFilter['>=PROPERTY_CARD_NUMBER'] = (int)$arFilterData['PROPERTY_CARD_NUMBER_from'];
        }
        if (!empty($arFilterData['PROPERTY_CARD_NUMBER_to'])) {
            $arFilter['<=PROPERTY_CARD_NUMBER'] = (int)$arFilterData['PROPERTY_CARD_NUMBER_to'];
        }
        if (!empty($arFilterData['PROPERTY_CARD_USER_VALUE'])) {
            $arFilter['PROPERTY_CARD_USER_VALUE'] = "%" . $arFilterData['FIND'] . "%";
        }

        if (!empty($arFilterData['PROPERTY_CARD_EXPIRATION_DATE_from'])) {
            $arFilter['>=PROPERTY_CARD_EXPIRATION_DATE'] = (int)$arFilterData['PROPERTY_CARD_EXPIRATION_DATE_from'];
        }
        if (!empty($arFilterData['PROPERTY_CARD_EXPIRATION_DATE_to'])) {
            $arFilter['<=PROPERTY_CARD_EXPIRATION_DATE'] = (int)$arFilterData['PROPERTY_CARD_EXPIRATION_DATER_to'];
        }

        return $arFilter;
    }

}
