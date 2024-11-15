<?php

declare(strict_types=1);

namespace Thelia\Api\Test\Command;


use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Test\Service\GenerateFunctionTestService;


#[AsCommand(name: 'api:generate:tests', description: 'Generate tests for APIP resource')]
class TestGenerationCommand extends Command
{
    private const NAMESPACE = 'Thelia\Api\Resource';

    private const RESOURCE_TEST_DIR = __DIR__ . '/../../Resource';

    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private readonly GenerateFunctionTestService $generateFunctionTestService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createDirIfNotExists(__DIR__.'/../ResourceTest/Generated/');
        foreach ($this->getFiles() as $file) {
            $className = self::NAMESPACE . '\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($className) && in_array(PropelResourceInterface::class, class_implements($className), true)) {
                $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create(resourceClass: $className);
                if (count($resourceMetadataCollection) < 1) {
                    continue;
                }
                $destinationClassPath = $this->createTestFile($file);
                foreach ($resourceMetadataCollection as $resourceMetadata) {
                    foreach ($resourceMetadata->getOperations() as $operation) {
                        try {
                            if ($operation instanceof Post) {
                                $this->generateFunctionTestService->createPostTest($operation, $destinationClassPath, $className);
                            }
                            if ($operation instanceof GetCollection) {
                                $this->generateFunctionTestService->createGetCollectionTest($operation, $destinationClassPath);
                            }
                            if ($operation instanceof Get){
                                //$this->createGetTest($operation, $destinationClassPath);
                            }
                            $output->writeln(sprintf('<info>%s</info>', $operation->getName()));
                        }catch (Exception $exception){
                            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
                            continue;
                        }
                    }
                }
            }
        }
        return Command::SUCCESS;
    }

    private function copyModelClass(string $sourceClass, string $destinationClassName, string $destinationClassNewName)
    {
        if (!file_exists($sourceClass)) {
            throw new RuntimeException(sprintf("The source file %s does not exist.", $sourceClass));
        }
        $content = file_get_contents($sourceClass);
        $content = str_replace('class ModelTestGeneration', 'class ' . substr($destinationClassNewName, 0, strrpos($destinationClassNewName, '.')), $content);
        $content = str_replace("Thelia\Api\Test\ResourceTest\Model", "Thelia\Api\Test\ResourceTest\Generated", $content);

        file_put_contents($destinationClassName, $content);
    }

    private function getFiles() : array
    {
        return array_filter(scandir(self::RESOURCE_TEST_DIR), function ($file) {
            return is_file(self::RESOURCE_TEST_DIR . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });
    }

    private function createDirIfNotExists(string $dir) : void{
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    private function createTestFile($file) : string
    {
        $destinationClassName = pathinfo($file, PATHINFO_FILENAME);
        $destinationClassNewName = $destinationClassName . 'Test.php';
        $destinationClassPath = __DIR__ . '/../ResourceTest/Generated/' . $destinationClassNewName;
        $this->copyModelClass(
            sourceClass: __DIR__ . '/../ResourceTest/Model/ModelTestGeneration.php',
            destinationClassName: $destinationClassPath,
            destinationClassNewName: $destinationClassNewName
        );
        return $destinationClassPath;
    }
}
