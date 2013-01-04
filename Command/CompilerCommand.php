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
    protected $path_root;
    protected $path_twitter;
    protected $path_resources;
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
        $this->path_root = $this->getContainer()->get('kernel')->getRootDir();
        $this->path_twitter = $this->path_root . '/../vendor/twitter/bootstrap/twitter/bootstrap';

        // read config
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
        $less_dir = $this->createDirectory($this->path_resources . '/less');
        $out_dir = $this->createDirectory($this->path_resources . '/public/css');

        $less = new lessc;
        $less->setImportDir(array($less_dir, $this->path_twitter  . '/less'));

        $output->writeln('<comment>Writing bootstrap.css from bootstrap.less</comment>');

        // read variable config and generate new variable less
        if (isset($this->config['less']['variables'])) {
            $variables_config = $this->config['less']['variables'];
            if (!empty($variables_config)) {
                $variables_file = file_get_contents($this->path_twitter . '/less/variables.less');
                foreach ($variables_config as $key => $value) {
                    $variables_file = preg_replace('/@' . $key . ':.*;/', '@' . $key . ': ' . $value . ';', $variables_file);
                }
                file_put_contents($less_dir . DIRECTORY_SEPARATOR . 'variables.less', $variables_file);
            }
        }

        // merge less files from config
        $content = "";
        $less_files = $this->config['less']['files'];
        foreach ($less_files as $file) {
            $content .= file_get_contents($this->path_twitter . '/less/' . $file);
        }

        // compile less
        if (!empty($content)) {
            $compiled = $less->compile($content);
            if (!empty($compiled)) {
                file_put_contents($out_dir . DIRECTORY_SEPARATOR . $this->config['less']['out'], $compiled);
                $output->writeln('<info>Success, bootstrap.css has been written in @PhpuglTwitterBootstrapBundle/css/' . $this->config['less']['out'] .  '</info>');
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
        $in_dir = $this->path_twitter . '/js';
        $out_dir = $this->createDirectory($this->path_resources . '/public/js');

        $javascript_files = $this->config['javascript']['files'];

        $content = '';
        foreach ($javascript_files as $file) {
            $path = realpath($in_dir . DIRECTORY_SEPARATOR . $file);
            if (file_exists($path)) {
                $content .= file_get_contents(realpath($in_dir . DIRECTORY_SEPARATOR . $file));
                $output->writeln('<info>Add ' . $file . ' to ' . $this->config['javascript']['out'] . '</info>');
            } else {
                $output->writeln('<error>File ' . $file . ' not found in ' . $path . '</error>');
            }
        }

        if (false !== file_put_contents($out_dir . DIRECTORY_SEPARATOR .$this->config['javascript']['out'], $content)) {;
            $output->writeln('<info>Success, bootstrap.css has been written in @PhpuglTwitterBootstrapBundle/js/' . $this->config['javascript']['out'] .  '</info>');
        }

        return true;
    }

    protected function copyImages(OutputInterface $output)
    {
        $image_dir = $this->path_twitter . '/img';
        $out_dir =  $this->createDirectory($this->path_resources . '/public/img');

        $image_files = $this->config['images']['files'];

        foreach ($image_files as $file) {
            $path = realpath($image_dir . DIRECTORY_SEPARATOR . $file);
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
