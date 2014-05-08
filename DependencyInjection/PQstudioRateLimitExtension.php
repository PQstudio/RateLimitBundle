<?php
namespace PQstudio\RateLimitBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PQstudioRateLimitExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['limits'])) {
            foreach ($config['limits'] as $limit) {
                $host = is_null($limit['host']) && $limit['domain'] ? $limit['domain'] : $limit['host'];
                $limit['ips'] = (empty($limit['ips'])) ? null : $limit['ips'];

                $matcher = $this->createRequestMatcher(
                    $container,
                    $limit['path'],
                    $host,
                    $limit['method'],
                    $limit['ips'],
                    $limit['attributes'],
                    $limit['controller']
                );

                unset(
                    $limit['path'],
                    $limit['method'],
                    $limit['ips'],
                    $limit['attributes'],
                    $limit['domain'],
                    $limit['host'],
                    $limit['controller']
                );

                $container->getDefinition('pq.rate_limit.request.listener')
                          ->addMethodCall('add', array($matcher, $limit));
            }
        }

        //$yamlMappingFiles = $container->getParameter('validator.mapping.loader.yaml_files_loader.mapping_files');
        //$yamlMappingFiles[] = __DIR__.'/../Resources/config/validation/user/User.yml';
        //$container->setParameter('validator.mapping.loader.yaml_files_loader.mapping_files', $yamlMappingFiles);
    }

    protected function createRequestMatcher(ContainerBuilder $container, $path = null, $host = null, $methods = null, $ips = null, array $attributes = array(), $controller = null)
    {
        if (null !== $controller) {
            $attributes['_controller'] = $controller;
        }

        $arguments = array($path, $host, $methods, $ips, $attributes);
        $serialized = serialize($arguments);
        $id = 'pq_rate_limit.request_matcher.'.md5($serialized).sha1($serialized);

        if (!$container->hasDefinition($id)) {
            // only add arguments that are necessary
            $container
                ->setDefinition($id, new DefinitionDecorator('pq.rate_limit.request_matcher'))
                ->setArguments($arguments)
            ;
        }

        return new Reference($id);
    }
}
