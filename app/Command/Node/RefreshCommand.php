<?php

declare(strict_types=1);

namespace App\Command\Node;

use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
class RefreshCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('node:refresh');
    }

    public function configure()
    {
        $this->setDescription('Hyperf Nodes Refresh Command');
    }

    public function handle()
    {
        $nodes_path = RUNTIME_PATH . 'nodes.php';
        file_exists($nodes_path) && unlink($nodes_path);

        $this->call('node:create');

        $this->comment('Nodes Data has successfully Refreshed!');
    }

}

