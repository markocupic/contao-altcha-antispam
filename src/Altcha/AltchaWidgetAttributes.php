<?php

declare(strict_types=1);

/*
 * This file is part of Contao Altcha Antispam.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-altcha-antispam
 */

namespace Markocupic\ContaoAltchaAntispam\Altcha;

use Contao\StringUtil;

class AltchaWidgetAttributes implements \Stringable
{
    public function __construct(
        private array $attributes = [],
    ) {
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function set(array $values): self
    {
        $clone = clone $this;

        foreach ($values as $key => $value) {
            if (empty($key) || !\is_string($key)) {
                continue;
            }

            $clone->add($key, $value);
        }

        return $clone;
    }

    public function add(string $name, bool|int|string $value): self
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    public function unset(string $name): self
    {
        $clone = clone $this;

        if (isset($clone->attributes[$name])) {
            unset($clone->attributes[$name]);
        }

        return $clone;
    }

    public function toString(bool $leadingSpace = true): string
    {
        $attributes = [];

        foreach ($this->attributes as $key => $value) {
            // Skip null values
            if (null === $value) {
                continue;
            }

            // Handle boolean attributes (e.g., disabled, required)
            if (\is_bool($value)) {
                if ($value) {
                    $attributes[] = $this->encode($key);
                }

                continue;
            }

            // Normal key="value" attributes
            $attributes[] = \sprintf(
                '%s="%s"',
                $this->encode($key),
                $this->encode((string) $value),
            );
        }

        $string = implode(' ', $attributes);

        return $leadingSpace && $string ? " $string" : $string;
    }

    /**
     * Always double encode, so the attribute value is decoded to exactly the given
     * string (the default of $blnDoubleEncode differs between Contao 5.3 and 6).
     */
    private function encode(string $value): string
    {
        return StringUtil::specialcharsAttribute($value, false, true);
    }
}
