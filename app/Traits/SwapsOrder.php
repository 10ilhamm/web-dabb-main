<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait SwapsOrder
{
    /**
     * Swap order jika ada item lain dengan order yang sama dalam scope parent.
     *
     * @param Model $model           Item yang sedang diupdate
     * @param int   $newOrder        Order baru yang diinginkan
     * @param int   $oldOrder        Order lama
     * @param array $scopeConditions Kondisi scope parent, e.g. ['feature_id' => 5]
     */
    protected function swapOrder($model, int $newOrder, int $oldOrder, array $scopeConditions): void
    {
        if ($newOrder === $oldOrder) {
            return;
        }

        $conflicting = $model::where($scopeConditions)
            ->where('id', '!=', $model->id)
            ->where('order', $newOrder)
            ->first();

        if ($conflicting) {
            $conflicting->update(['order' => $oldOrder]);
        }
    }
}
