<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\ImportData;
use App\Importer\SolarFacade;
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
            ->setDescription('import data for a city')
            ->addArgument('city', InputArgument::REQUIRED, 'City to import data for')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*$stmt = $this->entityManager->getConnection()->prepare('SELECT 1');
        if (count($stmt->executeQuery()->fetchAllAssociative()) === 0) {
            $output->writeln('Database is not available');
            return Command::FAILURE;
        }*/
        
        $this->solarFacade->setOutput($output);
        $solarUnits = $this->solarFacade->getUnitsForCity($input->getArgument('city'));

        $data = new ImportData();
        $data->ymd = (int) date('Ymd');
        $data->city = $input->getArgument('city');
        $data->snapshot = $solarUnits;

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}