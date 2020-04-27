# PHP学习说明
到新的公司，需要快速熟悉掌握Laravel框架，尽快熟悉公司的内部系统及相关业务。         
建立这个项目，用来存放学习过程中的敲的代码。        

创建完Laravel应用之后，在nginx配置文件添加 在 `location / { }` 中的关键代码：
```php
if (!-e $request_filename) {
   rewrite  ^(.*)$  /index.php?s=/$1  last;
   break;
}
```


