<?php

namespace App\Models;

use App\Models\Base as BaseModel;

/**
 * 消息组成员
 */
class MessageMember extends BaseModel
{
	//表名
    protected $table = 'message_member';

    //主键
    protected $primaryKey = 'item_id';
}