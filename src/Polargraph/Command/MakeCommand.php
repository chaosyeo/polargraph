<?php

namespace Polargraph\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class MakeCommand extends Command
{
    protected function configure()
    {
        $this->setName('make');
        $this->setDescription('Make photograph in polar effect');
        $this->setHelp("<info>php polargraph make input.jpg</info>");
        $this->setDefinition(array(
            new InputArgument('input',
                InputArgument::REQUIRED,
                'Input file'
            ),
            new InputOption('output',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Output file',
                'output.png'
            ),
            new InputOption('size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Size of the output file'
            )
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('input');
        if (!file_exists($source)) {
            throw new Exception($source . ' is not exists');
        }
        list($srcWidth, $srcHeight, $srcType) = getimagesize($source);
        switch ($srcType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($source);
                break;
            default:
                throw new Exception($source . ' file is not support');
        }

        $distWidth = $distHeight = $srcHeight;

        $srcResize = imagecreatetruecolor($distWidth, $distHeight);
        imagecopyresized($srcResize, imagerotate($source, 180, 0), 0, 0, 0, 0, $distWidth, $distHeight, $srcWidth, $srcHeight);

        imagedestroy($source);
        unset($source);
        unset($srcWidth, $srcHeight);

        $distDiagonal = $this->radius($distWidth, $distHeight);

        $dist = imagecreatetruecolor($distWidth, $distHeight);

        for ($distX = 0; $distX < $distWidth; $distX++) {
            for ($distY = 0; $distY < $distHeight; $distY++) {
                $relativeX = ($distX - $distWidth / 2) * 2 + 1;
                $relativeY = ($distY - $distHeight / 2) * 2 + 1;

                $radius = $this->radius($relativeX, $relativeY);
                $theta = $this->theta($relativeX, $relativeY);

                $srcResizeX = $theta / 360 * $distWidth;
                $srcResizeY = $radius * 2 * $distHeight / $distDiagonal;

                if(0 <= $srcResizeX && $srcResizeX < $distWidth && 0 <= $srcResizeY && $srcResizeY < $distHeight) {
                    $color = imagecolorat($srcResize, $srcResizeX, $srcResizeY);
                    imagesetpixel($dist, $distX, $distY, $color);
                } else {
                    $srcResizeY = $distHeight - 1;
                    $color = imagecolorat($srcResize, $srcResizeX, $srcResizeY);
                    imagesetpixel($dist, $distX, $distY, $color);
                }
            }
        }
        imagesavealpha($dist, true);
        imagejpeg($dist, $input->getOption('output'));
        imagedestroy($srcResize);
        imagedestroy($dist);
    }

    protected function radius(int $x, int $y) {
        return sqrt(pow($x, 2) + pow($y, 2));
    }

    protected function theta(int $x, int $y) {
        return atan2($y, $x) / pi() * 180 + 180;
    }
}