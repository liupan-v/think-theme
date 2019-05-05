ThinkPHP 5.1 多主题模板扩展
===============

## 安装
composer require liupan-v/think-theme 2.*

## 优点
1.无侵入式, 不影响原来控制器的代码<br/>
2.可以最少模板代码实现主题的制作，新增主题主要新增差异部分，其他的模板调用默认主题相应位置的，不用重复编写便于维护

## 使用方法

复制本项目下config目录下的think_theme.php配置文件到你需要开启多主题功能的模块config目录下，并按注释进行相应配置<br/>
控制器或其他地方动态修改think_theme配置current的值，可实现模板的切换<br/>

##### 举个栗子：

1. 新建一个tp5项目： composer create-project topthink/think tp5test (tp5test是项目目录，cd进入这个目录)
2. 安装主题插件：composer require liupan-v/think-theme 2.*
3. 复制配置文件：vendor/liupan-v/think-theme/config/think_theme.php复制到application/index/config/think_theme.php
4. 修改think_theme.php配置信息：
	    return [
	    'is_open' => true, //是否启用多主题配置，false为不开启，走框架默认的模板驱动
	    'current' => 'theme1', //必填，当前主题名称，可在代码里动态赋值来改变当前主题
	    'default' => 'default', //必填，默认主题名称
	    'theme_path' => 'theme', //必填，可为空，主题目录地址，上级是对应的模块地址，如填theme，默认主题的路径则为application/模块名/theme/default
	];
5. 模板文件路径application/index/theme/theme1

## 注意事项

不要在要开启多主题的模块下，修改配置文件的template.type配置信息，即template.php配置里的type配置，否则多主题不会生效

## 其他
tp5.0的多主题：https://github.com/liupan-v/think-theme/tree/tp5.0
