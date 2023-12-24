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

use function array_keys;
use function array_merge;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function is_string;
use function sprintf;
use function str_replace;

final class Template
{
    /**
     * @psalm-var non-empty-string
     */
    private readonly string $template;

    /**
     * @psalm-var non-empty-string
     */
    private readonly string $openDelimiter;

    /**
     * @psalm-var non-empty-string
     */
    private readonly string $closeDelimiter;

    /**
     * @psalm-var array<string,string>
     */
    private array $values = [];

    /**
     * @psalm-param non-empty-string $templateFile
     * @psalm-param non-empty-string $openDelimiter
     * @psalm-param non-empty-string $closeDelimiter
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $templateFile, string $openDelimiter = '{', string $closeDelimiter = '}')
    {
        $this->template       = $this->loadTemplateFile($templateFile);
        $this->openDelimiter  = $openDelimiter;
        $this->closeDelimiter = $closeDelimiter;
    }

    /**
     * @psalm-param array<string,string> $values
     */
    public function setVar(array $values, bool $merge = true): void
    {
        if (!$merge || empty($this->values)) {
            $this->values = $values;

            return;
        }

        $this->values = array_merge($this->values, $values);
    }

    public function render(): string
    {
        $keys = [];

        foreach (array_keys($this->values) as $key) {
            $keys[] = $this->openDelimiter . $key . $this->closeDelimiter;
        }

        return str_replace($keys, $this->values, $this->template);
    }

    /**
     * @codeCoverageIgnore
     */
    public function renderTo(string $target): void
    {
        if (!@file_put_contents($target, $this->render())) {
            throw new RuntimeException(
                sprintf(
                    'Writing rendered result to "%s" failed',
                    $target,
                ),
            );
        }
    }

    /**
     * @psalm-param non-empty-string $file
     *
     * @psalm-return non-empty-string
     *
     * @throws InvalidArgumentException
     */
    private function loadTemplateFile(string $file): string
    {
        if (is_file($file)) {
            $template = file_get_contents($file);

            if (is_string($template) && !empty($template)) {
                return $template;
            }
        }

        $distFile = $file . '.dist';

        if (is_file($distFile)) {
            $template = file_get_contents($distFile);

            if (is_string($template) && !empty($template)) {
                return $template;
            }
        }

        throw new InvalidArgumentException(
            sprintf(
                'Failed to load template "%s"',
                $file,
            ),
        );
    }
}
