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

use Chigi\Bundle\ChijiBundle\File\TwigResourceFile;
use Chigi\Chiji\Annotation\FunctionAnnotation;
use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\RequiresMapInterface;
use Chigi\Chiji\Project\Project;
use Chigi\Chiji\Util\ResourcesManager;
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
        $this->initStatics();
        /* @var $filesystem Filesystem */
        $filesystem = $this->getContainer()->get('filesystem');
        try {
            /* @var $bundle BundleInterface */
            $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument("name"));
        } catch (InvalidArgumentException $exc) {
            throw $exc;
        }
        \Chigi\Bundle\ChijiBundle\Util\StaticsManager::setBundle($bundle);
        $this->clearBundleRelease($bundle);
        $chiji_resources_path = $bundle->getPath() . '/Resources/chiji';
        if (!is_dir($chiji_resources_path)) {
            throw new ResourceNotFoundException(sprintf("The Path (\"%s\") NOT FOUND", $chiji_resources_path));
        }
        $project = new Project($chiji_resources_path . '/chiji-conf.php');
        Project::registProject($project, TRUE);
        //$template_path = $this->getContainer()->get('templating.locator')->locate($this->getContainer()->get('templating.name_parser')->parse('ChigiBlogBundle:chiji:edit.html.twig'));
        //var_dump($this->getContainer()->get('templating.name_parser')->parse('ChigiBlogBundle:Post:edit.html.twig')->getPath());
        //var_dump($this->getTemplatePath($this->getContainer()->get('templating.name_parser')->parse('ChigiBlogBundle:Post:edit.html.twig')));
        /* @var $templates array<TemplateReference> */
        $templates = $this->findTemplatesInFolder($bundle->getPath() . '/Resources/views');
        foreach ($templates as $template) {
            // 对所有 twig 模板文件进行遍历，交由ResourceManager 进行管理
            // 进而在 ResourceManager 中形成 加载资源表 map
            /* @var $template TemplateReferenceInterface */
            $template->set("bundle", $bundle->getName());
            ResourcesManager::getResource(
                    new TwigResourceFile($this->getContainer()->get('templating.locator')->locate($template))
            );
        }
        foreach (ResourcesManager::getAll() as $resource) {
            // 遍历所有 resource 对象，并针对有目标输入流的资源对象写入模板 HTML
            /* @var $resource AbstractResourceFile */
            if ($resource instanceof RequiresMapInterface) {
                //var_dump($resource->getRequires()->getArrayCopy());
            }
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
        }
        foreach (StaticsManager::getPostEndFunctionAnnotations() as $function) {
            /* @var $function FunctionAnnotation */
            $function->execute();
        }
        exit;
        /* @var $kernel KernelInterface */
        $kernel = $this->getContainer()->get('kernel');
        var_dump($kernel->getEnvironment());
        var_dump($bundle->getPath() . '/Resources/');
        var_dump($this->getBasePathForClass($bundle->getName(), $bundle->getNamespace(), $bundle->getPath()));
    }

    /**
     * Find templates in the given directory.
     *
     * @param string $dir The folder where to look for templates
     *
     * @return array<Symfony\Component\Templating\TemplateReferenceInterface> An array of templates of type TemplateReferenceInterface
     */
    private function findTemplatesInFolder($dir) {
        $templates = array();

        if (is_dir($dir)) {
            $finder = new Finder();
            foreach ($finder->files()->followLinks()->in($dir) as $file) {
                $template = $this->getContainer()->get('templating.filename_parser')->parse($file->getRelativePathname());
                if (false !== $template) {
                    $templates[] = $template;
                }
            }
        }

        return $templates;
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

    /**
     * Clear the old compilation cache and init the project release dir.
     * @param BundleInterface $bundle
     */
    protected function clearBundleRelease(BundleInterface $bundle) {
        /* @var $filesystem Filesystem */
        $filesystem = $this->getContainer()->get('filesystem');
        $chiji_subview_path = $bundle->getPath() . '/Resources/views/chiji';
        if (is_dir($chiji_subview_path)) {
            $this->taskCleanDir($chiji_subview_path)->run();
        } else {
            $filesystem->mkdir($chiji_subview_path);
        }
    }

    /**
     * Initial the current symfony statics manager
     */
    private function initStatics() {
        \Chigi\Bundle\ChijiBundle\Util\StaticsManager::setContainer($this->getContainer());
    }

}
