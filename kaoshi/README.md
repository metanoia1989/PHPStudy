# 模拟考试系统
安卓消防学院的考试系统，用的就是这套框架。      
主要了解路由定义以及数据库操作，然后修改控制器逻辑就差不多了。      
这个模拟考试系统的应用框架没啥文档，基本上都要去看源码。    

最主要的是一个文件 `kaoshi\phpems\lib\init.cls.php`，定义了抽象工厂。


## 路由分析
教师后台：            
课程管理 http://phpems.test/index.php?course-teach-course   
考试课程成绩 http://phpems.test/index.php?exam-teach-users  

管理员后台：        
全局、路由、考试、专题、文件、内容、财务、课程、证书、文档      
全局-首页 http://phpems.test/index.php?core-master      
全局-模块管理 http://phpems.test/index.php?core-master-apps     
全局-模块管理-模块设置 http://phpems.test/index.php?core-master-apps-config&appid=autoform      

用户-首页  http://phpems.test/index.php?user-master     
用户-用户管理 http://phpems.test/index.php?user-master-user     
用户-用户管理-修改用户 http://phpems.test/index.php?user-master-user-modify&userid=2&page=1     
用户-用户组管理 http://phpems.test/index.php?user-master-actor      
用户-模块设置 http://phpems.test/index.php?user-master-config       
用户-模型管理 http://phpems.test/index.php?user-master-module       

用户后台：  
新闻列表 http://phpems.test/index.php?content
新闻分类列表 http://phpems.test/index.php?content-app-category&catid=15     
新闻详情页 http://phpems.test/index.php?content-app-content&contentid=85        
专题列表 http://phpems.test/index.php?seminar       
专题分类列表 http://phpems.test/index.php?seminar-app-category&catid=24    内容跟上面一样
专题详情页 http://phpems.test/index.php?seminar-app-seminar&seminarid=1     
课程列表 http://phpems.test/index.php?course        
课程详情页 http://phpems.test/index.php?course-app-course&csid=3     
考试列表 http://phpems.test/index.php?exam      
考场相关 http://phpems.test/index.php?exam-app-lesson        
文档页面 http://phpems.test/index.php?docs      
文档分类页 http://phpems.test/index.php?docs-app-category&catid=19      
文档详情页 http://phpems.test/index.php?docs-app-docs&docid=4           
个人中心 http://phpems.test/index.php?user-center        
个人设置 http://phpems.test/index.php?user-center-privatement        

从这个路由形式来看，没有严格的前端和后端，都是统一的。     

路由分析 http://phpems.test/index.php?user-center-privatement-password      
第一个是模块名，app下各个目录名就是。   
第二个是控制器名，模块目录下的php文件就是控制器，这个类是控制器类的基类，其他控制器方法类都会继承这个类。            
第三个是控制器方法类文件，controllers目录下 以控制器名结尾的php文件 就是      
第四个是具体的控制器方法，默认为index。     


## 添加模块
模块控制命名方式 master api phone 
app 是前台页面的命名，如 `index.php?content-app` 新闻列表页。只写模块名，默认控制器为app。  
master 是后台管理的命名，如 `index.php?content-master` 内容管理。        
phone 是手机页面的命名，如 `index.php?content-phone=` 手机端列表页.    
api 不太清楚，有几处是进行跳转的，在user模块下的一个页面是废弃的。        

## 模板
控制器注入变量到模板，并渲染模板：
```php
$this->tpl->assign('topseminars',$topseminars);
$this->tpl->assign('categories',$this->category->categories);
$this->tpl->assign('contents',$contents);
$this->tpl->assign('catids',$catids);
$this->tpl->display('index');
```

下面是看模板文件提取出来的模板语法
```php
# 包含其他模板文件
{x2;include:header}
{x2;include:nav}
{x2;include:footer}

# 渲染变量
{x2;$_user['username']}

# 条件渲染
{x2;if:$block['blocktype'] == 1}
{x2;elseif:$block['blocktype'] == 2}
{x2;else}
{x2;endif}

# 列表渲染
{x2;tree:$contents,contents,cid}
    {x2;tree:v:contents['data'],content,cid}
        {x2;v:content['contenttitle']}
    {x2;endtree}
{x2;endtree}

# HTML渲染
{x2;realhtml:$cat['catdesc']}

# 声明变量
{x2;eval: v:cat = $catids[v:key]}
<h4 class="bigtitle">{x2;v:cat['catname']}</h4>
```

# 数据库操作
一种是传递数组， 各个键如下，有数据表、排序条件、where条件等，比较复杂的是条件query，是个二维数组。如果要进行一些复杂的数据库操作，需要弄清楚。     
一种是位置参数，这个我就不太清楚了，要好好的看一下。            
```php
# 传递数组
$data = array(
    'select' => false,
    'table' => 'content',
    'query' => $args,
    'orderby' => $order
);
$r = $this->db->listElements($page,$number,$data);

# 位置参数
$data = array(false,'content',array(array('AND',"contentid < :contentid",'contentid',$id),array('AND',"contentcatid = :catid",'catid',$catid)),false,"contentid DESC",5);
$sql = $this->pdosql->makeSelect($data);
$r['pre'] = $this->db->fetchAll($sql);
```