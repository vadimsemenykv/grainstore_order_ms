<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:18 PM
 */

namespace App\Infrastructure\Messaging\Bridge;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BusBridge implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->add($container, 'payment_system_command_bus');
        $this->add($container, 'payment_system_event_bus');
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
