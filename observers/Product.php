<?php namespace Octommerce\Octommerce\Observers;

class Product
{
    public function creating($model) 
    {
        $model->type->beforeCreateProduct();
    }

    public function updating($model)
    {
        $model->type->beforeUpdateProduct();
    }

    public function updated($model)
    {
        $model->type->afterUpdateProduct();
    }

    public function created($model)
    {
        $model->type->afterCreateProduct();
    }

    public function saving($model)
    {
        $model->type->beforeSaveProduct();
    }

    public function saved($model)
    {
        $model->type->afterSaveProduct();
    }

    public function deleting($model)
    {
        $model->type->beforeDeleteProduct();
    }

    public function deleted($model)
    {
        $model->type->afterDeleteProduct();
    }
}
