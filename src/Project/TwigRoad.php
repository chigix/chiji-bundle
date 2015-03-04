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
use Chigi\Component\IO\File;

/**
 * Description of TwigRoad
 *
 * @author 郷
 */
final class TwigRoad extends SourceRoad {

    public function getRegex() {
        return '.+\.twig$';
    }

    protected function resourcePathMatch(File $file) {
        $source_dirpath = str_replace('#', '\#', $this->getSourceDir()->getAbsolutePath());
        $chiji_dir = new File("chiji", $this->getSourceDir()->getAbsolutePath());
        if (strpos($file->getAbsolutePath(), $chiji_dir->getAbsolutePath()) !== FALSE) {
            return FALSE;
        }
        return preg_match('#^' . $source_dirpath . '/' . $this->getRegex() . '#', $file->getAbsolutePath()) ? TRUE : FALSE;
    }

    /**
     * Get the resource object with specific internal resource class.
     * @param File $file The resource as File Object.
     * @return TwigResourceFile The resource object from factory
     * @throws ResourceNotFoundException
     */
    protected function resourceFactory(File $file) {
        return new TwigResourceFile($file);
    }

}
