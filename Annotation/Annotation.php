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

use Chigi\Bundle\ChijiBundle\File\AbstractResourceFile;
use Chigi\Bundle\ChijiBundle\File\Annotation as AnnotationResource;

/**
 * The common annotation support for all resource file.
 *
 * @author 郷
 */
class Annotation {

    private $contents;
    private $scope;
    private $occurs_pos;

    /**
     * 
     * @param string $contents The original string content of the annotation without markup symbols.
     * @param Annotation $scope The Resource File with this annotation.
     * @param int $occurs_pos The position of this annotation occured.
     */
    function __construct($contents, AnnotationResource $scope, $occurs_pos) {
        $this->contents = trim($contents);
        $this->scope = $scope;
        $this->occurs_pos = $occurs_pos;
    }

    /**
     * 
     * @param string $contents
     * @return AbstractAnnotation
     */
    public function parse() {
        /* @var $scope_resource AbstractResourceFile */
        $scope_resource = $this->getScope();
        $contents = str_replace("\r", "\n", $this->contents);
        $annotation_lines = explode("\n", $contents);
        foreach ($annotation_lines as $annotation_line) {
            $annotation_line = trim($annotation_line);
            $matches = array();
            if ('@' !== substr($annotation_line, 0, 1)) {
                continue;
            }
            var_dump($annotation_line);
            preg_match('#^@(\S+)\s(.+)$#', $annotation_line, $matches);
            $command_name = strtolower($matches[1]);
            $params = $matches[2];
            switch ($command_name) {
                case 'require':
                    $require_annotation = new RequireAnnotation($params, $this);
                    if ($scope_resource instanceof \Chigi\Bundle\ChijiBundle\File\RequiresMapInterface) {
                        $scope_resource->getRequires()->addResource($require_annotation->getResource());
                    }
                    break;
                case 'use':
                    var_dump("USE");
                    //return new UseAnnotation($params, $this);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @return int The occurence position of this annotation
     */
    public function getOccursPos() {
        return $this->occurs_pos;
    }

    /**
     * 
     * @return AnnotationResource The Resource file of scope
     */
    public function getScope() {
        return $this->scope;
    }

}
