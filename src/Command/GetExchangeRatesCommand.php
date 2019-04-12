<?php

namespace App\Command;

use App\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetExchangeRatesCommand extends Command
{
    protected static $defaultName = 'getExchangeRates';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Makes a daily request to get the CBN exchange rates for the day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dayInWeek = date('w');
        $io = new SymfonyStyle($input, $output);
        //If today is Sunday or Monday, there's no need to run this function
        if (!($dayInWeek == 0 || $dayInWeek == 1)) {
            $exchangeRatesArray = Utility::create()->getExchangeRates(new \DateTime('now'));
            foreach ($exchangeRatesArray as $exchangeRate) {
                $this->entityManager->persist($exchangeRate);
            }
            $this->entityManager->flush();
            $io->success('Exchange Rates Loaded Successfully');
        }
        $io->success('Exchange Rates Command Executed Successfully');
    }
}
