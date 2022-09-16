<?php

namespace App\Helpers;

use Bitrix\Main\Web\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class PayPal
{
    public static function getUrlByTemplate(string $html): array
    {
        $crawler = new Crawler($html);

        $form   = $crawler->filter('form')->form();
        $uri    = $form->getUri();
        $values = $form->getValues();
        $method = $form->getMethod();
        $values["currency_code"] = "USD";
        $httpClient = new HttpClient();
        
        $response = $httpClient->post($uri, $values);

        return [
            "method" => $method,
            "uri"    => $uri,
            "values" => $values,
            "link"   => $response
        ];
    }
}
