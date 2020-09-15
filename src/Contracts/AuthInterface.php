<?php

namespace Mzh\Admin\Contracts;


interface AuthInterface
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
    public function hasPermission($userId, $iss, $url): bool;

    /**
     * 初始化或重置菜单至缓存
     * @param bool $reload
     * @return mixed
     */
    public function loadAuth($reload = false);

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
     * 扫描路由注解
     * @return mixed
     */
    public function killCache();


}