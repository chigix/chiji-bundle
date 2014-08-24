<?php

/*
 * Copyright 2014 郷.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Chigi\Bundle\ChijiBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Some Statics for Symfony Project Specially.
 *
 * @author 郷
 */
class StaticsManager {

    /**
     *
     * @var ContainerInterface
     */
    private static $container = null;

    /**
     *
     * @var BundleInterface
     */
    private static $bundle = null;

    /**
     * Set the current symfony container as static.
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container) {
        self::$container = $container;
    }

    /**
     * Get the current static symfony container.
     * @return ContainerInterface
     */
    public static function getContainer() {
        return self::$container;
    }

    /**
     * Get the current static symfony kernel 
     * @return Kernel
     */
    public static function getKernel() {
        return self::$container->get('kernel');
    }

    /**
     * Set the current static symfony bundle
     * @param BundleInterface $bundle
     */
    public static function setBundle(BundleInterface $bundle) {
        self::$bundle = $bundle;
    }

    /**
     * Get the current static symfony bundle.
     * @return BundleInterface
     */
    public static function getBundle() {
        return self::$bundle;
    }

}
