<?php

namespace Eighteen73\Radioactivity\Traits;

use Eighteen73\Radioactivity\Models\Energy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Query\JoinClause;

trait HasEnergy
{
    public static function bootHasEnergy(): void
    {
        HasMany::macro('orderByEnergy', function (string $direction = 'desc') {
            return $this->getRelated()->joinEnergyAndSort($this->getQuery(), $this->getRelated(), $direction);
        });

        Builder::macro('orderByEnergy', function (string $direction = 'desc') {
            return $this->getModel()->joinEnergyAndSort($this->getQuery(), $this->getModel(), $direction);
        });
    }

    public static function getEnergyTable(): string
    {
        return (new Energy)->getTable();
    }

    public function joinEnergyAndSort(Builder $builder, Model $model, string $direction = 'desc'): Builder
    {
        if (! in_array(HasEnergy::class, class_uses($model), true)) {
            throw new \Exception(sprintf('Model %s does not have the HasEnergy trait.', $model));
        }

        $table = self::getEnergyTable();
        $builder->select($model->getTable().'.*');
        $builder->leftJoin($table, function (JoinClause $join) use ($model) {
            $relation = $model->energy();
            $join->on($relation->getQualifiedForeignKeyName(), '=', $model->getQualifiedKeyName())
                ->where($relation->getMorphType(), '=', $relation->getMorphClass());
        });
        $builder->orderBy("{$table}.amount", $direction);

        return $builder;
    }

    public function addEnergy(int|float $amount = 1000): void
    {
        if (! $this->energy) {
            $this->createEnergy();
        }
        $entity = $this->fresh();
        if (! in_array(request()->ip(), config('radioactivity.ip_blacklist'))) {
            $this->energy()->update([
                'amount' => $entity->energy->amount + $amount,
            ]);
        }
    }

    public function energy(): MorphOne
    {
        return $this->morphOne(Energy::class, 'energisable');
    }

    public function energyAmount(): Attribute
    {
        return Attribute::get(function (): float {
            return (float) $this->energy?->amount ?? 0;
        });
    }

    public function decayEnergy(int|float $amount): void
    {
        $this->energy()->update([
            'amount' => $this->energy->amount - $amount,
        ]);
    }

    public function getEntityName(): string
    {
        return str_slug(get_class($this).' '.$this->id);
    }

    public function createEnergy(): Energy
    {
        return $this->energy()->create([
            'amount' => 0,
        ]);
    }

    public static function getHalfLife(): int
    {
        return config('radioactivity.half_life', 24);
    }
}
