<?php
namespace Accord\Integration\Test\Env;

trait SettingsExample
{

    /**
     * @return array
     */
    protected function getSettings1()
    {
        return [
            [
                'setting' => 'futureEndDays',
                'label' => 'Number of days to future orders end date',
                'category' => 'General - Checkout',
                'value' => '60'
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getSettings2()
    {
        return [
            [
                'setting' => 'futureEndDays',
                'label' => 'Number of days to future orders end date',
                'category' => 'General - Checkout',
                'value' => '60'
            ],
            [
                'setting' => 'allowGuests',
                'label' => 'Allow guests',
                'category' => 'Catalogue - General',
                'value' => 'true'
            ],
        ];
    }

}