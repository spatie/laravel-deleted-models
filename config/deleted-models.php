<?php

return [
    /*
     * The model uses to store deleted models.
     */
    'model' => Spatie\DeletedModels\Models\DeletedModel::class,

    /*
     * After this amount of days, the records in `deleted_models` will be deleted
     *
     * This functionality uses Laravel's native pruning feature.
     */
    'prune_after_days' => 365,
];
