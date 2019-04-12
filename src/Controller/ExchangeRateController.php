<?php

namespace App\Controller;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\Utility\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("rates/")
 */
class ExchangeRateController extends AbstractController
{
    /**
     * @Route("currencies/all", name="get_all_currencies", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCurrenciesAction()
    {
        $responseArray = [];
        foreach (Utility::CURRENCY_NAMES as $key => $value){
            $responseArray[$value] = [
                'abbreviation' => $key,
                'symbol' => Utility::CURRENCY_SYMBOLS[$key]
            ];
        }
        return $this->json($responseArray);
    }


    /**
     * @Route("all/{currency}", name="get_all_exchange_rates", methods={"GET"})
     * @param ExchangeRateRepository $exchangeRateRepository
     * @param null $currency
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getExchangeRatesAction(ExchangeRateRepository $exchangeRateRepository, $currency = null)
    {
        $searchCriteria = $currency ? ['currency' => $currency] : [];
        return $this->response($exchangeRateRepository->findBy($searchCriteria, ['date' => 'DESC']));
    }

    /**
     * @Route("periodic", name="get_periodic_exchange_rates", methods={"GET"})
     * @param ExchangeRateRepository $exchangeRateRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getExchangeRatesForDateAction(ExchangeRateRepository $exchangeRateRepository, Request $request)
    {
        //get request parameters
        $searchCriteria = $request->query->all();
        //force start date, end date and currency to null if they are not specified
        $startDateString = $searchCriteria['startDate'] ?? null;
        $endDateString = $searchCriteria['endDate'] ?? null;
        $currency = !array_key_exists('currency', $searchCriteria) ? null :
            //remove extra quotation marks in currency parameter
            str_replace('"', '', $searchCriteria['currency']);

        //create dateTime for start and end dates
        $startDate = $this->getDateTimeFromString($startDateString, '-1 month');
        $endDate = $this->getDateTimeFromString($endDateString, 'now');

        return $this->response($exchangeRateRepository->getRatesForDatesAndCurrency($startDate, $endDate, $currency));
    }

    public function response(array $responseArray)
    {
        return $this->json(array_map(function (ExchangeRate $exchangeRate): array {
            return $exchangeRate->toArray();
        }, $responseArray));
    }

    private function getDateTimeFromString($dateTimeString = null, $defaultDateString = null)
    {
        //My idea of a perfect date is dd/mm/yyyy . All other formats just make life difficult :)
        $dateTime = \DateTime::createFromFormat('j/m/Y', $dateTimeString);
        return $dateTime ? $dateTime :
            //if for some reason the datetime could not be created (date in the wrong format perhaps) then resort to the
            //default string provided
            new \DateTime($defaultDateString);

    }
}
