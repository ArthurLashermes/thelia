<?php

namespace Thelia\Api\Test;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:run_test', description: 'Run generated test for API')]
class RunTestCommand extends Command
{
    public function __construct(
        private readonly ResourceNameCollectionFactoryInterface     $resourceNameCollectionFactory,
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $resources = $this->resourceNameCollectionFactory->create();

        foreach ($resources as $resourceClass) {
            $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);

            foreach ($resourceMetadataCollection as $resourceMetadata) {
                $output->writeln('Ressource : ' . $resourceMetadata->getClass());

                foreach ($resourceMetadata->getOperations() as $operationName => $operation) {
                    $output->writeln(sprintf(
                        '  - Operation: %s, Path: %s, Method: %s',
                        $operationName,
                        $operation->getUriTemplate(),
                        $operation->getMethod()
                    ));
                }
            }
        }
        return Command::SUCCESS;
    }
}
