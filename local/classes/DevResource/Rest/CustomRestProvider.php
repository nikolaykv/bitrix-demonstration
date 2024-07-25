<?php

namespace DevResource\Rest;

use CRestServer;
use Bitrix\Main\Diag\Debug;

class CustomRestProvider
{
    /**
     * @return array[]
     */
    public static function OnRestServiceBuildDescription()
    {
        return array(
            'custom' => array(
                'devresource.profile' => array(
                    'callback' => array(
                        get_called_class(), 'example'
                    ),
                    'options' => []
                ),

            )
        );
    }

    /**
     * @param $query
     * @param $n
     * @param CRestServer $server
     * @return string
     */
    public static function example($query, $n, CRestServer $server)
    {
        return 'Devresource\Rest\CustomRestProvider::example();';
    }
}