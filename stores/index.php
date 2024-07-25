<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php'; ?>

<?php $APPLICATION->IncludeComponent(
    'devresource.stores:stores',
    '',
    [
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/stores/',
        'SEF_URL_TEMPLATES' => [
            'details' => '#ID#/',
            'edit' => '#ID#/edit/',
        ],
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '3600',
    ],
    false
); ?>


<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>
