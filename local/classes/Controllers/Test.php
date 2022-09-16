<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Symfony\Component\DomCrawler\Crawler;

class Test extends BaseController
{
    public function testAction()
    {
        $crawler = new Crawler($html);

        $form = $crawler->filter('form')->form();
        $uri = $form->getUri();
        $values = $form->getValues();

        return [
            "uri" => $uri,
            "values" => $values
        ];
    }
}
