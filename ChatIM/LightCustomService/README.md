# 辰光客服聊天系统
在线体验地址：http://wx.whtime.net/ 

=_= 感觉这货的系统也是拿 wolive 改的，数据表的前缀都没有变，简直了。            

看了看 data.sql 的语句挺好的，我经常希望把SQL语句写到README.md，导致README变的很长，后续的话我单独把变更写到sql文件里。         
一般PHP的web应用，都提供了安装服务，`public/install.php` 的代码值得学习参考，一般都是填写账号密码和配置，然后进行文件读写和数据库生成。         


# 安装教程
设置网站根目录为 `/public`以及TP伪静态，然后访问 `install.php`，填写安装设置。        
然后会自动生成数据库和相关账号，后台新建客服系统。  

然后生成客服系统的访问地址 http://wolive.test/admin/login/index/business_id/1.html
点击访问，会跳转辰光的授权页面，=_= 没找到跳转代码。    

## 域名授权破解
暴力枚举，一个个源码来读，就不信找不到。    
=_= 代码太多了，能力有限，还是从那个页面地址来分析吧。  
```php
//**************************************************************
// 首页页面跳转分析
//**************************************************************
// application\admin\controller\Login.php
/**
 * 登陆首页.
 *
 * @return string
 */
public function index()
{
    $token  = Cookie::get('service_token');
    if ($token) {
        $this->redirect(url('admin/index/index'));
    }
    //....问题就在上面这个方法里，上面代码注释掉能正常显示  
}

// application\admin\controller\Index.php
/**
 * 后台首页.
 *
 * @return mixed
 */
public function index()
{
    // 上面的代码都没啥问题，我返回之前断点输出，是能正常输出而不跳转的  
    return $this->fetch();
}

// application\admin\view\index\index.html
// 问题就出在 header 上，把这一行删掉就没问题了 
{include file="public/header"/}
```

有三个js代码是混淆加密的，解密之后就可以了，其中一个 `swfobject.js` 好像是不能用的，取消注释就好了。            
经过 `crack.js` 解混淆，结果一堆的报错，所以用直接混淆版的在线简单解密就好，然后一些逻辑看解混淆之后的。    

客户咨询页面访问分析
```php
//***************************************************
// http://wolive.test/admin/index/chats.html
// application\admin\view\index\chats.html
//***************************************************
<script type="text/javascript" src="https://img.phpym.net/kefu/chat.js?v=1.3"></script>
```
=_= 很多页面都有这种远程加载的JS，只能一个个下载到本地来了，然后注释掉其中跳转的代码。    
JS资源加载地址都是 `https://img.phpym.net/kefu/ + 文件名`       

## 一些访问报错的地址
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

客户管理页面，添加分组，点击添加时，会报错。    
http://wolive.test/admin/custom/%7B:url(%22admin/custom/editGroup%22)%7D 404 (Not Found)
```php
// http://wolive.test/admin/custom/index.html
// 修改 public\assets\js\chenguang\custom_index.js 657行为正确的地址即可
var url =  CGWL_ROOT_URL + "/admin/custom/editGroup";
```

然后点击新添加的分组也是报错
```php
// http://wolive.test/admin/custom/visiter?group_id=1&page=1
SQLSTATE[HY000]: General error: 3065 Expression #1 of 
ORDER BY clause is not in SELECT list, references column 'wolive.v.timestamp' which is not in SELECT list; this is incompatible with DISTINCT
// 应该是跟上面同样的错误，查询字段没有orderby。    
```