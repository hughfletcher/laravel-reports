Laravel Reports
===========

A [Laravel](https://laravel.com/) reporting package based off the awesome  *[jdorn/php-reports](https://github.com/jdorn/php-reports)* framework.

The intention is to mostly replicate his package using the Laravel framework and all it's goodyness to be used as either as a complete application or supplement your existing app.

At work we use *jdorn/php-reports* and am creating this for an existing internal Laravel app. Naturally, I am initially focusing on the features we already use, but intend to duplicate most of the app so it can maximize everything that Laravel offers. And maybe more...

At the moment you can install Laravel and this [package](https://github.com/hughfletcher/laravel-reports#getting-started). So far it only has Variable and some Filter headers.

What's Different?
============

- Report and header syntax is very strict and not near as forgiving as Jdorn's app. Header configuration is commented Json, that must begin on the first line, and must have two lines following before your query/code.
- No on the fly environments. Your data sources are whatever is in your database config.
- No multiple datasets per report currently.

Getting Started
============

Ok, so your still interested? To get going we're basically [installing Laravel](https://laravel.com/docs/5.6/installation), installing this package, and then publishing it.

```bash
$ composer create-project --prefer-dist laravel/laravel reports
$ cd reports
$ composer require hfletcher\laravel-reports
$ php artisan vendor:publish --provider="Reports\ServiceProvider"
```

You should be able to go to `http://your.domain/reports` and see something.

Just really haven't got to documentation yet, check the examples folder for some guidance for now.

Development
============

I would love any help but I do want to go a certain direction with this project. If you have an idea and might want to contribute create a new issue and let's talk about it. I'd hate for someone to spend time on something only for me to knock it down.

To get developing using docker, clone the repo and run `docker-compose up -d` in the project root. After the containers are built, run `docker-compose exec dev composer require hfletcher/laravel-reports dev-master`. You must require whatever branch you have checked out locally(dev-master, dev-develop, etc.).

If you want a test database to play with, run `docker-compose exec dev composer update` and then `docker-compose exec dev php artisan employees:install`. This installs a sample mysql employee database using the  [nojes/laravel-employees](https://github.com/nojes/laravel-employees) package, which all the MySql examples work off of.
