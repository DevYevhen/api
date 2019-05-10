<?php

namespace App\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OAuth2Factory implements SecurityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.qa_oauth2. ' . $id;
        $container
            ->setDefinition($providerId, new ChildDefinition('app.security.authentication.provider.oauth2_provider'))
            ->replaceArgument('$userProvider', new Reference($userProvider));

        $listenerId = 'security.authentication.listener.qa_oauth2. ' . $id;
        $container
            ->setDefinition($listenerId, new ChildDefinition('trikoder.oauth2.security.firewall.oauth2_listener'));

        return [$providerId, $listenerId, 'trikoder.oauth2.security.entry_point.oauth2_entry_point'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'qa_oauth2';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        return;
    }
}
