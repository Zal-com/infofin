<?php

namespace App\Traits;

trait ReplicateModelWithRelations
{
    public function replicateModelWithRelations($model)
    {
        $model->load('scientific_domains', 'expenses', 'activities', 'countries', 'continents', 'documents');

        $newModel = $model->replicate();
        $newModel->save();

        $newModel->scientific_domains()->sync($model->scientific_domains->pluck('id')->toArray());
        $newModel->expenses()->sync($model->expenses->pluck('id')->toArray());
        $newModel->activities()->sync($model->activities->pluck('id')->toArray());
        $newModel->countries()->sync($model->countries->pluck('id')->toArray());
        $newModel->continents()->sync($model->continents->pluck('code')->toArray());

        foreach ($model->documents as $document) {
            $newDocument = $document->replicate();
            $newDocument->project_id = $newModel->id;
            $newDocument->save();
        }
        $newModel->save();

        return $newModel;
    }
}
