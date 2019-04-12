<?php

namespace App\Utility;


use App\Entity\ExchangeRate;
use DateTime;

class Utility
{
    const CURRENCY_SYMBOLS = [
        'EUR' => '€',
        'GBP' => '£',
        'NGN' => '₦',
        'USD' => '$',
        'CHF' => 'CHF',
        'JPY' => '¥',
        'CNY' => '¥',
        'SAR' => '﷼',
        'ZAR' => 'R',
        'DKK' => 'kr'
    ];

    const CURRENCY_NAMES = [
        'EUR' => 'EURO',
        'GBP' => 'POUNDS STERLING',
        'NGN' => 'NIGERIAN NAIRA',
        'USD' => 'US DOLLAR',
        'CHF' => 'SWISS FRANC',
        'JPY' => 'YEN',
        'CNY' => 'YUAN/RENMINBI',
        'SAR' => 'RIYAL',
        'ZAR' => 'SOUTH AFRICAN RAND',
        'DKK' => 'DANISH KRONA'
    ];

    const CBN_EXCHANGE_RATE_URL = 'https://www.cbn.gov.ng/Functions/export.asp?tablename=exchange';

    public static function create()
    {
        return new self();
    }

    public function getExchangeRates(DateTime $earliestDate = null): array
    {
        $curlResponse = $this->curlExchangeRates();
        if (!$curlResponse) return [];
        $rateString = $this->processExchangeRates($curlResponse, $earliestDate);
        return $this->getExchangeRateArray($rateString);
    }

    protected function curlExchangeRates(): ?string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::CBN_EXCHANGE_RATE_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        /**
         * Consider logging the error (in the event of one) before returning null
         */
        return $err ? null : $response;
    }

    protected function processExchangeRates(string $curlResponse, DateTime $earliestDate = null): string
    {
        $delimiter = ($earliestDate ? $earliestDate : new DateTime('-20 years'))->format('n/j/Y');
        //split the string into an array based on the delimiter. The first string in the array is the rate string we need
        $ratesString = preg_split(sprintf('("%s")', $delimiter), $curlResponse, 2)[0];

        $headerPattern = '("Rate Date","Currency","Rate Year","Rate Month","Buying Rate","Central Rate","Selling Rate"\r\n)';
        //remove the first line in our rate string and return the result
        return preg_replace($headerPattern, '', $ratesString);
    }

    protected function getExchangeRateArray(string $ratesString): array
    {
        $formattedString = str_replace('"', '', $ratesString);
        $ratesStringArray = preg_split('(,\r\n)', $formattedString);
        //the last string in the array is empty and would cause problems if we try to create an ExchangeRate entity
        array_pop($ratesStringArray);
        $exchangeRateArray = [];
        foreach ($ratesStringArray as $rateString) {
            $detailsArray = explode(',', $rateString);
            $exchangeRateObject = new ExchangeRate();
            $exchangeRateArray[] =
                $exchangeRateObject
                    ->setDate(DateTime::createFromFormat(
                        'n/j/Y', $detailsArray[0],
                        new \DateTimeZone('Africa/Lagos')))
                    ->setCurrency($this->getCurrencyAbbreviation($detailsArray[1]))
                    ->setYear($detailsArray[2])
                    ->setMonth($detailsArray[3])
                    ->setBuyingRate($detailsArray[4])
                    ->setCentralRate($detailsArray[5])
                    ->setSellingRate($detailsArray[6]);
        }
        return $exchangeRateArray;
    }

    protected function getCurrencyAbbreviation($inputCurrency)
    {
        //get the key value of the input string in the array of constant names
        $key = array_search($inputCurrency, self::CURRENCY_NAMES);

        //if the value of key is false, return the input currency (applicable to currencies like CFA and WAUA)
        return $key ? $key : $inputCurrency;
    }
}