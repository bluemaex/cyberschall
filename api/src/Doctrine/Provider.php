<?php

declare(strict_types=1);

/*
 * This file is part of Cyberschall.
 *
 * (c) Max Stockner <mail@bluemaex.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bluemaex\Cyberschall\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['orm.cache'] = function (Container $pimple) {
            return extension_loaded('apcu') && !$pimple['debug']
                ? new ApcuCache()
                : new ArrayCache();
        };

        $pimple['orm.annotation_reader'] = function (Container $pimple) {
            // Register default doctrine annotations
            (new Configuration())->newDefaultAnnotationDriver([], false);

            return new CachedReader(new AnnotationReader(), $pimple['orm.cache'], $pimple['debug']);
        };

        $pimple['orm.ems'] = function (Container $pimple) {
            $ems = new Container();
            foreach ($pimple['orm.ems.options'] as $name => $options) {
                $ems[$name] = function () use ($pimple, $name, $options) {
                    $ormConfiguration = new Configuration();
                    $ormConfiguration->setQueryCacheImpl($pimple['orm.cache']);
                    $ormConfiguration->setResultCacheImpl($pimple['orm.cache']);
                    $ormConfiguration->setProxyDir($options['proxy_dir']);
                    $ormConfiguration->setProxyNamespace($options['proxy_namespace']);
                    $ormConfiguration->setAutoGenerateProxyClasses($options['debug']);
                    $ormConfiguration->setMetadataCacheImpl($pimple['orm.cache']);
                    $ormConfiguration->setMetadataDriverImpl(new AnnotationDriver(
                        $pimple['orm.annotation_reader'],
                        $options['doctrine.paths']
                    ));

                    return EntityManager::create(
                        $pimple['dbs'][$name],
                        $ormConfiguration,
                        $pimple['dbs.event_manager'][$name]
                    );
                };
            }

            return $ems;
        };

        $pimple['orm.em'] = function ($app) {
            $orms = $app['orm.ems'];

            return $orms[$app['orm.ems.default']];
        };
    }
}
