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

namespace HPlus\Admin\Install;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\DbConnection\Db;

/**
 * Class InstallCommand.
 * @Command
 */
class updateCommand extends HyperfCommand
{
    protected $name = 'admin:update';

    public function handle()
    {
        $db_conf = config('databases.default');
        if (!$db_conf || !$db_conf['host']) {
            $this->output->error('place set db config in env');
            return;
        }
        $sql = file_get_contents(__DIR__ . '/update.sql');
        Db::connection('default')->getPdo()->exec($sql);
        $this->output->success('hyperf-admin db update success');
    }

    protected function configure()
    {
        $this->setDescription('install db from hyperf-plus-admin.');
    }
}
