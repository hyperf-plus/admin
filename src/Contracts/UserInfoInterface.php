<?php

namespace Mzh\Admin\Contracts;


interface UserInfoInterface
{
    /**
     * @return int
     */
    public function getUserId(): int;

    /**
     * 获取用户对象
     * @param $name
     * @param $value
     * @return array
     */
    public function getAttribute($name, $value);

    /**
     * 获取用户对象
     * @param $data
     * @param null $value
     * @return array
     */
    public function setAttribute($data, $value = null);

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     */
    public function setId($id): void;

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getMobile();

    /**
     * @param string $mobile
     */
    public function setMobile($mobile);

    /**
     * @return bool
     */
    public function isStatus();

    /**
     * @param bool $status
     */
    public function setStatus($status);

    /**
     * @return bool
     */
    public function isIsAdmin();

    /**
     * @param bool $is_admin
     */
    public function setIsAdmin($is_admin);

    /**
     * @return string
     */
    public function getAvatar();

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar);


}