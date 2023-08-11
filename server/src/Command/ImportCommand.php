<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\ImportData;
use App\Marktstammdatenregister\SolarFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(name: 'app:import')]
class ImportCommand extends Command
{
    private SolarFacade $solarFacade;
    private EntityManagerInterface $entityManager;

    public function __construct(SolarFacade $solarFacade, EntityManagerInterface $entityManager)
    {
        $this->solarFacade = $solarFacade;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('city', InputArgument::REQUIRED, 'City to import data for')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->solarFacade->setOutput($output);
        $solarUnits = $this->solarFacade->getUnitsForCity($input->getArgument('city'));

        // create new ImportData
        $data = new ImportData();
        $data->setYmd((int) date('Ymd'));
        $data->setCity($input->getArgument('city'));
        $data->setSnapshot($solarUnits);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        // write into db
        //$entityManager = $this->getContainer()->get('doctrine')->getManager();

        return Command::SUCCESS;
    }
}