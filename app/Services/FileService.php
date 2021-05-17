<?php

namespace App\Services;

class FileService
{
    const FILE_TYPE = ['avatar', 'product', 'banner', 'introduce', 'category'];
    const FILE_ACCEPT = ['jpg', 'jpeg', 'png'];
    const FILE_COMPERSS = ['jpg', 'jpeg', 'png'];
    const MAX_OFFSET = 1200;

    public function upload($file, $cate, $thumb = true)
    {
        if (!in_array($cate, self::FILE_TYPE)) return false;
        $ext = explode('/', $file['type'])[1] ?? '';
        if (!in_array($ext, self::FILE_ACCEPT)) {
            return false;
        }
        $name = md5_file($file['tmp_name']);
        $attachmentService = make('App\Services\AttachmentService');
        $data = $attachmentService->getAttachmentByName($name, 200);
        if (empty($data)) {
            $path = ROOT_PATH . env('FILE_CENTER') . DS . $cate . DS;
            //创建目录
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $saveUrl = $path . $name . '.' . $ext;
            $result = move_uploaded_file($file['tmp_name'], $saveUrl);
            if (!$result) {
                return false;
            }
            $imageService = make('App\Services\ImageService');
            $imageService->compressImg($saveUrl);
            $data = [
                'name' => $name,
                'type' => $ext,
                'cate' => $cate,
                'size' => filesize($saveUrl),
            ];
            $attachId = $attachmentService->create($data);
            $data['attach_id'] = $attachId;
            //图片缩略
            if ($thumb) {
                $thumb = ['600', '400', '200'];
                foreach ($thumb as $value) {
                    $to = $path . $name . DS . $value . '.' . $ext;
                    $imageService->thumbImage($saveUrl, $to, $value, $value);
                }
            }
            $data = $attachmentService->urlInfo($data, 200);
        }
        return $data;
    }

    public function uploadUrlImage($url, $cate, $thumb = true)
    {
        if (!in_array($cate, self::FILE_TYPE)) return false;
        //生成临时文件
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $tempName = ROOT_PATH.env('FILE_CENTER').DS.\frame\Str::getUniqueName().'.'.$ext;
        if (file_put_contents($tempName, file_get_contents($url))) {
            $name = md5_file($tempName);
            $attachmentService = make('App\Services\AttachmentService');
            $data = $attachmentService->getAttachmentByName($name);
            if (empty($data)) {
                $path = ROOT_PATH . env('FILE_CENTER') . DS . $cate . DS;
                //创建目录
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $file = $path . $name . '.' . $ext;
                //存入压缩文件
                $imageService = make('App\Services\ImageService');
                $imageService->compressImg($tempName, $file);
                $data = [
                    'name' => $name,
                    'type' => $ext,
                    'cate' => $cate,
                    'size' => filesize($file),
                ];
                $attachId = $attachmentService->create($data);
                $data['attach_id'] = $attachId;
                //图片缩略
                if ($thumb) {
                    $thumb = ['600', '400', '200'];
                    foreach ($thumb as $value) {
                        $to = $path . $name . DS . $value . '.' . $ext;
                        $imageService->thumbImage($file, $to, $value, $value);
                    }
                }
                $data = $attachmentService->urlInfo($data, 200);
            }
            unlink($tempName);
            return $data;
        }
        return false;
    }
}
