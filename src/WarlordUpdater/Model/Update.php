<?php
namespace WarlordUpdater\Model;

class Update
{
    public $id;
    public $patch_file;
    public $created_at;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->patch_file = (isset($data['patch_file'])) ? $data['patch_file'] : null;
        $this->created_at  = (isset($data['created_at'])) ? $data['created_at'] : null;
    }
}