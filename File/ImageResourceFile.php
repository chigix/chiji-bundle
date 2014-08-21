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

namespace Chigi\Bundle\ChijiBundle\File;

/**
 * Description of ImageResourceFile
 *
 * @author 郷
 */
class ImageResourceFile extends AbstractResourceFile implements RequiresMapInterface {

    private $requires;

    public function __construct($file_path) {
        parent::__construct($file_path);
        $this->requires = new \Chigi\Bundle\ChijiBundle\Collection\ResourcesCollection();
    }

    public function getRequires() {
        return $this->requires;
    }

}