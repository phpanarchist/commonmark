<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Inline\Parser;

use League\CommonMark\Cursor;
use League\CommonMark\Inline\Element\Code;
use League\CommonMark\Inline\Parser\BacktickParser;
use League\CommonMark\InlineParserContext;

class BacktickParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $string
     * @param $expectedContents
     *
     * @dataProvider dataForTestParse
     */
    public function testParse($string, $expectedContents)
    {
        $cursor = new Cursor($string);

        // Move to just before the first backtick
        $firstBacktickPos = mb_strpos($string, '`', null, 'utf-8');
        $cursor->advanceBy($firstBacktickPos);

        $inlineContext = new InlineParserContext($cursor);
        $nodeStub = $this->getMock('League\CommonMark\Node\Node');
        $nodeStub
            ->expects($this->once())
            ->method('appendChild')
            ->with($this->callback(function (Code $code) use ($expectedContents) {
                return $code instanceof Code && $expectedContents === $code->getContent();
            }))
        ;
        $contextStub = $this->getMock('League\CommonMark\ContextInterface');
        $contextStub->expects($this->any())->method('getContainer')->willReturn($nodeStub);

        $parser = new BacktickParser();
        $parser->parse($contextStub, $inlineContext);
    }

    /**
     * @return array
     */
    public function dataForTestParse()
    {
        return [
            ['This is `just` a test', 'just'],
            ['Из: твоя `feature` ветка', 'feature'],
            ['Из: твоя `тест` ветка', 'тест'],
        ];
    }
}
