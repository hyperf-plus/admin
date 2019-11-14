<?php

declare(strict_types=1);

namespace App\Command\Node;

use App\Logic\NodeLogic;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Annotation\Command;
use App\Util\Data;

/**
 * @Command
 */
class CreateCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('node:create');
    }

    public function configure()
    {
        $this->setDescription('Hyperf Nodes Create Command');
    }

    public function handle()
    {
        $logic = new NodeLogic();

        $list = $logic->getList();

        $tree = $logic->toTree($list);

        $multi_tree = arr2tree($tree, 'node', 'pnode', 'sub');

        file_put_contents(RUNTIME_PATH . 'nodes.php', "<?php \n return " . var_export($multi_tree, true) . ";");

        $this->comment('Nodes Data has successfully created!');
    }

}

