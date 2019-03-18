<?php
/**
 * Created by PhpStorm.
 * User: liupan
 * Email: 498501258@qq.com
 * Date: 2019/3/14
 * Time: 17:52
 */
return [
    'is_open' => false,  //是否启用多主题配置，false为不开启，走框架默认的模板驱动
    'current' => 'view',  //必填，当前主题名称，可在代码里动态赋值来改变当前主题
    'default' => 'view',  //必填，默认主题名称
    'theme_path' => '',   //必填，可为空，主题目录地址，上级是对应的模块地址，如填theme，默认主题的路径则为application/theme/default
    //'callback' => function(){} //选填，实例化模板引擎的时候执行的方法，根据自己需求或许可能用到
];