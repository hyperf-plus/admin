<?php

namespace HPlus\Admin\Install;

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
        $this->setDescription('install db from hyperf-plus-admin.');
    }

    public function handle()
    {
        $db_conf = config('databases.default');
        if (!$db_conf || !$db_conf['host']) {
            $this->output->error('place set db config in env');
            return;
        }
        $sql = file_get_contents(__DIR__ . '/install.sql');
        Db::connection('default')->getPdo()->exec($sql);
        $this->output->success('hyperf-admin db install success');
        $this->output->text('start make file');
        $content = file_get_contents(__DIR__ . '/stubs/AuthController.stub');
        if (!is_dir(BASE_PATH . '/app/Controller/Admin/')){
            @mkdir(BASE_PATH . '/app/Controller/Admin/');
        }
        file_put_contents(BASE_PATH . '/app/Controller/Admin/AuthController.php', $content);
        $this->output->success('create success!');
        $this->output->success('启动服务后访问：http://127.0.0.1:9501/auth');
        $this->output->success('更多文档请参阅:https://hyperf.plus');
    }
}