Time Traveller Bundle
=====================

It helps you in writing tests for project which has time dependencies. You can control what `now` means.

```php
// Original way:
$entity->setCreatedAt(new \DateTime());

// New way with the TimeManager:
$entity->setCreatedAt($timeManager->getNow());
```

> ### Limitations
> 
> - The `CURRENT_TIMESTAMP()` in Doctrine or the PDO `NOW()` functions will get back the "right" dates and times.
>     You have to avoid these "outside" functions.
> - Some (third party) listeners (eg: TimestampableListener) use the original `new DateTime()` method. You have to pay 
>     attention to this, and you have to override, or fix in the background with your own listener.
> 

## Install

### Install the bundle

Install bundle with composer:

```shell
$ composer require fchris82/time-traveller-bundle
```

Edit the `config/bundles.php` file:

```diff
<?php

return [
    // ...
    Fchris82\TimeTravellerBundle\TimeTravellerBundle::class => ['all' => true],
];
```

Create config file:

```shell
$ echo "time_traveller:" > config/packages/time_traveller.yaml
$ echo "  time_passing: false" >> config/packages/time_traveller.yaml
```

Change configuration if you need in the `config/packages/time_traveller.yaml` file.

> ### About 'time passing behaviour'
> 
> When you start shifting the `now` AND you have longer processes - because of high load eg - you would need the time
> passing in tests. If `time_passing` is `true`, it means, time will be passing. If you set `now` to: `2010-01-01T00:00:00`
> and you ask for `now` with `getNow()` 5 seconds later, you will get back: `2010-01-01T00:00:05`.
>
> If it is `false` AND you shifted the time, you will get back always the same time: `2010-01-01T00:00:00`, it does not
> matter how much time has passed.
> 
> This config value has no any effects:
> - if you do not have longer running processes
> - if you have not set/shifted the time with `setNow()` yet
> 
> `false` needs less resources.

### Replace date creating everywhere you need

You installed it, you configured it, now it's time to use it.

1. Add `Fchris82\TimeTravellerBundle\Manager\TimeManager` service to every service, command, controller where you use `now`
2. Replace `new DateTime()` to `$this->timeManager->getNow()`
3. Replace `new DateTime('+1 day')` or similar to `$this->timeManager->getNow()->modify('+1 day')'`
4. Refactor your PHPUnit tests. In your integration tests you can retrieve the `TimeManager` service, see: https://symfony.com/doc/current/testing.html#retrieving-services-in-the-test
5. Refactor your Behat tests

## Usage

### Getting 'now'

There are 2 getters:
- `getNow()` gets back a `\DateTime` object
- `getSqlNow()` gets back an SQL compatible date string

### "Travelling" in/shifting the time

- `setNow()` helps you setting an accurate appointment 
- `modify()` is the same to `\DateTime::modify()` method. It depends on what was set in the `setNow()`.
- `shiftForward()` and `shiftBackward()` need a `\DateInterval` object, and it will add or sub the datetime that was set with the `setNow()`

Eg:

```php
// Without calling `setNow()`
$real = $timeManager->getNow();

// Init
$timeManager->setNow(new DateTime('2010-01-01 00:00:00'));
$now = $timeManager->getNow(); // '2010-01-01 00:00:00'

// Change                                   !
$timeManager->setNow(new DateTime('2010-01-02 00:00:00'));
$now = $timeManager->getNow(); // '2010-01-02 00:00:00'
//                                          ^

// Modify: adding 3 days to '2010-01-02 00:00:00'
$timeManager->modify('+3 day');
$now = $timeManager->getNow(); // '2010-01-05 00:00:00'
//                                          ^

// Shift: Adding 2 hours to '2010-01-05 00:00:00'
$timeManager->shiftForward(new \DateInterval('PT2H'));
$now = $timeManager->getNow(); // '2010-01-05 02:00:00'
//                                             ^

// You can play with the timezone as well:
$timeManager->setNow(new \DateTime('now', new \DateTimeZone('Europa/Budapest')));
//                                        ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

```
