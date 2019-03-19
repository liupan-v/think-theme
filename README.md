ThinkPHP 5.0 多主题模板扩展
===============

## 安装
composer.json加上"liupan-v/think-theme": "1.0.*"，然后执行composer install<br/>

## 优点
1.无侵入式, 不影响原来控制器的代码<br/>
2.可以最少模板代码实现主题的制作，新增主题主要新增差异部分，其他的模板调用默认主题相应位置的，不用重复编写便于维护

## 使用方法
1.修改配置文件config.php的template.type配置，把默认的Think改为\liupanv\think\theme\Driver<br/>
2.复制本项目下extra目录到你需要开启多主题功能的模块下，并按注释进行相应配置<br/>
3.控制器或其他地方动态修改think_theme配置current的值，可实现模板的切换

## 其他
tp5.1的多主题：https://github.com/liupan-v/think-theme