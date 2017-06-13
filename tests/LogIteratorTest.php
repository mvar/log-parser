<?php

/*
 * (c) Mantas Varatiejus <var.mantas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MVar\LogParser\Tests;

use MVar\LogParser\LineParserInterface;
use MVar\LogParser\LogIterator;
use MVar\LogParser\Exception\MatchException;
use PHPUnit\Framework\TestCase;

class LogIteratorTest extends TestCase
{
    /**
     * Creates and returns instance of parser mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LineParserInterface
     */
    protected function getParser()
    {
        return $this->createMock(LineParserInterface::class);
    }

    /**
     * Test for log iterator.
     *
     * @param string $logfile
     * @param int    $rowsCount
     *
     * @dataProvider getTestIteratorData()
     */
    public function testIterator($logfile, $rowsCount)
    {
        $parser = $this->getParser();
        $expectedData = 'parsed_line';

        // Test if parser was called as many times as expected
        $parser->expects($this->exactly($rowsCount))
            ->method('parseLine')
            ->willReturn($expectedData);

        $iterator = new LogIterator($logfile, $parser);

        foreach ($iterator as $line => $data) {
            $this->assertTrue(is_string($line));
            $this->assertEquals($expectedData, $data);
        }
    }

    /**
     * Test for iterator in case of empty lines in log.
     */
    public function testIteratorWithEmptyLines()
    {
        $parser = $this->getParser();

        $parser->expects($this->exactly(3))
            ->method('parseLine')
            ->will(
                $this->onConsecutiveCalls(
                    $this->returnValue('parsed_line'),
                    $this->throwException(new MatchException()),
                    $this->returnValue('parsed_line')
                )
            );

        $iterator = new LogIterator(__DIR__ . '/Fixtures/access.log', $parser, false);

        $result = [];
        foreach ($iterator as $data) {
            $result[] = $data;
        }

        // Test if empty line was not parsed (NULL)
        $expectedResult = ['parsed_line', null, 'parsed_line'];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for iterator in case of empty lines in log.
     */
    public function testIteratorWithWhitespaceLines()
    {
        $parser = $this->getParser();

        $parser->expects($this->any())
            ->method('parseLine')
            ->willReturnArgument(0);

        $iterator = new LogIterator(__DIR__ . '/Fixtures/custom.log', $parser);

        $result = [];
        foreach ($iterator as $data) {
            $result[] = $data;
        }

        // Test if leading whitespaces were not trimmed
        $expectedResult = [
            '2016-03-01T22:22:37.800861Z 0 [Note] InnoDB: Progress in MB:',
            ' 100 200',
        ];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for iterator in case of file handler exception.
     *
     * @expectedException \MVar\LogParser\Exception\ParserException
     * @expectedExceptionMessage Can not open log file
     */
    public function testIteratorFileException()
    {
        $iterator = new LogIterator(__DIR__ . '/Fixtures/non_existing_file.log', $this->getParser());
        $iterator->rewind();
    }

    /**
     * Data provider for testIterator().
     *
     * @return array[]
     */
    public function getTestIteratorData()
    {
        $data = [];

        // Simple log
        $data[] = [__DIR__ . '/Fixtures/access.log', 2];

        // Compressed log
        $data[] = ['compress.zlib://file://' . __DIR__ . '/Fixtures/access_compressed.gz', 4];

        return $data;
    }
}
