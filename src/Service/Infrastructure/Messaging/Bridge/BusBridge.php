<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:18 PM
 */

namespace Service\Infrastructure\Messaging\Bridge;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BusBridge implements CompilerPassInterface
{
    /** @array */
    protected $names;

    public function __construct(array $names)
    {
        $this->names = $names;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->names as $name) {
            $this->add($container, $name);
        }
    }

    private function add(ContainerBuilder $container, $name)
    {
        // always first check if the primary service is defined
        if (!$container->has($name)) {
            return;
        }
        $definition = $container->findDefinition($name);
        $taggedServices = $container->findTaggedServiceIds("{$name}.handler");
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $args = [new Reference($id), $attributes["message"]];
                if (isset($attributes["priority"])) {
                    $args[] = $attributes["priority"];
                }
                $definition->addMethodCall('attach', $args);
            }
        }
    }
}
