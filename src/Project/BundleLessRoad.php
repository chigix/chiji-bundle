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

use Chigi\Chiji\Project\LessRoad;
use Chigi\Chiji\Project\UrlStampEnum;

/**
 * The less road for symfony bundle project only.
 *
 * @author 郷
 */
class BundleLessRoad extends LessRoad {

    public $bundleName = "";

    /**
     * Get the release format map.
     * @return array<typename==string>
     */
    protected function getReleaseFormatMap() {
        $map = parent::getReleaseFormatMap();
        $map['TWIG_CSS_LINK'] = '<link type="text/css" href="{{ asset("chiji/' . $this->bundleName . '/[FILE]") }}?[STAMP]" rel="stylesheet">';
        return $map;
    }

    protected function getUrlStampType() {
        return UrlStampEnum::HASH;
    }

}
