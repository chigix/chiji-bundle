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

use Chigi\Bundle\ChijiBundle\Exception\ResourceNotFoundException;
use Chigi\Bundle\ChijiBundle\File\AbstractResourceFile;
use Chigi\Bundle\ChijiBundle\File\Annotation;
use Chigi\Bundle\ChijiBundle\File\PlainResourceFile;

/**
 * Description of ResourcesManager
 *
 * @author 郷
 */
class ResourcesManager {

    private static $resources = array();

    /**
     * Add a resource object to this manager
     * @param AbstractResourceFile $resource
     * @return AbstractResourceFile 
     */
    public static function getResource(AbstractResourceFile $resource) {
        if (!isset(self::$resources[$resource->getId()])) {
            self::$resources[$resource->getId()] = $resource;
            if ($resource instanceof Annotation) {
                $resource->analyzeAnnotations();
            }
        }
        return self::$resources[$resource->getId()];
    }

    /**
     * 
     * @param type $path
     * @return AbstractResourceFile
     * @throws ResourceNotFoundException
     */
    public static function getResourceByPath($path) {
        $real_path = realpath($path);
        if ($real_path === FALSE) {
            throw new ResourceNotFoundException("The $path NOT FOUND");
        }
        return self::getResource(new PlainResourceFile($real_path));
    }

    /**
     * List all the resources registered
     * @return array<AbstractResourceFile> 
     */
    public static function getAll() {
        return array_values(self::$resources);
    }

}