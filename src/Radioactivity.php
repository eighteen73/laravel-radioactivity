<?php

namespace Eighteen73\Radioactivity;

class Radioactivity
{
    public function add($model, int|float $amount = 1000)
    {
        return $model->addEnergy($amount);
    }

    public function getEnergy($model)
    {
        $energy = $model->energy;

        return $energy !== null ? (float) $energy->amount : null;
    }
}
