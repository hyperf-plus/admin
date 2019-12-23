<?php
declare(strict_types=1);


namespace App\Service;


use App\Entity\Tenant;

class TenantService
{

    public function getInfo(int $tenant_id): Tenant
    {
        $Tenant = new Tenant();
        $Tenant->setId(1);
        $Tenant->setMiniAppId('miniappid');
        $Tenant->setName("测试SaaS商户");
        $Tenant->setExpires(time() + 3600 * 24);
        return $Tenant;
    }
}