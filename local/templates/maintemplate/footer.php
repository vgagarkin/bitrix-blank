<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? global $APPLICATION; ?>
<? $APPLICATION->IncludeFile(
    '/local/include/footer_counters.php',
    [
    ],
    [
        'MODE' => 'text',
        'NAME' => 'Счетчики, которые распологаются в футере'
    ]
) ?>
</body>
</html>