# 禾匠点企来客服系统
=_= 感觉这货的系统也是拿 wolive 改的，数据表的前缀都没有变，简直了。            

看了看 data.sql 的语句挺好的，我经常希望把SQL语句写到README.md，导致README变的很长，后续的话我单独把变更写到sql文件里。         
一般PHP的web应用，都提供了安装服务，`public/install.php` 的代码值得学习参考，一般都是填写账号密码和配置，然后进行文件读写和数据库生成。         

# 安装教程
设置网站根目录为 `/public`以及TP伪静态，然后访问 `install.php`，填写安装设置。        
然后会自动生成数据库和相关账号，后台新建客服系统。  

然后生成客服系统的访问地址 http://wolive.test/admin/login/index/business_id/1.htm


结果刚一访问就遇到问题了，我先解决再来记录。
页面直接显示报错信息：
```php
Array and string offset access syntax with curly braces is deprecated
```
=_= 原来是版本太高了，使用 PHP4.3 导致TP5出现问题了。切换成PHP3.4就没问题了。       

这个能正常使用了，前后端都有。只有实时分发消息用到websocket，其他的信息管理完全可以用传统的模式。    
看看怎么移植到教务系统那边去。这个的IM消息分发还有点问题，需要研究修复一下。          


# BUG 修复
## wolive_queue 字段查询有误
```php
// http://wolive.test/admin/set/getchats
// 报错信息如下
SQLSTATE[HY000]: General error: 3065 Expression #1 of 
ORDER BY clause is not in SELECT list, references column 'wolive.wolive_queue.timestamp' which is not in SELECT list; this is incompatible with DISTINCT
/**
 * 对话列表类.
 *
 * @return mixed
 */
public function getchats()
{
    $visiters = Admins::table('wolive_queue')
        ->distinct(true)
        ->field('visiter_id, timestamp')
        ->where(['service_id' => $login['service_id'], 'business_id' => $login['business_id']])
        ->where('state', 'normal')
        ->order('timestamp desc')
        ->select();
}
```

## zjhj_pusher 的 start.php 无法启动
`zjhj_pusher` 目录的vendor没有加入到版本管理，然后手动安装了`workerman`。
```sh
# 单独地在这个目录下进行composer初始化
composer init
composer require workerman/workerman
```

最后运行wokerman程序，但是报错：`$ php zjhj_pusher/start.php start`，出现了报错，报错信息如下：
```log
Fatal error: Uncaught Error: Class 'think\Route' not found in E:\WorkSpace\PHPStudy\ChatIM\CustomerServiceSystem\vendor\topthink\think-captcha\src\helper.php on line 12
Error: Class 'think\Route' not found in E:\WorkSpace\PHPStudy\ChatIM\CustomerServiceSystem\vendor\topthink\think-captcha\src\helper.php on line 12
```
在 `zjhj_pusher` 目录下独立的安装包就没有问题。 

然后根据目录下 composer.json 对 TP 的版本不能超过5.0，不然会有问题，所以要用`~`作为前缀，不能更新就会出大问题。=_= 及其坑爹啊。 

2020/9/24 时安装的版本是 `5.0.24`，导致模板赋值变量没有进行模板输出替换。
看到原来的版本是 `5.0.5`，降级成这个版本就可以了。         

## 生成HTML报错 
```php
//*******************************************************
// 网页部署相关页面
//*******************************************************
http://customerservice.test/admin/index/front.html
// 控制台报500错误，__style__ 没有被替换    
http://customerservice.test__style__/index/dianqilai_online.css 

// application\admin\controller\Index.php
/**
 * 生成前台文件页面.
 *
 * @return mixed
 */
public function front()
{
    $this->assign('class', $class);
    $this->assign('business', $login['business_id']);
    $this->assign('web', $web);
    $this->assign('wechat', $wechat);
    $this->assign('personal', $action.'/index/index/home?visiter_id=&visiter_name=&avatar=&business_id='.$login['business_id'].'&groupid='.$login['groupid'].'&special='.$login['service_id']);
    $this->assign('action', $action);
    $this->assign('html', $str);
    $this->assign("title", "网页部署");
    $this->assign("part", "网页部署");

    return $this->fetch();
}
// application\admin\view\index\front.html
var show =function(){
    $('#wechat-mp').addClass('hide');
    $('#personal').addClass('hide');
   $type =$("#type").val();
   if($type == 0){
      $("#codearea").removeClass('hide');
      $("#wolive-js").addClass('hide');
      $("#frontjs").removeClass('hide');
      $("#minjs").addClass('hide');
      $("#container").append('{$html}');
   }
}
// 最后输出的内容为
<link rel="stylesheet" href="http://customerservice.test__style__/index/dianqilai_online.css">
<div class="dianqilai-form" id="dianqilai-kefu">
    <i class="dianqilai-icon"></i> 
    <form class="dianqilai-item" method="post" target="_blank"
        action="http://customerservice.test/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=1&groupid=0">
        <input type="hidden" name="product"  value="">
        <input type="submit" value="在线咨询">
    </form>
</div>
```
