<?php

return [
    /*
     * The model uses to store deleted models.
     */
    'model' => Spatie\DeletedModels\Models\DeletedModel::class,

    /**
     * The table name used to store deleted data
     */
    'table_name' => 'deleted_models',

    /*
     * After this amount of days, the records in `deleted_models` will be deleted
     *
     * This functionality uses Laravel's native pruning feature.
     */
    'prune_after_days' => 365,
];
