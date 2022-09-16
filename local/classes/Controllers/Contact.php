<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Contacts extends BaseController
{
    public function getContactsAction()
    {
        return [
            [
                "type" => "Call",
                "image_url" => "https://www.dl.dropboxusercontent.com/s/3owcz8erqsppeue/phone.pdf?dl=0",
                "action_type" => 'open_link',
                "action_value" => 'tel://+78003505670',
                "direct_web_url" => 'https://sodamoda.ru/contacts/',
                "attach_product_link_if_possible" => false,
            ],
            [
                "type" => "WhatsApp",
                "image_url" => "https://www.dl.dropboxusercontent.com/s/2rpbue4husgidft/whatsapp.pdf?dl=0",
                "action_type" => 'open_link',
                "action_value" => 'whatsapp://send?phone=79688700070&text=Hello!%20I\'m%20interested%20in%20this%20product%3A%20%0A',
                "direct_web_url" => 'https://api.whatsapp.com/send?phone=79688700070/',
                "attach_product_link_if_possible" => true,
            ],
        ];
    }
}
