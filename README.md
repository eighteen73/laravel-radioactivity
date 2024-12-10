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

Laravel Radioactivity allows you to create a trending system for any model you want. E.g. it receives 1 point of energy per hit, but after 30 minutes this single point of energy decays 0.25 of it's value. After more 30 minutes it decays 0.45 points of it's value. Finally, after another 30 minutes it decays 0.30 of its value returning to 0.

But how can a trend be detected? Imagine that thousands of people hit the same item at the same time, this item will have thousands of energy points and if you have an ordered list of items this one will surely be on top, but after a few minutes if this item doesn't receive any more energy points it will start to loose it's energy and decay over time.

## Configuration

To configure your decaying time you can set the `energy_decay` parameter in `config/radioactivity.php`. The decaying time is measured in hours.

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

To add energy to your model use the following method:

```php
$model->addEnergy(1);
```

To get the current value:

```php
$model->energy->amount;
```

## Examples

```php
$models = MyModel::all();

$orderedModels = $models->sortByDesc('energy_amount');
```

The above code creates a ordered list of items based on radioactivity.

## License

Laravel Radioactivity is open-sourced software licensed under the [MIT license](LICENSE.md).

## Credits
This plugin is forked from the [Laravel Trends](https://github.com/hacklabsdev/laravel-trends) package. All due credit to the authors of that package.

