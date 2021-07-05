<?php 

namespace App\Services\Admin;

use App\Services\MemberService as BaseService;
use App\Models\Admin\Member;

class MemberService extends BaseService
{	
	public function __construct(Member $model)
    {
        $this->baseModel =  $model;
    }

    public function create($data)
    {
    	if (empty($data['password'])) return false;

		$data['salt'] = $this->getSalt();

		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_BCRYPT);

		$data['status'] = 1;
		$data['create_at'] = now();

		return $this->baseModel->insertGetId($data);
    }

    public function updateData($id, $data)
    {
    	if (empty($id) || empty($data['password'])) return false;

		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_BCRYPT);
		
		$data['update_at'] = time();

		return $this->baseModel->updateDataById($id, $data);
    }
}