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
use Chigi\Chiji\Collection\RoadMap;
use Chigi\Chiji\Project\ProjectConfig;
use Chigi\Chiji\Project\SourceRoad;
use Chigi\Chiji\Util\PathHelper;

/**
 * Description of SymfonyBundleProject
 *
 * @author 郷
 */
abstract class SymfonyBundleProjectConfig extends ProjectConfig {

    public function getProjectName() {
        return StaticsManager::getBundle()->getName();
    }

    /**
     * Returns the roadmap for this project.
     * @return RoadMap
     */
    public function getRoadMap() {
        $bundle_name = strtolower(preg_replace('#[A-Z][a-z0-9]#', '_$0', $this->getProjectName()));
        $road_map = new RoadMap();
        $rootRoad = new RootRoad("ROOT", $this->getProjectRootPath(), PathHelper::searchRealPath(StaticsManager::getKernel()->getRootDir(), '../web/chiji/' . $bundle_name));
        $rootRoad->bundleName = $bundle_name;
        $road_map->append($rootRoad);
        $road_map->append(new TwigRoad('TWIG', PathHelper::searchRealPath(StaticsManager::getBundle()->getPath(), 'Resources/views'), PathHelper::searchRealPath(StaticsManager::getBundle()->getPath(), 'Resources/views/chiji')));
        return $road_map;
    }

}
