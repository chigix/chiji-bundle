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

namespace Chigi\Bundle\ChijiBundle\Command;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * install components and demos
 *
 * @author 郷
 */
class InstallResourcesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName("chiji:install")
                ->setDescription("Install components and demos.")
                ->addArgument("name", InputArgument::REQUIRED, "A bundle name")
                ->setHelp("BANKAI");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            /* @var $bundle BundleInterface */
            $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument("name"));
        } catch (InvalidArgumentException $exc) {
            throw $exc;
        }
        $chiji_resources_path = $bundle->getPath() . '/Resources/chiji';
        /* @var $filesystem \Symfony\Component\Filesystem\Filesystem */
        $filesystem = $this->getContainer()->get('filesystem');
        if (is_dir($chiji_resources_path)) {
            $output->writeln("The Chiji Resources dir is not empty.");
        } else {
            try {
                $filesystem->mkdir($chiji_resources_path);
                $filesystem->mkdir($chiji_resources_path . '/modules');
                $filesystem->touch($chiji_resources_path . '/chiji-conf.php');
            } catch (\Symfony\Component\Filesystem\Exception\IOException $exc) {
                throw $exc;
            }
            $output->writeln("The Chiji Resources dir created.");
        }
    }

}
