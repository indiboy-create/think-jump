<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;

class Demo extends BaseController
{
    public function index()
    {
        return 'try /demo/successDemo, /demo/errorDemo, /demo/resultDemo, /demo/redirectDemo';
    }

    public function successDemo()
    {
        return $this->success('操作成功', 'demo/index');
    }

    public function successWithDataDemo()
    {
        return $this->success('操作成功', 'demo/index', ['id' => 1], 3);
    }

    public function errorDemo()
    {
        return $this->error('操作失败');
    }

    public function errorWithUrlDemo()
    {
        return $this->error('操作失败', 'demo/index');
    }

    public function resultDemo()
    {
        return $this->result(['id' => 1, 'name' => 'think-jump']);
    }

    public function redirectDemo()
    {
        return $this->redirect('/index/index');
    }
}