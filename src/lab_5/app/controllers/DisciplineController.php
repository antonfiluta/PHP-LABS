<?php
declare(strict_types=1);

class DisciplineController
{
    public function renderView(): void
    {
        $model = new DisciplineModel();
        $model->seedIfEmpty();

        $storageType = $model->getStorageType();
        require ROOT_DIR . '/app/views/discipline_view.php';
    }
}