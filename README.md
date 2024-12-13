# Laravel Radioactivity

## Introduction

Give your eloquent models energy so the most active items are easy to find, with extra energy given to _recently_ active items.

## Prerequisites

Before you install the package make sure you have queues working and running since Laravel Radioactivity uses it to control the tendencies. Refer to Laravel [official documentation](https://laravel.com/docs/master/queues) in order to configure queues in your project.

## Installation

You may install Laravel Radioactivity via Composer:

```shell
composer require eighteen73/laravel-radioactivity
```

Next, publish the Laravel Radioactivity configuration and migration files using the `vendor:publish` command. The configuration file will be placed in your config directory:

```shell
php artisan vendor:publish --provider="Eighteen73\Radioactivity\RadioactivityServiceProvider"
```

And finally, you should run your database migrations:

```shell
php artisan migrate
```

## How it works

Laravel Radioactivity allows you to create a trending system for any model you want. E.g. assuming a `half_life` set to `24`, and receiving 1000 points of energy initially, after 24 hours this energy would be now 500, and continues to decrement exponentially according to the decay equation.

But how can a trend be detected? Imagine that thousands of people hit the same item at the same time, this item will have thousands of energy points and if you have an ordered list of items this one will surely be on top, but after some time if this item doesn't receive any more energy points it will start to lose its energy and decay over time.

To help avoid the energies table growing too large to sort, this package also prunes models with very low energy (according to your `min_energy` setting).

## Configuration

To configure your decaying time you can set the `half_life` and `min_energy` parameters in `config/radioactivity.php`. The half life is measured in hours.

If you want to auto-decay the energy on your models then you also need to add your model to the `models` section.

```php
'models' => [
    \App\Models\MyModel::class,
],
```

## Preparing your model

To allow your model to work with Laravel Radioactivity you'll need to implement the HasEnergy trait. And in order to return the current model's energy value, add `energy_amount` to your serialization.

```php
use Eighteen73\Radioactivity\Traits\HasEnergy;

class MyModel extends Model
{
    use HasEnergy;

    protected $appends = ['energy_amount'];
}
```
## Usage

To add energy to your model use the following method. By default, 1000 energy is added each time to avoid lots of floating points, but any desired amount can be added by specifying the `$amount` parameter.

```php
$model->addEnergy();    // Adds 1000 energy
$model->addEnergy(100); // Adds 100 energy
```

To get the current value:

```php
$model->energy->amount;
```

## Schedule

This package uses Laravel Schedule to queue decay jobs for each Model type you configure. Make sure you are running [Laravel's Schedule via cron](https://laravel.com/docs/11.x/scheduling#running-the-scheduler) every minute.

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Examples

```php
# Get Ordered Models by energy (DESC by default)
$orderedModels = MyModel::orderByEnergy('desc')->get();
```

The above code creates a ordered list of items based on radioactivity.

## License

Laravel Radioactivity is open-sourced software licensed under the [MIT license](LICENSE.md).

## Credits
This plugin is forked from the [Laravel Trends](https://github.com/hacklabsdev/laravel-trends) package. All due credit to the authors of that package.

