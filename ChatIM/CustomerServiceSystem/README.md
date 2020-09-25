# 禾匠点企来客服系统
=_= 感觉这货的系统也是拿 wolive 改的，数据表的前缀都没有变，简直了。            

看了看 data.sql 的语句挺好的，我经常希望把SQL语句写到README.md，导致README变的很长，后续的话我单独把变更写到sql文件里。         
一般PHP的web应用，都提供了安装服务，`public/install.php` 的代码值得学习参考，一般都是填写账号密码和配置，然后进行文件读写和数据库生成。         

# 安装教程
设置网站根目录为 `/public`以及TP伪静态，然后访问 `install.php`，填写安装设置。          
然后会自动生成数据库和相关账号，后台新建客服系统。          

然后生成客服系统的访问地址 http://customerservice.test/admin/login/index/business_id/1.htm       
客服登录页面地址 http://customerservice.test/admin/login/index.html?business_id=1       

结果刚一访问就遇到问题了，我先解决再来记录。        
页面直接显示报错信息：
```php
Array and string offset access syntax with curly braces is deprecated
```
=_= 原来是版本太高了，使用 PHP4.3 导致TP5出现问题了。切换成PHP3.4就没问题了。       

这个能正常使用了，前后端都有。只有实时分发消息用到websocket，其他的信息管理完全可以用传统的模式。    
看看怎么移植到教务系统那边去。这个的IM消息分发还有点问题，需要研究修复一下。          

# 文件目录及数据表说明      
**文件目录**        
* application/platform 管理员后台模块     
* application/admin 客服后台模块      
* application/api         
* application/index PC端访客模块      
* application/mobile 手机端访客模块       
* application/weixin 微信端访客模块       
* application/manager 微擎超级管理员模块，没啥乱用，感觉这个后台框架好像是移植这个来的    

**数据表分析**      
* `wolive_admin` 后台管理员用户表
* `wolive_admin_token` 管理员登录TOKEN
* `wolive_business` 公司表
* `wolive_chats` 聊天历史记录表
* `wolive_comment` 用户评价表
* `wolive_comment_detail` 用户评价详情表
* `wolive_comment_setting` 评价设置表
* `wolive_group` 客服分组，分组名、公司ID
* `wolive_message` 用户留言表，在 manager 模块里有用到，但是这个系统废弃掉了，改成留姓名和电话了    
* `wolive_option` 公司客服平台配置表，名称、Logo 标题等等
* `wolive_question` 常见问题表，对话窗口右侧问题列表       
* `wolive_queue` 当前访客对话里列表，是否推送评价、是否发送模板消息
* `wolive_reply` 快捷回复表，保存快捷回复的信息
* `wolive_rest_setting` 下班设置表
* `wolive_sentence` 问候语表，访客第一次进入时发送的消息
* `wolive_service` 客服表
* `wolive_tablist` 对话窗口右侧Tab页，默认第一个为常见问题
* `wolive_vgroup` 访客分组表
* `wolive_visiter` 访客表
* `wolive_visiter_vgroup` 访客与访客分组关联表
* `wolive_wechat_platform` 公司绑定的公众号表，包含了appid、appsecret 以及模板消息ID
* `wolive_weixin` 不太确定，客服绑定微信表？



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

## 评价提交失败
http://customerservice.test/admin/event/comment     
Class 'app\admin\model\Comment' not found           

从另外一份文件里复制过来就行。=_= 这一份下载的，总是缺一两个文件，图标啊、模型类啊啥的。      

## 客户管理中获取指定分组的客户失败 
新建了一个访客分组 `wolive_vgroup`，然后访客与分组关联表 `wolive_visiter_vgroup`就有新的关联记录。但是查询的时候报错了。    
```php
// http://customerservice.test/admin/custom/visiter?group_id=1&page=1
SQLSTATE[HY000]: General error: 3065 Expression #1 of 
ORDER BY clause is not in SELECT list, references column 'customer_service.v.timestamp' which is not in SELECT list; this is incompatible with DISTINCT
就是使用 distinct 聚合函数，其order by 的字段必须放在 fields 中
// application\admin\controller\Custom.php 231行
$vids = VisiterGroup::alias('vg')->where('group_id',$group)
    ->join('wolive_visiter v','vg.vid = v.vid','left')
    ->join('wolive_queue q','q.visiter_id = v.visiter_id','left')
    ->where('vg.business_id',$this->login['business_id'])
    ->where('vg.service_id',$this->login['service_id'])
    ->where('q.state','neq','in_black_list')
    ->distinct(true)
    ->field('vg.vid, v.timestamp')
    ->order('v.timestamp','desc')
    ->paginate(20);
```