<?php

namespace App\Models\Admin;

use App\Models\Member as BaseModel;

class Member extends BaseModel
{
    //表名
    protected $_table = 'admin_member';
    //主键
    protected $_primaryKey = 'mem_id';
}