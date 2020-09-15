<?php
declare(strict_types=1);

namespace HPlus\Admin\Entity;

use HPlus\Admin\Contracts\UserInfoInterface;

/**
 *
 * Class UserInfo
 * @package App\Entity
 */
class UserInfo extends EntityBean implements UserInfoInterface
{
    /**
     * @var integer
     */
    protected $id;
    /**
     * @var integer
     */
    private $userId;
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $realname;

    /**
     * @var string
     */
    protected $mobile;

    /**
     * @var bool
     */
    protected $status = false;
    /**
     * @var bool
     */
    protected $is_admin = false;
    /**
     * @var string
     */
    protected $avatar;

    /**
     * @var array
     */
    protected $attribute;


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
    public function setId($id): void
    {
        $this->id = intval($id);
        $this->userId = intval($id);
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return intval($this->userId);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username ?? '';
    }

    /**
     * @param string $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getRealname(): string
    {
        return $this->realname ?? '';
    }

    /**
     * @param string $realname
     */
    public function setRealname($realname): void
    {
        $this->realname = $realname;
    }

    /**
     * @return string
     */
    public function getMobile(): string
    {
        return $this->mobile ?? '';
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status): void
    {
        $this->status = $status == 1;
    }

    /**
     * @return bool
     */
    public function isIsAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * @param bool $is_admin
     */
    public function setIsAdmin($is_admin): void
    {
        $this->is_admin = $is_admin == 1;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar ?? '';
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAttribute($field = null, $default = null)
    {
        if ($field === null) {
            return $this->attribute;
        }
        // TODO: Implement getData() method.
        return $this->attribute[$field] ?? $default;
    }

    public function setAttribute($data, $value = null)
    {
        if (is_string($data)) {
            $this->attribute[$data] = $data;
        } else {
            $this->attribute = array_merge($this->attribute, $data);
        }
    }
}
