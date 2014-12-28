<?php
namespace WarlordUpdater\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class UpdateTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	public function getUpdate($id)
	{
		$id = (int)$id;
		$rowset = $this->tableGateway->select(array(
			'id' => $id
		));
		$row = $rowset->current();
		if(! $row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}

	public function saveUpdate(Update $update)
	{
		$data = array(
			'patch_file' => $update->patch_file,
			'created_at' => $update->created_at
		);
		
		$id = (int)$update->id;
		if($id == 0) {
			$this->tableGateway->insert($data);
		} else {
			if($this->getUpdate($id)) {
				$this->tableGateway->update($data, array(
					'id' => $id
				));
			} else {
				throw new \Exception('Form id does not exist');
			}
		}
	}

	public function deleteUpdate($id)
	{
		$this->tableGateway->delete(array(
			'id' => $id
		));
	}

	public function query($sql)
	{
		$this->tableGateway->getAdapter()->query($sql, Adapter::QUERY_MODE_EXECUTE);
	}
}