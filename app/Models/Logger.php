<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class Logger extends BaseModel
{
    //表名
    protected $_table = 'visitor_log';
    //主键
    protected $_primaryKey = 'log_id';
    
    protected $_connect = 'static';
}