<?php

namespace App\Models\Admin;

use App\Models\Base as BaseModel;

class Logger extends BaseModel
{
    //表名
    protected $_table = 'admin_logger';
    //主键
    protected $_primaryKey = 'log_id';
    //库
    protected $_connect = 'static';

    const TYPE_LOGIN = 0; //登录
    const TYPE_LOGOUT = 1; //登出
}