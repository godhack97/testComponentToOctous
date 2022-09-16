<?php

namespace App\Helpers;

use Symfony\Component\DomCrawler\Crawler;

class Qiwi
{
    public static function getUrlByTemplate(string $html): array
    {
        $crawler = new Crawler($html);

        $linkCrawler = $crawler->filter('.sale-paysystem-qiwi-button-item')->links();
       
        return $linkCrawler[0]->getUri() ?? "";
    }
}
