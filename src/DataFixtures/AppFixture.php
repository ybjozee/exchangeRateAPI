<?php

namespace App\DataFixtures;

use App\Utility\Utility;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadExchangeRates($manager);
        $manager->flush();
    }

    private function loadExchangeRates(ObjectManager $manager)
    {
        $exchangeRatesArray = Utility::create()->getExchangeRates();
        foreach ($exchangeRatesArray as $exchangeRate) {
            $manager->persist($exchangeRate);
        }
    }
}
