<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-text-template.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Template;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Template\Template
 */
final class TemplateTest extends TestCase
{
    public function testRendersFromGivenTemplateFileToString(): void
    {
        $template = new Template(__DIR__ . '/_fixture/one.txt');

        $template->setVar(
            [
                'foo' => 'baz',
                'bar' => 'barbara',
            ]
        );

        $this->assertSame("baz barbara\n", $template->render());
    }

    public function testRendersFromFallbackTemplateFileToString(): void
    {
        $template = new Template(__DIR__ . '/_fixture/two.txt');

        $template->setVar(
            [
                'foo' => 'baz',
                'bar' => 'barbara',
            ]
        );

        $this->assertSame("baz barbara\n", $template->render());
    }

    public function testVariablesCanBeMerged(): void
    {
        $template = new Template(__DIR__ . '/_fixture/one.txt');

        $template->setVar(
            [
                'foo' => 'baz',
            ]
        );

        $template->setVar(
            [
                'bar' => 'barbara',
            ]
        );

        $this->assertSame("baz barbara\n", $template->render());
    }

    public function testCannotRenderTemplateThatDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Template('does_not_exist.html');
    }
}
