<?php
declare(strict_types=1);

namespace App\Entity;

class UserInfo extends EntityBean
{
    protected int $groupId = 0;
    protected array $groupIds = [];
    protected string $username = '';
    protected int $type = -1; //  int -1:游客 0:顾客 1:管理组
    protected int $corpId = 0;
    protected string $primaryKey = 'user_id';

    /**
     * @var string
     */
    protected ?string $nickname;

    protected int $uid = 1;

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * @return array
     */
    public function getGroupIds(): array
    {
        return $this->groupIds;
    }

    /**
     * @param array $groupIds
     */
    public function setGroupIds(array $groupIds): void
    {
        $this->groupIds = $groupIds;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
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

    /**
     * @return int
     */
    public function getCorpId(): int
    {
        return $this->corpId;
    }

    /**
     * @param int $corpId
     */
    public function setCorpId(int $corpId): void
    {
        $this->corpId = $corpId;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        switch ($this->type) {
            case 1:
                return 'admin_id';
                break;
            default:
                return 'user_id';
                break;
        }
    }

}