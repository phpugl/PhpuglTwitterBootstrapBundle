<?php
namespace Phpugl\TwitterBootstrapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ClearCommand extends ContainerAwareCommand
{
    public function __construct()
    {
        parent::__construct($name = null);
    }

    protected function configure()
    {
        $this
            ->setName('twitter-bootstrap:clear')
            ->setDescription('Delete any generated assetic file in PhpuglBootstrapBundle Resources')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../Resources/public/')->name('/(js$)|(less$)|(css$)|(img$)/')->sortByName();

        foreach ($finder->files() as $file) {
            unlink($file->getRealPath());
            $output->writeln('<comment>Delete '. $file->getFilename() .'</comment>');
        }
        $output->writeln('<info>Success every files had been removed</info>');
    }
}
