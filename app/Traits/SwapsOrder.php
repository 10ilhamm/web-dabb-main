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

    /**
     * Insert a new item at a specific order, shifting existing items down.
     * All items with order >= $insertOrder will be incremented by 1.
     *
     * @param string $modelClass      Fully qualified model class name
     * @param int    $insertOrder     The desired order for the new item
     * @param array  $scopeConditions Scope conditions, e.g. ['feature_id' => 5]
     * @param array  $extraAttributes Extra attributes for the new item
     * @return Model                   The newly created model instance
     */
    protected function insertAndShiftOrder(string $modelClass, int $insertOrder, array $scopeConditions, array $extraAttributes = []): Model
    {
        // Shift all existing items at or after the insert position up by 1
        $modelClass::where($scopeConditions)
            ->where('order', '>=', $insertOrder)
            ->increment('order');

        // Create the new item at the desired order
        return $modelClass::create(array_merge($scopeConditions, $extraAttributes, ['order' => $insertOrder]));
    }
}
