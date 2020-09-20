<?php

namespace HPlus\Admin\Contracts;


interface PermissionInterface
{
    /**
     * 检测对用户开放
     * @param $url
     * @return bool
     */
    public function isUserOpen($url): bool;

    /**
     * 检测用户权限
     * @param $userId
     * @param $iss
     * @param $url
     * @return bool
     */
    public function hasPermission($method, $route, $allPermission = []): bool;

    /**
     * 初始化或重置菜单至缓存
     * @param bool $reload
     * @return mixed
     */
    public function hasRole($slug, $userId = null): bool;

    /**
     * 移除忽略
     * @param $url
     * @return mixed
     */
    public function removeIgnore($url);

    /**
     * 设置对登录用户开放
     * @param $url
     * @return mixed
     */
    public function setUserOpen($url);

    /**
     * 设置忽略授权
     * @param $url
     * @return mixed
     */
    public function setIgnore($url);


    /**
     * 检测是否开放
     * @param $currUrl
     * @return mixed
     */
    public function isOpen($currUrl);

    /**
     * 获取所有忽略的节点
     * @return mixed
     */
    public function getIgnore();

    /**
     * 获取用户角色组
     * @param null $userId
     * @return mixed
     */
    public function getUserRoles($userId = null): array;

    /**
     * 重载用户权限
     * @param $id
     * @return mixed
     */
    public function reloadUser($id);

    /**
     * 重载角色权限缓存
     * @param bool $reload
     * @return mixed
     */
    public function loadRoles($reload = false);

    /**
     * 检测路有权限
     * @param $method
     * @param $route
     * @return mixed
     */
    public function can($method, $route): bool;
}