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
namespace HPlus\Admin\Service;

use Composer\Semver\Comparator;
use Hyperf\Utils\Collection;
use Hyperf\Utils\Composer;

class PluginService
{
    protected $composer = [];

    public function __construct()
    {
        $this->composer = Collection::make(Composer::getLockContent()->get('packages'));
    }

    public function checkComposer(array $packages = [])
    {
        $noInstall = [];
        $versionErr = [];
        foreach ($packages as $package => $version) {
            $pack = $this->composer->where('name', $package)->first();
            if (! $pack) {
                $noInstall[] = [
                    'package' => $package,
                    'version' => $version,
                ];
                continue;
            }
            if (! Comparator::greaterThanOrEqualTo($pack['version'], $version)) {
                $versionErr[] = [
                    'package' => $package,
                    'need_version' => $pack['version'],
                    'current_version' => $version,
                ];
            }
        }
        return [
            empty($versionErr) && empty($noInstall),
            [
                'noInstall' => $noInstall,
                'versionErr' => $versionErr,
            ],
        ];
    }
}
