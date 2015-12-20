How to implement custom parser?
===

In order to implement your custom log parser you need to write a class which
implements `LineParserInterface`. If your parser is based on regular expressions
you can use `AbstractLineParser` which already implements later interface.

In example below we will assume that we have a log with configurable format.
Variables `%m`, `%p` and `%c` can be used to define format.

```php
<?php

use MVar\LogParser\AbstractLineParser;

class FormattedLogParser extends AbstractLineParser
{
    /**
     * @var string
     */
    private $format;

    /**
     * @param string $format
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPattern()
    {
        $replacements = [
            '%m' => '(?<method>\S+)', // for simplicity, we match everything until first space
            '%p' => '(?<path>\S+)',
            '%c' => '(?<code>\S+)',
        ];
    
        $pattern = str_replace(array_keys($replacements), array_values($replacements), $this->format);
        
        return "/$pattern/";
    }
}
```

That's it. You are ready to use your parser. Lets say we have log which is
printed in `%c - %m %p` format:

```
200 - GET /favicon.ico
404 - GET /about
```

Initialize parser with specific format and parse single line:

```php
$parser = new FormattedLogParser('%c - %m %p');

var_export($parser->parseLine('200 - GET /favicon.ico'));
```

This example will output:

```php
array (
  'code' => '200',
  'method' => 'GET',
  'path' => '/favicon.ico',
)
```

Links
---

- [mvar/apache2-log-parser][1] - Apache access/error log parser based on this library

[1]: https://github.com/mvar/apache2-log-parser
