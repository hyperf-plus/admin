<?php

namespace Mzh\Admin\Service;

interface ServiceInterface
{

    public function create(array $data);

    public function set($id, array $data);

    public function get($id);

    public function list($where, $page = 1, $size = 20);

}