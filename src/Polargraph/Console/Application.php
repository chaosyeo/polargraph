<?php

namespace Polargraph\Console;

use Polargraph\DiInterface;
use Symfony\Component\Console\Application as Base;
use Polargraph\Polargraph;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Polargraph\Command as Command;
use Polargraph\Di\InjectableTrait;

class Application extends Base
{
    use InjectableTrait;

    private static $logo = '
    
P)ppppp   O)oooo  L)         A)aa   R)rrrrr    G)gggg R)rrrrr    A)aa   P)ppppp  H)    hh 
P)    pp O)    oo L)        A)  aa  R)    rr  G)      R)    rr  A)  aa  P)    pp H)    hh 
P)ppppp  O)    oo L)       A)    aa R)  rrr  G)  ggg  R)  rrr  A)    aa P)ppppp  H)hhhhhh 
P)       O)    oo L)       A)aaaaaa R) rr    G)    gg R) rr    A)aaaaaa P)       H)    hh 
P)       O)    oo L)       A)    aa R)   rr   G)   gg R)   rr  A)    aa P)       H)    hh 
P)        O)oooo  L)llllll A)    aa R)    rr   G)ggg  R)    rr A)    aa P)       H)    hh

    ';

    public function __construct(DiInterface $di)
    {
        parent::__construct('Polargraph', Polargraph::VERSION);
        $this->setDi($di);
    }

    public function getHelp()
    {
        return self::$logo . parent::getHelp();
    }

    protected function getDefaultCommands()
    {
        $commands = array_merge(parent::getDefaultCommands(), array(
            new Command\AboutCommand(),
            new Command\MakeCommand()
        ));
        return $commands;
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return parent::run($input, $output);
    }
}