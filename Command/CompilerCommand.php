<?php
namespace Phpugl\TwitterBootstrapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use lessc;

class CompilerCommand extends ContainerAwareCommand
{
    protected $kernel;
    protected $path_root;
    protected $path_resources;
    protected $path_twitter;
    protected $config = array();

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->path_resources = __DIR__ . '/../Resources';
    }

    protected function configure()
    {
        $this
            ->setName('twitter-bootstrap:compile')
            ->setDescription('Compile a version of bootstrap and paste it into Bundle public folder')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->kernel = $this->getContainer()->get('kernel');
        $this->path_root = $this->getContainer()->get('kernel')->getRootDir();

        // read config
        $this->config['config'] = $this->getContainer()->getParameter('phpugl_twitter_bootstrap.config');

        if (isset($this->config['config']['twitter_path'])) {
            $this->path_twitter = $this->config['config']['twitter_path'];
        }

        $this->config['less'] = $this->getContainer()->getParameter('phpugl_twitter_bootstrap.less');
        $this->config['images'] = $this->getContainer()->getParameter('phpugl_twitter_bootstrap.images');
        $this->config['javascript'] = $this->getContainer()->getParameter('phpugl_twitter_bootstrap.javascript');

        if (!empty($this->config['less'])) {
            $this->generateCss($output);
        }

        if (!empty($this->config['images'])) {
            $this->copyImages($output);
        }

        if (!empty($this->config['javascript'])) {
            $this->generateJavascript($output);
        }
    }

    protected function generateCss(OutputInterface $output)
    {
        $src_dir = $this->path_twitter . '/less';
        $less_dir = $this->createDirectory($this->path_resources . '/less');
        $out_dir = $this->createDirectory($this->path_resources . '/public/css');

        $less = new lessc;
        $less->setImportDir(array($less_dir, $src_dir));

        // read variable config and generate new variable less
        if (isset($this->config['less']['variables'])) {
            $variables_config = $this->config['less']['variables'];
            if (!empty($variables_config)) {
                $variables_file = file_get_contents($src_dir . DIRECTORY_SEPARATOR . 'variables.less');
                foreach ($variables_config as $key => $value) {
                    $variables_file = preg_replace('/@' . $key . ':.*;/', '@' . $key . ': ' . $value . ';', $variables_file);
                }
                file_put_contents($less_dir . DIRECTORY_SEPARATOR . 'variables.less', $variables_file);
            }
        }

        // merge less files from config
        $content = '';
        foreach ($this->config['less']['files'] as $file) {
            try {
                $path = $this->kernel->locateResource($file);
            } catch (\InvalidArgumentException $ex) {
                $path = $src_dir . DIRECTORY_SEPARATOR . $file;
            }

            if (file_exists($path)) {
                $output->writeln('<comment>Add ' . $file . ' to compiling source</comment>');
                $content .= file_get_contents($path);
            }
        }

        // compile less
        if (!empty($content)) {
            $compiled = $less->compile($content);

            if (!empty($compiled)) {
                file_put_contents($out_dir . DIRECTORY_SEPARATOR . $this->config['less']['out'], $compiled);
                $output->writeln('<info>Compiling successful, output has been written in @PhpuglTwitterBootstrapBundle/css/' . $this->config['less']['out'] .  '</info>');
            } else {
                throw new \Exception("The compiled output is empty.");
            }
        } else {
            throw new \Exception("Check you twitter-bootstrap.less.files configuration, there is nothing to compile.");
        }

        return true;
    }

    protected function generateJavascript(OutputInterface $output)
    {
        $src_dir = $this->path_twitter . '/js';
        $out_dir = $this->createDirectory($this->path_resources . '/public/js');

        $content = '';
        foreach ($this->config['javascript']['files'] as $file) {
            try {
                $path = $this->kernel->locateResource($file);
            } catch (\InvalidArgumentException $ex) {
                $path = realpath($src_dir . DIRECTORY_SEPARATOR . $file);
            }

            if (file_exists($path)) {
                $content .= file_get_contents($path);
                $output->writeln('<comment>Add ' . $file . ' to ' . $this->config['javascript']['out'] . '</comment>');
            } else {
                $output->writeln('<error>File ' . $file . ' not found in ' . $path . '</error>');
            }
        }

        if (false !== file_put_contents($out_dir . DIRECTORY_SEPARATOR .$this->config['javascript']['out'], $content)) {;
            $output->writeln('<info>Merging successful, output has been written in @PhpuglTwitterBootstrapBundle/js/' . $this->config['javascript']['out'] .  '</info>');
        }

        return true;
    }

    protected function copyImages(OutputInterface $output)
    {
        $src_dir = $this->path_twitter . '/img';
        $out_dir =  $this->createDirectory($this->path_resources . '/public/img');

        foreach ($this->config['images']['files'] as $file) {
            try {
                $path = $this->kernel->locateResource($file);
            } catch (\InvalidArgumentException $ex) {
                $path = realpath($src_dir . DIRECTORY_SEPARATOR . $file);
            }

            if (file_exists($path)) {
                if (copy($path, $out_dir . DIRECTORY_SEPARATOR . $file) === true) {
                    $output->writeln('<info>Copy ' . $file . ' to ' . $out_dir . '</info>');
                } else {
                    $output->writeln('<error>File ' . $file . ' could not be copied to ' . $out_dir . '</error>');
                }
            } else {
                $output->writeln('<error>File ' . $file . ' not found in ' . $path . '</error>');
            }
        }

        return true;
    }

    protected function createDirectory($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }
}
