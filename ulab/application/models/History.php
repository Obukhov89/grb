<?php

class History extends Model
{
	public function addHistory($data)
	{
		$sql = $this->prepearTableData('HISTORY', $data);
		$this->DB->Insert('HISTORY', $sql);
	}
}

