<?php

use Bitrix;
use Bitrix\Main;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Context;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Sale;
use Bitrix\Sale\Order;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;
use Bitrix\Main\Security\Random;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Sender\Subscription;
use Bitrix\Sender\PostingRecipient;

#TODO: Назвать класс по называнию проекта
/**
 * Class CTemplate
 */
class CTemplate
{
    /**
     * Список характеристик, которые не нужно отображать в блоке с характеристиками
     */
    const ARR_HIDE_CHARACTERISTICS = [
        'GTIN'
    ];

    /**
     * Каталог
     */
    const IBLOCK_TYPE_CATALOG = 'catalog';

    const IBLOCK_CODE_CATALOG = 'catalog';

    const IBLOCK_ID_CATALOG = 1;

    /**
     * Получить идентификатор инфоблока по его символьному коду
     *
     * @param string $code Символьный код инфоблока
     *
     * @return int Идентификатор инфоблока
     *
     * @throws LoaderException
     * @throws ArgumentException
     */
    final public static function getIblockIdByCode($code)
    {
        if (null === $code || '' === trim($code)) {
            throw new ArgumentException('Empty iblock code');
        }

        $result = 0;
        $cache = Cache::createInstance();

        if ($cache->initCache(PHP_INT_MAX, __FILE__ . __LINE__ . $code, 'tmp')) {
            $result = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            Loader::includeModule('iblock');

            $dbRes = CIBlock::GetList(
                ['SORT' => 'ASC'],
                ['CODE' => $code, 'CHECK_PERMISSIONS' => 'N']
            );

            $result = $dbRes->Fetch();

            $result = (int)$result['ID'];


            if ($result === 0) {
                $cache->abortDataCache();
            }

            $cache->endDataCache($result);
        }

        return $result;
    }

    /**
     * Функция для получения списка всех св-в товара
     * поддерживает скрытие лишних свойств
     *
     * Работает с кешированием
     * Список обновляется раз в сутки
     *
     * @param array $arrHideProperties Массив с кодами свойств, которые нужно скрыть
     *
     * @return array Массив всех свойств ИБ Каталог
     */
    public static function getCatalogProperties($arrHideProperties = [])
    {
        CModule::IncludeModule("iblock");
        try {

            $result = [];
            $cache = Bitrix\Main\Data\Cache::createInstance();

            if ($cache->initCache(86400, __FILE__ . __LINE__ . time(), 'cache')) {
                $result = $cache->getVars();
            } elseif ($cache->startDataCache()) {
                $list = \CIBlockProperty::GetList(
                    [],
                    [
                        'IBLOCK_ID' => self::IBLOCK_ID_CATALOG
                    ],
                    []
                );
                $result = [];
                while($element = $list->GetNext())
                {
                    if (
                        $element['CODE'] == '' ||
                        in_array($element['CODE'], $arrHideProperties)
                    ) {
                        continue;
                    }
                    $result[] = $element['CODE'];
                }


                if (0 === count($result)) {
                    $cache->abortDataCache();
                }

                $cache->endDataCache($result);
            }

            return $result;
        } catch (\Exception $e) {
            return '';
        }
    }

    /*
     * Функция для удаления вкладки "Реклама" из редактирования элемента и раздела
     */
    function RemoveYandexDirectTab(&$TabControl){
        if (
            $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_element_edit.php' ||
            $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/cat_product_edit.php'
        ) {
            foreach($TabControl->tabs as $Key => $arTab){
                if($arTab['DIV']=='seo_adv_seo_adv') {
                    unset($TabControl->tabs[$Key]);
                }
            }
        }
    }

    /**
     * Функция вырезает type="text/javascript" из тега <script>
     * @param $content
     */
    function removeTypeJS(&$content) {
        $content = str_replace(' type="text/javascript"', "", $content);
    }

    /**
     * Проверка на 404
     */
    static function Check404Error() {
        if(defined('ERROR_404') && ERROR_404=='Y' || CHTTP::GetLastStatus() == "404 Not Found"){
            GLOBAL $APPLICATION;
            $APPLICATION->RestartBuffer();
            $APPLICATION->SetPageProperty("keywords", "Страница не найдена");
            $APPLICATION->SetPageProperty("title", "Страница не найдена");
            $APPLICATION->SetPageProperty("description", "Страница не найдена");
            $APPLICATION->SetPageProperty("ADDITIONAL_TITLE", "Страница не найдена");
            require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/header.php';
            require $_SERVER['DOCUMENT_ROOT'].'/404.php';
            require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/footer.php';
        }
    }

}