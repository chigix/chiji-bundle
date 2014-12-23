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

namespace Chigi\Bundle\ChijiBundle\Project;

use Chigi\Bundle\ChijiBundle\File\TwigResourceFile;
use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Chiji\Project\SourceRoad;
use Chigi\Chiji\Util\PathHelper;

/**
 * Description of TwigRoad
 *
 * @author 郷
 */
final class TwigRoad extends SourceRoad {

    public function getRegex() {
        return '.+\.twig$';
    }

    protected function resourcePathMatch($file_path) {
        $source_dir = str_replace('#', '\#', $this->getSourceDir());
        $file_path = PathHelper::pathStandardize($file_path);
        if (strpos($file_path, PathHelper::searchRealPath($this->getSourceDir(), 'chiji')) !== FALSE) {
            return FALSE;
        }
        return preg_match('#^' . $source_dir . '/' . $this->getRegex() . '#', $file_path) ? TRUE : FALSE;
    }

    /**
     * Get the resource object with specific internal resource class.
     * @param string $resource_path Support absolute path ONLY.
     * @return TwigResourceFile The resource object from factory
     * @throws ResourceNotFoundException
     */
    protected function resourceFactory($resource_path) {
        return new TwigResourceFile($resource_path);
    }

}
