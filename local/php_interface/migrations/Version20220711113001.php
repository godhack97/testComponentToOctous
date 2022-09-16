<?php

namespace Sprint\Migration;


class Version20220711113001 extends Version
{
    protected $description = "Color prod";

    protected $moduleVersion = "4.0.2";

    /**
     * @throws Exceptions\ExchangeException
     * @throws Exceptions\RestartException
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $this->getExchangeManager()
            ->HlblockElementsImport()
            ->setExchangeResource('hlblock_elements.xml')
            ->setLimit(20)
            ->execute(function ($item) {
                $this->getHelperManager()
                    ->Hlblock()
                    ->addElement(
                        $item['hlblock_id'],
                        $item['fields']
                    );
            });
    }

    public function down()
    {
        //your code ...
    }
}
