<?php
namespace Lonicera\core;

class Model
{
    public function getRealTableName($tableName, $prefix = '')
    {
        if (!empty($prefix)) { // 表前缀处理
            $realTableName = $prefix."_{$tableName}";
        } elseif (isset($GLOBALS['_config']['db']['prefix']) && !empty($GLOBALS['_config']['db']['prefix'])) {
            $realTableName = $GLOBALS['_config']['db']['prefix']."_{$tableName}";
        } else {
            $realTableName = $tableName;
        }
        return $realTableName;
    }

    /**
     * 根据表名，从数据区取information_schema库其表的源数据字段MetaData，然后自动生成Model文件
     *
     * @param string $tableName
     * @param string $prefix
     * @return void
     */
    public function buildPO($tableName, $prefix = '')
    {
        $db = DB::getInstance($GLOBALS['_config']['db']);
        $ret = $db->query('SELECRT * FROM `information_schema`.`COLUMNS` WHERE TABLE_NAME=:TABLENAME', 
            [ 'TABLENAME' => $this->getRealTableName($tableName, $prefix)]);
        $className = ucfirst($tableName); // 首字母大小，暂不考虑驼峰命名法
        $file = _APP.'model/'.$className.'.php';
        $classString = "<?php\r\nclass $className extends Model{ \r\n";
        foreach ($ret as $key => $value) {
            $classString .= 'public $'."{$value['COLUMN_NAME']};";
            if (!empty($value['COLUMN_COMENT'])) {
                $classString .= "           // {$value['COLUMN_COMMENT']}";
            }
            $classString .= "\r\n";
        }
        $classString .= "}";
        file_put_contents($file, $classString);
    }

    /**
     * 根据PO的类名反向生成表名
     *
     * @param ReflectionClass $reflect
     * @return string
     */
    public function getTableNameByPO($reflect)
    {
        // 反向降解过程，从类名生成表名，暂不考虑多个单词下的驼峰规则需要考虑命名空间的问题
        return $this->getRealTableName(strtolower($reflect->getShortName()));
    }

    /**
     * 保存操作
     *
     * @return void
     */
    public function save()
    {
        $reflect = new \ReflectionClass($this);
        // 只获取 PUBLIC 字段，约定所有表字段对应的类属性使用 PUBLIC 修饰符 
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $sqlTemplate = "INSERT INTO ".$this->getTableNameByPO($reflect)."(";
        $keyArray = array_column($props, 'name');
        $keys = implode(',', $keyArray);
        $prepareKeys = implode(',', array_map(function ($key) {
            return ':'. $key;
        }, $keyArray));
        $sqlTemplate = "INSERT INTO ".$this->getTableNameByPO($reflect). "({$keys}) VALUES ($prepareKeys)";
        $data = [];
        foreach ($props as $v) {
            $data[$v->name] = $reflect->getProperty($v->name)->getValue($this);
        }
        $db = DB::getInstance($GLOBALS['_config']['db']);
        $ret = $db->execute($sqlTemplate, $data);
        return $ret;
    }

    /**
     * 根据主键删除
     *
     * @return void
     */
    public function deleteByPid()
    {

    }

    public function update()
    {

    }

    public function find()
    {

    }

    public function buildPrimaryWhere()
    {

    }
}