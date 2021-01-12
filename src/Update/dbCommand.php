<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin\Update;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\DbConnection\Db;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class InstallCommand.
 * @Command
 */
class dbCommand extends HyperfCommand
{
    protected $name = 'admin:db';

    public function configure()
    {
        parent::configure();
        $this->addUsage('--connection 数据库连接');
        $this->addUsage('--update 升级至xx版本');
        $this->addUsage('--list 可更新指令列表');
        // $this->addUsage('--f 忽略错误，强制执行');
        $this->addOption('update', 'u', InputOption::VALUE_OPTIONAL, '版本号，输入此选项会更新此版本的sql升级文件');
        $this->addOption('list', 'l', InputOption::VALUE_OPTIONAL, '可更新指令列表', true);
        $this->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, '数据库连接', 'default');
        // $this->addOption('force', 'f', InputOption::VALUE_OPTIONAL, '忽略错误，强制执行');
        $this->setDescription('update db from hyperf-plus-admin.');
    }

    public function handle()
    {
        $db_conf = config('databases.default');
        if (! $db_conf || ! $db_conf['host']) {
            $this->output->error('place set db config in env');
            return;
        }
        switch (true) {
            case ! $this->input->getOption('list'):
                $list = $this->search(__DIR__ . '/db/');
                $this->output->table([['编号', '升级指令', '升级内容']], $list);
                break;
            case $this->input->getOption('update'):
                $update = $this->input->getOption('update');
                $this->output->info('开始升级版本' . $update);
                $connection = $this->input->getOption('connection');
                $path = __DIR__ . '/db/' . $update . '.sql';
                if (! is_file($path)) {
                    $this->output->error($update . '版本不存在,使用 php bin/hyperf.php admin:db -l 查看可升级的版本号');
                    return;
                }
                $sql = file_get_contents($path);
                Db::connection($connection)->getPdo()->exec($sql);
                $this->output->success('升级至' . $update . '成功！');
                break;
        }
    }

    private function search($dir)
    {
        return array_filter(array_map(function ($item) use ($dir) {
            if (strpos($item, '.sql') != 0) {
                $sql = file_get_contents($dir . $item);
                $version = str_replace('.sql', '', $item);
                return [
                    $version,
                    'php bin/hyperf.php admin:db -u ' . $version,
                    current(explode(PHP_EOL, $sql)),
                ];
            }
        }, scandir($dir)));
    }
}
