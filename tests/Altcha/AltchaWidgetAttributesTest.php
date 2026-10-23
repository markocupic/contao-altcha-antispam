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

namespace Markocupic\ContaoAltchaAntispam\Tests\Altcha;

use Markocupic\ContaoAltchaAntispam\Altcha\AltchaWidgetAttributes;
use PHPUnit\Framework\TestCase;

class AltchaWidgetAttributesTest extends TestCase
{
    public function testRendersAttributes(): void
    {
        $attributes = (new AltchaWidgetAttributes())
            ->add('name', 'altcha')
            ->add('maxnumber', 1000000)
            ->add('hidelogo', true)
            ->add('hidefooter', false)
        ;

        $this->assertSame(' name="altcha" maxnumber="1000000" hidelogo', (string) $attributes);
        $this->assertSame('name="altcha" maxnumber="1000000" hidelogo', $attributes->toString(false));
    }

    public function testEncodesSpecialCharacters(): void
    {
        $attributes = (new AltchaWidgetAttributes())->add('data-foo', '"><script>alert(1)</script>&amp;');

        $this->assertSame(' data-foo="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;&amp;amp;"', (string) $attributes);
    }

    public function testJsonValueIsDecodedToTheOriginalString(): void
    {
        $json = json_encode(['footer' => 'Geschützt durch <a href="https://altcha.org/">ALTCHA</a>', 'nested' => ['a' => 'b']]);

        $html = (new AltchaWidgetAttributes())->add('strings', $json)->toString(false);

        // Closing curly braces are encoded to prevent them from being parsed as insert tags
        $this->assertStringNotContainsString('}', $html);

        $this->assertSame(1, preg_match('/^strings="([^"]*)"$/', $html, $matches));
        $this->assertSame($json, html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public function testUnsetAndImmutability(): void
    {
        $attributes = (new AltchaWidgetAttributes())->add('foo', 'bar');
        $changed = $attributes->unset('foo')->add('baz', 'qux');

        $this->assertSame(' foo="bar"', (string) $attributes);
        $this->assertSame(' baz="qux"', (string) $changed);
    }
}
