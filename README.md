# Laravel-geo-routes
[![Build Status](https://img.shields.io/travis/LaraCrafts/laravel-geo-routes.svg?style=flat-square)](https://travis-ci.org/LaraCrafts/laravel-geo-routes)
![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/LaraCrafts/laravel-geo-routes.svg?style=flat-square)
![](https://img.shields.io/github/tag/LaraCrafts/laravel-geo-routes.svg?style=flat-square)
![](https://img.shields.io/packagist/php-v/laracrafts/laravel-geo-routes.svg?style=flat-square)
![](https://img.shields.io/packagist/l/laracrafts/laravel-geo-routes.svg?style=flat-square)
> GeoLocation Restricted Routes For Laravel
## Requirements
- Laravel >= 5.5
- PHP >= 7.1

## Installation

Navigate to your project's root folder via terminal or command prompt and execute the following command:
```bash
composer require laracrafts/laravel-geo-routes
```
* Publish the configuration

While still in the same folder, execute this command in your terminal:
```php
php artisan vendor:publish --provider="LaraCrafts\GeoRoutes\GeoRoutesServiceProvider"
```


## Usage

To get started real quick, the `allowFrom` and `denyFrom` methods allow you to restrict access to routes depending on *GeoLocations*


- Allow access from specific regions

```php
Route::get('/home', 'FooController@bar')->allowFrom('us', 'gb');
```
What the above example does, is allowing access to the `/home` route **only** from the *United States* and the *United Kingdom*.

Alternatively we can do something like the following: 
```php
Route::get('/home', 'FooController@bar')->from('us', 'gb')->allow();
```

**By default,** all other countries will receive an **HTTP 401 Unauthorized Error**, to change this behavior you can use a callback as described in the <a href="">callbacks</a> section.


- Deny access from specific regions

So in the second example we are going to deny access **only** from specific locations, for instance: Canada, Germany and France

```php
Route::get('/home', 'FooController@bar')->denyFrom('ca', 'de', 'fr');
```
Alternatively:
```php
Route::get('/home', 'FooController@bar')->from('ca', 'de', 'fr')->deny();
```

> ***Note:*** This package uses *<a href="https://www.nationsonline.org/oneworld/country_code_list.htm">ISO Alpha-2</a>* country codes.


## Callbacks

As mentioned earlier, the default behavior for unauthorized users is an `HTTP 401 Unauthorized Error` response, but you are still able to change this behavior by using ***callbacks***.

To use a callback you have to simply add `->orCallback()` to the end of the GeoRoute constraint, like so:
```php
Route::get('/forums', 'FooController@bar')
->allowFrom('de', 'ca')
->orCallback();
```

> ***Note:*** You can also mixin with native router methods

- ### Default Callbacks

*Laravel-geo-routes* have some useful built-in callbacks, we are going to list them below along with their use cases.

- `orNotFound`

The `orNotFound` callback will result in an HTTP 404 Not Found response for unauthorized visitors.
```php
Route::get('/forums', 'FooController@bar')
->allowFrom('de', 'ca')
->orNotFound();
```
- `orRedirectTo`

This callback accepts one ***required*** argument which has to be a valid route name. 
Thanks to this callback, you'll be able to redirect unauthorized visitors to a route of your choice.

- ### Custom callbacks
The callbacks above might not be enough for your own use case, so you might want to add custom callbacks, the following guide will describe the steps to create your own custom callbacks.

1. Create a new class, for instance `CustomCallbacks`
2. Add as many callbacks as you want to add, but be sure that all of your methods are **`static`** or you'll be facing problems
3. Open the `config/geo-routes.php` configuration file, and add your callbacks to the callbacks array, like so:
```php
'callbacks' => [
    'myCallback' => 'CustomCallbacks::myCallback',
    'anotherCallback' => 'CustomCallbacks::anotherCallback'
]
```
Now your callbacks are ready, and you can start using them like so:
```php
Route::get('/forums', 'FooController@bar')
->allowFrom('ca', 'us')
->orMyCallback();

Route::get('/blog', 'FooController@baz')
->denyFrom('fr', 'es', 'ar')
->orAnotherCallback();
```
> ***Notice*** that we have added the **`or`** prefix and converted the callback name to studly case (e.g. `myCallback` was converted to `orMyCallback`), be sure not to forget this note as it is very important for your callback to work.

## Contribution
All contributions are welcomed for this project, please refer to the CONTRIBUTING.md file for more information about contribution guidelines.

## License
**Copyright (c) 2018 LaraCrafts.**

This product is licensed under the MIT license, please refer to the <a href="https://github.com/LaraCrafts/laravel-geo-routes/blob/master/LICENSE">License file</a> for more information.
