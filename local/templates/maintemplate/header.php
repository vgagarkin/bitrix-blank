<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); } ?>
<?
global $APPLICATION;
global $isIndexPage;
global $isAdmin;
global $isAuthorized;
global $USER;

$isIndexPage    = '/index.php' === $APPLICATION->GetCurPage(true);
$isAdmin        = $USER->IsAdmin();
$isAuthorized   = $USER->IsAuthorized();
$isCatalog      = (strripos($APPLICATION->GetCurPage(false), '/catalog/') !== false) ? true : false;
$logoLink       = $isIndexPage ? '' : 'href="/"';
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <?php $APPLICATION->ShowHead(); ?>
    <?php $APPLICATION->ShowMeta('title'); ?>
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?
    $asset = \Bitrix\Main\Page\Asset::getInstance();

    $asset->addCss(BUILD_DIR_PATH . '/css/build.css'); # всегда должен быть последним

    $asset->addJs(BUILD_DIR_PATH . '/js/bundle.js', false); # всегда должен быть последним
    CJSCore::Init([ "ajax" ]);
    ?>
    <? $APPLICATION->IncludeFile(
        '/local/include/header_counters.php',
        [
        ],
        [
            'MODE' => 'text',
            'NAME' => 'Счетчики, которые распологаются в хедере'
        ]
    ) ?>
</head>
<body class="<?= $isAdmin ? 'admin' : '' ?>">
<?php $APPLICATION->ShowPanel(); ?>