<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\IdealIssuersResponse;

class IdealIssuersResponseBuilder
{
    public static function newInstance(): IdealIssuersResponse
    {
        return new IdealIssuersResponse(json_encode(self::getTestData()));
    }

    public static function newInstanceAsJson(): string
    {
        return json_encode(self::getTestData());
    }

    private static function getTestData(): array
    {
          return [
            'issuers' => [
                [
                    'id' => 'BANKNL2Y',
                    'name' => 'iDEAL issuer simulatie',
                    'logos' => [
                        [
                            'url' => 'https://betalen-acpt3.rabobank.nl/omnikassa/static/issuers/BANKNL2Y.png',
                            'mimeType' => 'image/png',
                        ]
                    ],
                    'countryNames' => 'Nederland',
                ],
                [
                    'id' => 'RABONL2U',
                    'name' => 'RABONL2U - eWL issuer simluation',
                    'logos' => [
                        [
                            'url' => 'https://betalen-acpt3.rabobank.nl/omnikassa/static/issuers/RABONL2U.png',
                            'mimeType' => 'image/png',
                        ]
                    ],
                    'countryNames' => 'Nederland',
                ],
            ],
        ];
    }
}
