<?php

declare(strict_types=1);

namespace App\Command;

use Dotenv\Dotenv;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class SaltCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('salt:gen');
    }

    public function configure()
    {
        $this->setDescription('Hyperf salt Command');
    }

    public function handle()
    {
        $env_file = BASE_PATH . '/.env';
        if (!file_exists($env_file)) {
            $this->error('.env file not exists!');
            return;
        }

        $env = Dotenv::create([BASE_PATH])->load();

        $length = 16;

        $app_key = uuid($length);

        $env['APP_KEY'] = $app_key;

        $handle = fopen($env_file, 'w+');

        foreach ($env as $key => $val) {
            $name = strtoupper($key);
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $item = $name . '_' . strtoupper($k);
                    fwrite($handle, "$item=$v" . PHP_EOL);
//                    putenv("$item=$v");
                }
            } else {
//                putenv("$name=$val");
                fwrite($handle, "$name=$val" . PHP_EOL);
            }
        }

        fclose($handle);

        $this->line($app_key, 'info');
    }
}

