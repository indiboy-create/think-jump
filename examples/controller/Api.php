<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;

class Api extends BaseController
{
    public function successApi()
    {
        return $this->success('操作成功', null, ['id' => 1]);
    }

    public function errorApi()
    {
        return $this->error('参数错误', null, ['field' => 'name']);
    }

    public function resultApi()
    {
        return $this->result(['list' => [['id' => 1, 'name' => 'think-jump']]], 0, 'ok', 'json');
    }
}