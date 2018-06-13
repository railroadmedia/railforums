<?php

namespace Railroad\Railforums\Repositories\Traits;

use Carbon\Carbon;

trait SoftDelete
{
	/**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->continueOrNewQuery()
        			->where('id', $id)
        			->update([
        				'deleted_at' => Carbon::now()->toDateTimeString()
        			]);
    }
}