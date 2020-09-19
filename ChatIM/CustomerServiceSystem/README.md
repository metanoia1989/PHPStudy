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

# 技术原理分析
数据表设计，各个数据表的字段是怎样的，用处是什么，有哪些精妙的设计？        
主要有几个资源管理，客服资源、客户资源、问候语、常见问题、下班等细节问题。      
客户与客服对话的前后端交互式怎么样的，Workerman是怎么进行消息分发的。   
聊天框部署，标准及迷你窗口、生成js、生成HTML、微信公众号链接、客服专属链接。        
