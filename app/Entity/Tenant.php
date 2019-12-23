<?php
declare(strict_types=1);

namespace App\Entity;


class Tenant
{
    const TENANT_TYPE_WE = 0;
    const TENANT_TYPE_MI = 1;
    /**
     * 租户ID
     * @var int
     */
    protected int $id = 1;
    /**
     * 公众号类型
     * @var int
     */
    protected int $type = 0;
    /**
     * 租户名称
     * @var string
     */
    protected string $name = '';
    /**
     * 过期时间
     * @var int
     */
    protected int $expires = 0;

    /**
     * 微信AppId
     * @var string
     */
    protected string $appId = '';

    /**
     * 小程序AppId
     * @var string
     */
    protected string $miniAppId = '';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     */
    public function setExpires(int $expires): void
    {
        $this->expires = $expires;
    }

    /**
     * @return string
     */
    public function getMiniAppId(): string
    {
        return $this->miniAppId;
    }

    /**
     * @param string $miniAppId
     */
    public function setMiniAppId(string $miniAppId): void
    {
        $this->miniAppId = $miniAppId;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }


}