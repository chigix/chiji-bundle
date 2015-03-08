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

use Chigi\Chiji\Annotation\FunctionAnnotation;
use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Chiji\Project\Project;
use Chigi\Chiji\Project\SourceRoad;
use Chigi\Chiji\Util\StaticsManager;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Build and deploy your front-end project.
 *
 * @author 郷
 */
class ReleaseResourcesCommand extends ContainerAwareCommand {

    use \Robo\Task\FileSystem;

use \Robo\Output;

    /**
     * Configures the current command.
     */
    protected function configure() {
        $this->setName("chiji:release")
                ->setDescription("Build and Deploy your front-end project.")
                ->addArgument("name", InputArgument::REQUIRED, "A bundle name")
                ->setHelp("BANKAI");
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int     null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        /* @var $filesystem Filesystem */
        $filesystem = $this->getContainer()->get('filesystem');
        try {
            /* @var $bundle BundleInterface */
            $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument("name"));
        } catch (InvalidArgumentException $exc) {
            throw $exc;
        }
        \Chigi\Bundle\ChijiBundle\Util\StaticsManager::setContainer($this->getContainer());
        \Chigi\Bundle\ChijiBundle\Util\StaticsManager::setBundle($bundle);
        /* @var $kernel KernelInterface */
        $kernel = $this->getContainer()->get('kernel');
        $chiji_resources_path = $bundle->getPath() . '/Resources/chiji';
        if (!is_dir($chiji_resources_path)) {
            throw new ResourceNotFoundException(sprintf("The Path (\"%s\") NOT FOUND", $chiji_resources_path));
        }
        $project = new Project($chiji_resources_path . '/chiji-conf.php');
        \Chigi\Chiji\Util\ProjectUtil::registerProject($project);
        // Clear all the release directories.
        foreach ($project->getReleaseDirs() as $dir_path) {
            $this->taskCleanDir($dir_path)->run();
        }
        $project->getCacheManager()->openCache();
        foreach ($project->getSourceDirs() as $dir_path) {
            if (is_dir($dir_path)) {
                $finder = new Finder();
                foreach ($finder->files()->followLinks()->in($dir_path) as $file) {
                    /* @var $file \Symfony\Component\Finder\SplFileInfo */
                    if (($road = $project->getMatchRoad(new \Chigi\Component\IO\File($file->getPathname()))) instanceof SourceRoad) {
                        $this->say('<' . $road->getName() . '>:' . $file->getPathname());
                    }
                }
            }
        }
        foreach ($project->getRegisteredResources() as $resource) {
            /* @var $resource \Chigi\Chiji\File\AbstractResourceFile */
            if ($resource instanceof \Chigi\Chiji\File\Annotation && $resource->getMemberId() === $resource->getFinalCache()->getMemberId()) {
                $resource->analyzeAnnotations();
            }
        }
        //$template_path = $this->getContainer()->get('templating.locator')->locate($this->getContainer()->get('templating.name_parser')->parse('ChigiBlogBundle:chiji:edit.html.twig'));
        //var_dump($this->getContainer()->get('templating.name_parser')->parse('ChigiBlogBundle:Post:edit.html.twig')->getPath());
        //var_dump($this->getTemplatePath($this->getContainer()->get('templating.name_parser')->parse('ChigiBlogBundle:Post:edit.html.twig')));
//        foreach (ResourcesManager::getAll() as $resource) {
//            // 遍历所有 resource 对象，并针对有目标输入流的资源对象写入模板 HTML
//            /* @var $resource AbstractResourceFile */
//            if ($resource instanceof RequiresMapInterface) {
//                //var_dump($resource->getRequires()->getArrayCopy());
//            }
//            foreach ($print_nodes as $node) {
//                if (trim($node) === "") {
//                    continue;
//                }
//                $include_match = array();
//                if (preg_match('#^include\([\'"](.+)[\'"]\)$#', trim($node), $include_match)) {
//                // 将最终结果写入到目标 TWIG 文件中
//                    $target_subtemplate = $this->getTemplatePath($this->getContainer()->get('templating.name_parser')->parse(end($include_match)));
//                    if (!is_file($target_subtemplate)) {
//                        $filesystem->touch($target_subtemplate);
//                    }
//                    file_put_contents($target_subtemplate, "QQCUM");
//                }
//            }
//        }
        foreach (StaticsManager::getPostEndFunctionAnnotations() as $function) {
            /* @var $function FunctionAnnotation */
            $function->execute();
        }
        $project->getCacheManager()->closeCache();
        return;
        var_dump($kernel->getEnvironment());
        var_dump($bundle->getPath() . '/Resources/');
        var_dump($this->getBasePathForClass($bundle->getName(), $bundle->getNamespace(), $bundle->getPath()));
    }

    /**
     * Get a base path for a class
     *
     * @param string $name      class name
     * @param string $namespace class namespace
     * @param string $path      class path
     *
     * @return string
     * @throws RuntimeException When base path not found
     */
    private function getBasePathForClass($name, $namespace, $path) {
        $namespace = str_replace('\\', '/', $namespace);
        $search = str_replace('\\', '/', $path);
        $destination = str_replace('/' . $namespace, '', $search, $c);

        if ($c != 1) {
            throw new RuntimeException(sprintf('Can\'t find base path for "%s" (path: "%s", destination: "%s").', $name, $path, $destination));
        }

        return $destination;
    }

    /**
     * Get the realpath for the target TemplateReference object
     * @param TemplateReferenceInterface $template
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function getTemplatePath(TemplateReferenceInterface $template) {
        $name = $template->getPath();
        if ('@' !== substr($name, 0, 1)) {
            throw new \InvalidArgumentException(sprintf('A resource name must start with @ ("%s" given).', $name));
        }

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $bundleName = substr($name, 1);
        $path = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $path) = explode('/', $bundleName, 2);
        }
        return $this->getApplication()->getKernel()->getBundle($bundleName)->getPath() . '/' . $path;
    }

}
