<?php
/**
 * MageSpec
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License, that is bundled with this
 * package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 *
 * http://opensource.org/licenses/MIT
 *
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email
 * to <magetest@sessiondigital.com> so we can send you a copy immediately.
 *
 * @category   MageTest
 * @package    PhpSpec_MagentoExtension
 *
 * @copyright  Copyright (c) 2012-2013 MageTest team and contributors.
 */
namespace MageTest\PhpSpec\MagentoExtension\CodeGenerator\Generator;

use MageTest\PhpSpec\MagentoExtension\Locator\Magento\ModuleInitXmlResource;
use PhpSpec\Console\IO;
use PhpSpec\CodeGenerator\TemplateRenderer;
use PhpSpec\CodeGenerator\Generator\GeneratorInterface;
use PhpSpec\Util\Filesystem;
use PhpSpec\Locator\ResourceInterface;

/**
 * BlockGenerator
 *
 * @category   MageTest
 * @package    PhpSpec_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/MageSpec/contributors)
 */
class ModuleInitXmlGenerator implements GeneratorInterface
{
    private $io;
    private $templates;
    private $filesystem;

    public function __construct(IO $io, TemplateRenderer $templates, Filesystem $filesystem = null)
    {
        $this->io         = $io;
        $this->templates  = $templates;
        $this->filesystem = $filesystem ?: new Filesystem;
    }

    public function supports(ResourceInterface $resource, $generation, array $data)
    {
        return $resource instanceof ModuleInitXmlResource;
    }

    public function generate(ResourceInterface $resource, array $data = array())
    {
        $filepath = $resource->getSrcFilename();
        if ($this->filesystem->pathExists($filepath)) {
            $message = sprintf('File "%s" already exists. Overwrite?', basename($filepath));
            if (!$this->io->askConfirmation($message, false)) {
                return;
            }

            $this->io->writeln();
        }

        $path = dirname($filepath);
        if (!$this->filesystem->isDirectory($path)) {
            $this->filesystem->makeDirectory($path);
        }

        $values = array(
            '%filepath%'        => $filepath,
            '%name%'            => $resource->getName(),
        );

        if (!$content = $this->templates->render('mage_init_xml', $values)) {
            $content = $this->templates->renderString(
                file_get_contents(__FILE__, null, null, __COMPILER_HALT_OFFSET__), $values
            );
        }

        $this->filesystem->putFileContents($filepath, $content);
        $this->io->writeln(sprintf(
            "<info>Magento module init xml <value>%s</value> created in <value>%s</value>.</info>\n",
            $resource->getName(), $filepath
        ));
    }

    public function getPriority()
    {
        return 30;
    }
}
__halt_compiler();<?php%namespace_block%

<config>
    <modules>
        <%name%>
            <active>true</active>
            <codePool>local</codePool>
        </%name%>
    </modules>
</config>
