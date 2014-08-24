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

use Chigi\Bundle\ChijiBundle\Util\StaticsManager;
use Chigi\Chiji\Project\ProjectConfig;
use Chigi\Chiji\Util\PathHelper;

/**
 * Description of SymfonyBundleProject
 *
 * @author 郷
 */
abstract class SymfonyBundleProjectConfig extends ProjectConfig {

    public function getReleaseRootPath() {
        $bundle_name = strtolower(preg_replace('#[A-Z][a-z0-9]#', '_$0', $this->getProjectName()));
        return PathHelper::searchRealPath(StaticsManager::getKernel()->getRootDir(), "../web/chiji/" . $bundle_name) . '/%s';
    }

    public function getReleaseRootUrl() {
        $bundle_name = strtolower(preg_replace('#[A-Z][a-z0-9]#', '_$0', $this->getProjectName()));
        return "{{ asset(\"chiji/" . $bundle_name . "/%s\") }}";
    }

    public function getProjectName() {
        return StaticsManager::getBundle()->getName();
    }

}
