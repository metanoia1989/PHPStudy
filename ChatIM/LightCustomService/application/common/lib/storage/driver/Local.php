<?php
/**
 * @copyright ©2019 辰光PHP客服系统
 * Created by PhpStorm.
 * User: Andy - Wangjie
 * Date: 2019/6/17
 * Time: 15:37
 */
namespace app\common\lib\storage\driver;

use app\common\lib\storage\Driver;
use app\common\lib\storage\StorageException;

class Local extends Driver
{
    protected $base_root = null;

    public function __construct()
    {
        $basename = request()->root();
        if (pathinfo($basename, PATHINFO_EXTENSION) == 'php') {
            $basename = dirname($basename);
        }
        $this->base_root = $basename;
        parent::__construct();
    }

    public function put()
    {
        $info = $this->file->move(ROOT_PATH."/public".$this->saveFileFolder,uniqid().time());
        if ($info) {
            $imgname = $info->getSaveName();
            $imgpath = $this->base_root . $this->saveFileFolder."/" . $imgname;
            $this->url = $imgpath;
            $this->thumbUrl = $this->url;
        } else {
            throw new StorageException('上传失败');
        }
        return $this->save();
    }
}
