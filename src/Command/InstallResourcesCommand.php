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

use Chigi\Component\IO\File;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * install components and demos
 *
 * @author 郷
 */
class InstallResourcesCommand extends ContainerAwareCommand {
    
    use \Robo\Task\FileSystem;

    protected function configure() {
        $this->setName("chiji:install")
                ->setDescription("Install components to the target bundle.")
                ->addArgument("name", InputArgument::REQUIRED, "A bundle name")
                ->addOption("force", null, InputOption::VALUE_NONE, "Causes The installation physically executed against the existed bundle chiji directories.")
                ->setHelp("<info>%command.name%</info> command installs the common "
                        . "chiji frontend configurations and dirs into the given "
                        . "symfony bundle.");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            /* @var $bundle BundleInterface */
            $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument("name"));
        } catch (InvalidArgumentException $exc) {
            throw $exc;
        }
        $chiji_resources_path = $bundle->getPath() . '/Resources/chiji';
        /* @var $filesystem Filesystem */
        $filesystem = $this->getContainer()->get('filesystem');
        if ($input->getOption("force")) {
            $this->taskDeleteDir($chiji_resources_path)->run();
        }
        if (is_dir($chiji_resources_path)) {
            $output->writeln("The Chiji Resources dir is not empty.");
            return;
        }
        try {
            $filesystem->mkdir($chiji_resources_path);
            $filesystem->mkdir($chiji_resources_path . '/modules');
            $filesystem->touch($chiji_resources_path . '/chiji-conf.php');
        } catch (IOException $exc) {
            throw $exc;
        }
        $output->writeln("The Chiji Resources dir created.");
        $this->generateConfFile($bundle, new File("chiji-conf.php", $chiji_resources_path));
    }

    private function generateConfFile(BundleInterface $bundle, File $outputFile) {
        /* @var $chiji_bundle BundleInterface */
        $chiji_bundle = $this->getApplication()->getKernel()->getBundle("ChigiChijiBundle");
        $templates_dir = new File("Resources/templates", $chiji_bundle->getPath());
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($templates_dir->getAbsolutePath()));
        \file_put_contents($outputFile->getAbsolutePath(), $twig->loadTemplate("ProjectConfig.php.twig")->render(array("bundleName" => $bundle->getName())));
    }

}
