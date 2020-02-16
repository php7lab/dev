<?php

namespace PhpLab\Dev\Stress\Commands;

use Illuminate\Support\Collection;
use PhpLab\Dev\Stress\Domain\Entities\TestEntity;
use PhpLab\Dev\Stress\Domain\Services\StressService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StressCommand extends Command
{

    protected static $defaultName = 'dev:stress:test';
    private $stressService;

    public function __construct(?string $name = null, StressService $stressService)
    {
        parent::__construct($name);
        $this->stressService = $stressService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['<fg=white># Stress test</>']);

        $synchQueryCount = 20; // кол-во параллельных запросов
        $ageCount = 5; // кол-во эпох теста
        $baseUrl = $_ENV['API_URL'];
        $url = $baseUrl . '/php/v1/article';

        /** @var TestEntity[] $queryCollection */
        $queryCollection = new Collection;

        for ($i = 0; $i < $synchQueryCount; $i++) {
            $testEntity = new TestEntity;
            $id = $i + 1;
            $testEntity->url = $url . '/' . $id;
            $queryCollection->add($testEntity);
        }

        $resultEntity = $this->stressService->test($queryCollection, $ageCount);
        $queryRuntime = $resultEntity->runtime / $resultEntity->queryCount;

        $output->writeln([
            '',
            '<fg=green>Stress success!</>',
            '<fg=green>Total runtime: ' . $resultEntity->runtime . '</>',
            '<fg=green>Query count: ' . $resultEntity->queryCount . '</>',
            '<fg=green>Query runtime: ' . $queryRuntime . '</>',
            '<fg=green>Performance: ' . round(1 / $queryRuntime) . ' queries per second</>',
            '',
        ]);
    }

}
