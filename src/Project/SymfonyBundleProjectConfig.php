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
use Chigi\Chiji\Project\BuildRoad;
use Chigi\Chiji\Project\ProjectConfig;
use Chigi\Component\IO\File;

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
        $lessRoad = new BundleLessRoad("LESSCSS", $this->generateCacheDir($this->getProjectRootDir()), new File("../web/chiji/" . $bundle_name, StaticsManager::getKernel()->getRootDir()));
        $lessRoad->bundleName = $bundle_name;
        $rootRoad = new RootRoad("ROOT", $this->generateCacheDir($this->getProjectRootDir()), new File('../web/chiji/' . $bundle_name, StaticsManager::getKernel()->getRootDir()));
        $rootRoad->bundleName = $bundle_name;
        $road_map->append($lessRoad);
        $road_map->append($rootRoad);
        $road_map->append(new TwigRoad('TWIG', new File('Resources/views', StaticsManager::getBundle()->getPath()), new File('Resources/views/chiji', StaticsManager::getBundle()->getPath())));
        $road_map->append(new BuildRoad("BuildCache", $this->getProjectRootDir()));
        return $road_map;
    }

}
