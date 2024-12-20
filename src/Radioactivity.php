<?php

namespace Eighteen73\Radioactivity;

class Radioactivity
{
    public function add($model, $amount)
    {
        return $model->addEnergy($amount);
    }

    public function getEnergy($model)
    {
        $energy = $model->energy;

        return $energy !== null ? (float) $energy->amount : null;
    }
}
