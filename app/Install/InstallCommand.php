<?php


namespace App\Install;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\DbConnection\Db;

/**
 * Class InstallCommand
 * @Command()
 * @package App\Install
 */
class InstallCommand extends HyperfCommand
{
    protected $name = 'admin:install';

    protected function configure()
    {
        $this->setDescription('install db from hyperf-admin.');
    }

    public function handle()
    {
        $db_conf = config('databases.default');
        if (!$db_conf || !$db_conf['host']) {
            $this->output->error('place set db config in env');
        }

        $sql = file_get_contents(__DIR__ . '/install.sql');
        $re = Db::connection('default')->getPdo()->exec($sql);

        $this->output->success('hyperf-admin db install success');
    }
}