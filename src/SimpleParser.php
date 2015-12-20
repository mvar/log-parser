<?php

/*
 * (c) Mantas Varatiejus <var.mantas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MVar\LogParser;

/**
 * Simple parser implementation which use regular expression to parse line.
 */
class SimpleParser extends AbstractLineParser
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * Constructor.
     *
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareParsedData(array $matches)
    {
        // Remove indexed values
        $filtered = array_filter(array_keys($matches), 'is_string');
        $result = array_intersect_key($matches, array_flip($filtered));
        $result = array_filter($result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPattern()
    {
        return $this->pattern;
    }
}
