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

use Chigi\Chiji\Annotation\Annotation;
use Chigi\Chiji\File\HtmlResourceFile;

/**
 * Description of TwigResourceFile
 *
 * @author 郷
 */
class TwigResourceFile extends HtmlResourceFile {

    /**
     *
     * @var array<string>
     */
    private $all_twig_nodes;

    /**
     * TWIG BLOCK NODES LIKE: {%%}
     * @var array<string>
     */
    private $tag_nodes;

    /**
     * TWIG PRINT NODES LIKE: {{ $post }}
     * @var array<string>
     */
    private $print_nodes;

    /**
     * TWIG COMMENT NODES LIKE: {# BANKAI #}
     * @var array<string>
     */
    private $annotaion_nodes;

    /**
     *
     * @var array<int>
     */
    private $all_twig_nodes_pos;

    public function __construct($file_path) {
        parent::__construct($file_path);
        $this->parseTwigNodes();
        $this->calculateNodesPos();
        $this->parseAnnotation();
    }

    private function parseTwigNodes() {
        $matches = array();
        preg_match_all('/\{\#\s*((?!\#\}).*)\s*\#\}|\{\%\s*([^\%\}]*)\s*\%\}|\{\{\s*([^\}\}]*)\s*\}\}/i', $this->getFileContents(), $matches);
        $this->all_twig_nodes = $matches[0];
        $this->annotaion_nodes = $matches[1];
        $this->tag_nodes = $matches[2];
        $this->print_nodes = $matches[3];
    }

    private function calculateNodesPos() {
        $file_occurs_pos = 0;
        foreach ($this->all_twig_nodes as $key => $node_str) {
            $occur_pos = strpos($this->getFileContents(), $node_str, $file_occurs_pos);
            $file_occurs_pos = $occur_pos + strlen($node_str);
            $this->all_twig_nodes_pos[$key] = $occur_pos;
        }
    }

    private function parseAnnotation() {
        foreach ($this->annotaion_nodes as $key => $node_str) {
            if (trim($node_str) === "") {
                continue;
            }
            $this->getAnnotations()->addAnnotation(new Annotation($node_str, $this, $this->all_twig_nodes_pos[$key]));
        }
    }

}
