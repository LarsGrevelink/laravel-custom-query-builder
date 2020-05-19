# Laravel Custom Query Builder

[![Test Suite Status](https://github.com/larsgrevelink/laravel-custom-query-builder/workflows/Test%20Suite/badge.svg)](https://github.com/larsgrevelink/laravel-custom-query-builder)
[![Total Downloads](https://poser.pugx.org/lgrevelink/laravel-custom-query-builder/d/total.svg)](https://packagist.org/packages/lgrevelink/laravel-custom-query-builder)
[![Latest Stable Version](https://poser.pugx.org/lgrevelink/laravel-custom-query-builder/v/stable.svg)](https://packagist.org/packages/lgrevelink/laravel-custom-query-builder)
[![License](https://poser.pugx.org/lgrevelink/laravel-custom-query-builder/license.svg)](https://github.com/larsgrevelink/laravel-custom-query-builder)

A custom query builder which allows projects to use Eloquent's builder on an application level. Define joins, filters and sorting methods with proper IntelliSense through a thin abstraction layer. It's a different approach to Laravel's local scopes.

## Installation

```bash
composer require lgrevelink/laravel-custom-query-builder
```


## Configuration for Laravel

Laravel's auto-discovery directly registers the service provider so it should be instantly usable. If you don't use auto-discovery, please add the `ServiceProvider` to the provider array in `config/app.php`.

```php
LGrevelink\CustomQueryBuilder\ServiceProvider::class
```

The artisan command is directly registered by adding the service provider. If you want to change the default configuration you can publish it through the following command;

```bash
php artisan vendor:publish --provider="LGrevelink\CustomQueryBuilder\ServiceProvider"
```


## Configuration for Lumen

Using this package in lumen requires you to register the service provider in `bootstrap/app.php`.

```php
$app->register(LGrevelink\CustomQueryBuilder\ServiceProvider::class);
```

The artisan command is directly registered by adding the service provider. If you want to change the default configuration you can publish it through the following command;

```php
$app->configure('querybuilder');
```


## Usage

Models use Eloquent's builder as a default when running operations from your model. This package allows you to override this default behaviour with a custom query builder which can be hosted in your project.

### Create a new query builder (generator)

The package includes an artisan make command which configures an example builder at the given location. Simply run the following and you should have the builder;

```bash
php artisan make:query-builder MyQueryBuilder
```

### Assigning a query builder

After creating the query builder we need to assign it to the model. The model needs to have the `HasCustomQueryBuilder` concern applied to it. This can be done by extending the `LGrevelink\CustomQueryBuilder\Model` instead of the Eloquent's model or adding the trait directly to the class. This only initiates a connection to the `CustomQueryBuilder`. To use your own you have to set it in the model;

```php
class SomeModel extends Model
{
    use LGrevelink\CustomQueryBuilder\Concerns\HasCustomQueryBuilder;

    protected $queryBuilder = App\QueryBuilders\MyQueryBuilder::class;
}
```

### Using the query builder

The default naming structures for filters and sorting are `filterOn%s` and `sortBy%s` where the placeholder will be replaced by a **singular or plural** version of the filter depending on the filter value. In case it's an array it attempts the plural version. Any other value will make use of the singular version. Below are some examples where each of the sets acts the same.

```php
$builder = SomeModel::select();

// Using direct filters on the query builder
$builder->filterOnProperty(1234);
$builder->filterOnCategories([1, 2]);
$builder->sortByCategory('asc');
$builder->sortByTitle('asc');

// Enforcing the naming structure
$builder->applyFilter('property', 1234); // Calls filterOnProperty
$builder->applyFilter('category', [1, 2]); // Calls filterOnCategories
$builder->applySorting('category', 'asc'); // Calls sortByCategory
$builder->applySorting('title', 'asc'); // Calls sortByTitle

// Setting them in bulk
$builder->applyFilters([
    'property' => 1234,
    'category' => [1, 2],
); // Calls both filterOnProperty and filterOnCategories

$builder->applySorting([
    'category' => 'asc',
    'title' => 'asc',
]); // Calls both sortByCategory and sortByTitle
```

### Strict exceptions

In case a filter does not exist an exception will be thrown. There is a fallback for these cases which automatically applies a `where` or `whereIn` for unknown filters and applies an `orderBy` for unknown sortings. By default, the query builder's mode is set to `strict` and this behaviour is prevented. You can change this by overriding the `querybuilder.mode` config value and set it to `auto`. **Be aware that this could have side-effects if the input is not validated properly.**

## Utilities

### joinOnce

The `joinOnce` method can be used to join tables but prevent duplicates. It makes use of the `\Illuminate\Database\Query\Builder`'s `join` method does a basic table validation before adding the join to the query. This way multiple joins on the same table can be prevented.

```php
class ProductQueryBuilder extends CustomQueryBuilder
{
    public function joinCategories() {
        return $this->joinOnce('categories', 'categories.id', 'products.category_id');
    }

    public function filterOnCategoryStatus(string $status) {
        return $this->joinCategories()->where('categories.status', $status);
    }

    public function filterOnCategoryTitle(string $title) {
        return $this->joinCategories()->where('categories.title', 'LIKE', $title);
    }
}
```

In this case when filtering on status as well as title the join will only be forged once instead of multiple times.


### Wildcard column qualifying

Using unqualified wildcards in select statements can have side-effects which are hard to find. When using joins, values of the original table can be overwritten by the joined tables when the column naming is the same. To prevent this behaviour we qualify the unqualified wildcards. In case the overwriting is the behaviour you seek, we suggest to specifically add the columns you want to the select statement. Instead of making it a side effect, it should be a deliberate choice the developer is aware of.
