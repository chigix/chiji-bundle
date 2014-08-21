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

namespace Chigi\Bundle\ChijiBundle\Annotation;

use Chigi\Bundle\ChijiBundle\File\Annotation as AnnotationInterface;

/**
 * Description of AbstractAnnotation
 *
 * @author 郷
 */
abstract class AbstractAnnotation {

    /**
     *
     * @var Annotation
     */
    private $annotation;

    final function __construct($param_str, Annotation $annotation) {
        $this->annotation = $annotation;
        $this->parse($param_str);
    }

    /**
     * @param string $param_str The String as params following the command name
     */
    abstract protected function parse($param_str);

    /**
     * 
     * @return AnnotationInterface
     */
    protected function getScope() {
        return $this->annotation->getScope();
    }

    /**
     * 
     * @return int
     */
    protected function getOccursPos() {
        return $this->annotation->getOccursPos();
    }

}
