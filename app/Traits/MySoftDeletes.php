<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


trait MySoftDeletes
{
	use SoftDeletes;

	protected function runSoftDelete()
	{
		$query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];

        $this->{$this->getDeletedAtColumn()} = $time;

        if ($this->timestamps && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }
		
		$columns['t_is_active'] = 0;
		$columns['t_is_deleted'] = 1;
		$columns['i_deleted_id'] = session()->get('user_id');
		
		
		$query->update($columns);
	}
}