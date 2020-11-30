<?php

namespace App\Console\Commands;

use App\Classes\Mastermind;
use App\Traits\ColorsDSGenerator;
use Illuminate\Console\Command;

class GameTerminal extends Command
{
    protected int $tryNumber = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play:mastermind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play mastermind through terminal';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function askColorPattern(Mastermind $mastermind)
    {
        $response =  $this->choice(
            'What is the secret? <fg=red>(write colors or related numbers separated by comma)',
            $mastermind->getColorsDataSet(),
            null,
            $maxAttempts = 3,
            $allowMultipleSelections = true
        );

        if (count($mastermind->getSecretColorPattern()) != count($response)) {
            $this->info('<fg=red> Upps! You should pick 4 colors');
            $response = $this->askColorPattern($mastermind);
        }

        return $response;

    }

    public function getInteractiveGameResult(Mastermind $mastermind)
    {
        if ( $this->tryNumber > 12 ) {
            $this->info('Nice try, but the machine has won!');
            return false;
        }

        $this->info('Oportunity #' . $this->tryNumber);

        $response = $this->askColorPattern($mastermind);
        $mastermind->setGuessedColorPattern($response);

        if  ( $mastermind->getGuessedColorPattern() == $mastermind->getSecretColorPattern() ) {
            $this->info('Well done, you have won!');
            $this->info('Your code is:');
            dump($mastermind->getGuessedColorPattern());
            $this->info('Machine\'s code is:');
            dump($mastermind->getSecretColorPattern());
            return true;
        }

        $this->tryNumber++;
        dump($mastermind->getHints());
        $this->getInteractiveGameResult($mastermind);

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('### Welcome to mastermind terminal game ###');
        sleep((1/2));

        $mastermind = new Mastermind();
        $this->getInteractiveGameResult($mastermind);
    }

}