<?php

namespace Eighteen73\Radioactivity\Traits;

use Eighteen73\Radioactivity\Jobs\EnergyDecay;
use Eighteen73\Radioactivity\Models\Energy;

trait HasEnergy
{
    public function addEnergy($amount = 1)
    {
        if (! $this->energy) {
            $this->createEnergy();
        }
        $entity = $this->fresh();
        if (! in_array(request()->ip(), config('radioactivity.ip_blacklist'))) {
            $this->energy()->update([
                'amount' => $entity->energy->amount += $amount,
            ]);
            EnergyDecay::dispatch($entity, 0.25)->delay(now()->addHours(config('radioactivity.energy_decay')));
            EnergyDecay::dispatch($entity, 0.45)->delay(now()->addHours(config('radioactivity.energy_decay') * 2));
            EnergyDecay::dispatch($entity, 0.30)->delay(now()->addHours(config('radioactivity.energy_decay') * 3));
        }
    }

    public function energy()
    {
        return $this->morphOne(Energy::class, 'energisable');
    }

    public function getEnergyAmountAttribute()
    {
        return ($this->energy) ? (float) $this->energy->amount : 0;
    }

    public function decayEnergy($amount)
    {
        $this->energy()->update([
            'amount' => $this->energy->amount -= $amount,
        ]);
    }

    public function getEntityName()
    {
        return str_slug(get_class($this).' '.$this->id);
    }

    public function createEnergy()
    {
        return $this->energy()->create([
            'amount' => 0,
        ]);
    }
}
