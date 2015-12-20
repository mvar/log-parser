Server Log Parser
=================

[![Latest Stable Version](https://poser.pugx.org/mvar/log-parser/v/stable.png)](https://packagist.org/packages/mvar/log-parser)
[![Build Status](https://travis-ci.org/mvar/log-parser.png?branch=master)](https://travis-ci.org/mvar/log-parser)

This library allows you to easily iterate over your Apache, nginx or any other
web server log files.

Main features:

- Log file iterator
- Parser abstraction to help you implement your custom parser
- Low memory footprint even with huge files

Installation
------------

This library can be found on [Packagist](https://packagist.org/packages/mvar/log-parser).
The recommended way to install this is through [Composer](https://getcomposer.org):

```bash
composer require mvar/log-parser:dev-master
```

Usage
-----

Lets say you have log file `my.log` with following content:

```
GET /favicon.ico 200
GET /about 404
```

All you need to do to iterate over the file is to initialize `SimpleParser`
with your regular expression and pass it to `LogIterator`:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use MVar\LogParser\LogIterator;
use MVar\LogParser\SimpleParser;  

// Pass your regular expression
$parser = new SimpleParser('/(?<method>\S+)\s+(?<path>\S+)\s+(?<response_code>\d+)/');

foreach (new LogIterator('access.log', $parser) as $data) {
    var_export($data);
}
```

The above example will output:

```php
array (
  'method' => 'GET',
  'path' => '/favicon.ico',
  'response_code' => '200',
)
array (
  'method' => 'GET',
  'path' => '/about',
  'response_code' => '404',
)
```

It is also possible to parse compressed files by adding stream wrapper before file name:

```php
$logFile = 'compress.zlib://file:///path/to/log.gz';
```
    
How To
------

- How to implement custom parser?
    
Contributing
------------

TODO
