<?php
declare(strict_types=1);

namespace app;

use think\App;
use youvision\think\Jump;

abstract class BaseController
{
    /**
     * Request 实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;

        $this->initialize();
    }

    protected function initialize()
    {
    }

    use Jump;
}