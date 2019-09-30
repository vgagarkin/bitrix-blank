<?php
@require_once 'classes/autoload.php';
define("BUILD_DIR_PATH", $_SERVER["DOCUMENT_ROOT"] . "/local/build/");

# автозагрузка классов
\Bitrix\Main\Loader::registerAutoLoadClasses(
    null,
    [
        # Основной класс сайта
        'CTemplate' => '/local/php_interface/classes/CTemplate.php'
    ]
);
# менеджер событий
$eventManager = \Bitrix\Main\EventManager::getInstance();

# удаляем вкладку Реклама в инфоблоках и магазине. TODO: если вкладка нужна, то удалить
$eventManager->addEventHandler(
    'main',
    'OnAdminTabControlBegin',
    [
        'CTemplate',
        'RemoveYandexDirectTab'
    ]
);
# вырезаем type="text/javascript" по требованиям W3C
$eventManager->addEventHandler(
    'main',
    'OnEndBufferContent',
    [
        'CTemplate',
        'removeTypeJS'
    ]
);
#
$eventManager->addEventHandler(
    'main',
    'OnEpilog',
    [
        'CTemplate',
        'Check404Error'
    ]
);
