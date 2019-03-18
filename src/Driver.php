<?php
/**
 * Created by PhpStorm.
 * 在原thinkphp框架作者liu21st <liu21st@gmail.com>代码基础上做的修改
 * User: liupan
 * Email: 498501258@qq.com
 * Date: 2019/3/14
 * Time: 17:52
 */
namespace liupanv\think\theme;


use liupanv\think\theme\library\Template;
use think\App;
use think\exception\TemplateNotFoundException;
use think\Loader;
use think\Template as TemplateDefault;

class Driver
{
    // 模板引擎实例
    private $template;
    private $app;

    // 模板引擎参数
    protected $config = [
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
        'auto_rule'   => 1,
        // 视图基础目录（集中式）
        'view_base'   => '',
        // 模板起始路径
        'view_path'   => '',
        // 模板文件后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr'   => DIRECTORY_SEPARATOR,
        // 是否开启模板编译缓存,设为false则每次都会重新编译
        'tpl_cache'   => true,
    ];

    public function __construct(App $app, $config = [])
    {
        $this->app    = $app;
        $this->config = array_merge($this->config, (array) $config);
        $modulePath = $app->getModulePath();
        if(!config('?think_theme.is_open') || !config('think_theme.is_open'))
        {
            if (empty($this->config['view_path'])) {
                $this->config['view_path'] = $modulePath . 'view' . DIRECTORY_SEPARATOR;
            }
            $this->template = new TemplateDefault($app, $this->config);
            return;
        }
        $this->config['view_path'] = $modulePath.config('think_theme.theme_path') . DIRECTORY_SEPARATOR.config('think_theme.current') . DIRECTORY_SEPARATOR;
        if(config('?think_theme.callback') && is_callable(config('think_theme.callback')))
        {
            config('think_theme.callback')();
        }
        $this->template = new Template($app, $this->config);
    }

    /**
     * 检测是否存在模板文件
     * @access public
     * @param  string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists($template)
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }

        return is_file($template);
    }

    /**
     * 渲染模板文件
     * @access public
     * @param  string    $template 模板文件
     * @param  array     $data 模板变量
     * @param  array     $config 模板参数
     * @return void
     */
    public function fetch($template, $data = [], $config = [])
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }
        // 模板不存在 抛出异常
        if (!is_file($template)) {
            throw new TemplateNotFoundException('template not exists:' . $template, $template);
        }
        // 记录视图信息
        $this->app
            ->log('[ VIEW ] ' . $template . ' [ ' . var_export(array_keys($data), true) . ' ]');

        $this->template->fetch($template, $data, $config);
    }

    /**
     * 渲染模板内容
     * @access public
     * @param  string    $template 模板内容
     * @param  array     $data 模板变量
     * @param  array     $config 模板参数
     * @return void
     */
    public function display($template, $data = [], $config = [])
    {
        $this->template->display($template, $data, $config);
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param  string $template 模板文件规则
     * @return string
     */
    private function parseTemplate($template, $config = [], $isJumpDefault = 1)
    {
        $oriTemplate = $template;
        if(empty($config))
        {
            $config = $this->config;
        }
        // 分析模板文件规则
        $request = $this->app['request'];

        // 获取视图根目录
        if (strpos($template, '@')) {
            // 跨模块调用
            list($module, $template) = explode('@', $template);
        }

        if ($config['view_base']) {
            // 基础视图目录
            $module = isset($module) ? $module : $request->module();
            $path   = $config['view_base'] . ($module ? $module . DIRECTORY_SEPARATOR : '');
        } else {
            $path = isset($module) ? $this->app->getAppPath() . $module . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR : $config['view_path'];
        }

        $depr = $config['view_depr'];

        if (0 !== strpos($template, '/')) {
            $template   = str_replace(['/', ':'], $depr, $template);
            $controller = Loader::parseName($request->controller());

            if ($controller) {
                if ('' == $template) {
                    // 如果模板文件名为空 按照默认规则定位
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $this->getActionTemplate($request, $config);
                } elseif (false === strpos($template, $depr)) {
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                }
            }
        } else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }

        $fullPath = $path . ltrim($template, '/') . '.' . ltrim($config['view_suffix'], '.');

        if (config('?think_theme.is_open') && config('think_theme.is_open') && $isJumpDefault && !is_file($fullPath)) {
            $modulePath = \think\facade\App::getModulePath();
            $config['view_path'] = $modulePath.config('think_theme.theme_path') . DIRECTORY_SEPARATOR.config('think_theme.default') . DIRECTORY_SEPARATOR;
            return $this->parseTemplate($oriTemplate, $config,0);
        }
        return $fullPath;
    }

    protected function getActionTemplate($request, $config)
    {
        $rule = [$request->action(true), Loader::parseName($request->action(true)), $request->action()];
        $type = $config['auto_rule'];

        return isset($rule[$type]) ? $rule[$type] : $rule[0];
    }

    /**
     * 配置或者获取模板引擎参数
     * @access private
     * @param  string|array  $name 参数名
     * @param  mixed         $value 参数值
     * @return mixed
     */
    public function config($name, $value = null)
    {
        if (is_array($name)) {
            $this->template->config($name);
            $this->config = array_merge($this->config, $name);
        } elseif (is_null($value)) {
            return $this->template->config($name);
        } else {
            $this->template->$name = $value;
            $this->config[$name]   = $value;
        }
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->template, $method], $params);
    }

    public function __debugInfo()
    {
        return ['config' => $this->config];
    }
}