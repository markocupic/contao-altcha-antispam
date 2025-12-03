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

namespace Markocupic\ContaoAltchaAntispam\DependencyInjection;

use Markocupic\ContaoAltchaAntispam\Altcha\Algorithm;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_KEY = 'markocupic_contao_altcha_antispam';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_KEY);
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('hmac_key')
                    ->cannotBeEmpty()
                    ->defaultValue('')
                ->end()
                ->enumNode('algorithm')
                    ->values(array_map(static fn ($case) => $case->value, Algorithm::cases()))
                    ->defaultValue(Algorithm::ALGORITHM_SHA_256->value)
                ->end()
                ->integerNode('range_min')
                    ->defaultValue(10000)
                ->end()
                ->integerNode('range_max')
                    ->defaultValue(100000)
                ->end()
                ->integerNode('challenge_expiry')
                    ->defaultValue(3600)
                ->end()
            ->end()
        ;

        $this->addRangeSection($rootNode);

        return $treeBuilder;
    }

    private function addRangeSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->integerNode('range_min')
                    ->defaultValue(10000)
                ->end()
                ->integerNode('range_max')
                    ->defaultValue(100000)
                ->end()
            ->end()
            ->validate()
            ->ifTrue(static fn ($config) => isset($config['range_min'], $config['range_max']) && $config['range_max'] <= $config['range_min'])
            ->thenInvalid('The "range_max" value must be greater than "range_min" value.')
            ->end()
        ;
    }
}
