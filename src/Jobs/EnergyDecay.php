<?php

namespace Eighteen73\Radioactivity\Jobs;

use Eighteen73\Radioactivity\Models\Energy;
use Eighteen73\Radioactivity\Traits\HasEnergy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class EnergyDecay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const RUN_EVERY = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue(config('radioactivity.queue'));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (config('radioactivity.models', []) as $class) {
            if (class_uses($class, HasEnergy::class)) {
                $model = new $class;
                $this->updateEnergies($model);
                $this->pruneEnergies($model);
            }
        }
    }

    protected function calculateDecay(Model $model): float
    {
        $halfLife = $model::getHalfLife();
        $hours = self::RUN_EVERY / 60;

        $meanLifetime = log(2) / $halfLife;
        $decay = exp($meanLifetime * $hours);

        return $decay;
    }

    protected function updateEnergies(Model $model): void
    {
        /**
         * @var MorphOne $relationship
         */
        $relationship = $model->energy();

        $decay = $this->calculateDecay($model);

        // Update energies
        Energy::where($relationship->getMorphType(), '=', $relationship->getMorphClass())
            ->update([
                'amount' => DB::raw('amount / '.$decay),
            ]);
    }

    protected function pruneEnergies(Model $model): void
    {
        /**
         * @var MorphOne $relationship
         */
        $relationship = $model->energy();

        // Delete old values below min energy
        Energy::where($relationship->getMorphType(), '=', $relationship->getMorphClass())
            ->where('amount', '<', config('radioactivity.min_energy'))
            ->delete();
    }
}
