<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class Message extends BaseModel
{
	//表名
    protected $table = 'message';
    //主键
    protected $primaryKey = 'message_id';
}