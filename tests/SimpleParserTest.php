<?php

/*
 * (c) Mantas Varatiejus <var.mantas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MVar\LogParser\Tests;

use MVar\LogParser\SimpleParser;
use PHPUnit\Framework\TestCase;

class SimpleParserTest extends TestCase
{
    /**
     * Test for parseLine().
     *
     * @param string $pattern
     * @param string $line
     * @param array  $expectedResult
     *
     * @dataProvider getTestParseLineData()
     */
    public function testParseLine($pattern, $line, array $expectedResult)
    {
        $parser = new SimpleParser($pattern);
        $result = $parser->parseLine($line);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for testParseLine().
     *
     * @return array[]
     */
    public function getTestParseLineData()
    {
        return [
            [
                '/(?<method>\S+)\s+(?<path>\S+)\s+(?<response_code>\d+)/',
                'GET /favicon.ico 200',
                [
                    'method' => 'GET',
                    'path' => '/favicon.ico',
                    'response_code' => '200',
                ],
            ],
            [
                '/(?<method>\S+)\s+(?<path>\S+)\s+(?<response_code>\d+)/',
                'GET /about 404',
                [
                    'method' => 'GET',
                    'path' => '/about',
                    'response_code' => '404',
                ],
            ],
        ];
    }
}
