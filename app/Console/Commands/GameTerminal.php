<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Array_;

class GameTerminal extends Command
{
    public array $colorsDS = [
        'white',
        'black',
        'yellow',
        'blue',
        'red',
        'green',
        'cyan',
        'pink'
    ];

    public array $secretPatternMaker;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mastermind:play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To play mastermind via terminal';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // The welcome message
        $this->secretPatternMaker ?? $this->getTerminalMessage();

        // Creating the color code. Duplicates may happen.
        $this->secretPatternMaker = $this->secretPatternMaker ?? $this->getRandValuesFromArray($this->colorsDS, 4);

        // Try to guess the color code.
        $breakAttempt = $this->getColorCodeBreakAttemptValidation();

        /*
            Feedback from codemaker placing >= 0 && <= 4  pegs on the guesses's row.
        */
        $attemptFeedback = $this->getSecretPatterMakerFeedBack($breakAttempt);

        // The result is to build a function that takes a list of 4 random color and returns a list of >= 0 && <= 4 black and/or white pegs.
        dump($feedback);

        if(count(array_unique($attemptFeedback)) === 1 && end($attemptFeedback) === 'black'){
            $this->info('Congrats, you are the winner!');
            dump($this->secretPatternMaker);
            $playAgain = $this->choice('Play again?', ['y', 'n']);
            if($playAgain == 'y') {
                $this->$secretPatternMaker = [];
                $this->handle();
            }
            $this->info('See you soon');

        } else {
            $this->handle();
        }

    }
    // Terminal messages
    public function getTerminalMessage(int $status = null) 
    {
        switch($status) {
            default:
            $this->info('Mastermind Game');
                sleep(3);
            $this->info('CodeMaker is crafting a secret color combination');
                sleep(3);
        }
    }

    // Function to return a list of random values, including duplicates.
    public function getRandValuesFromArray(array $array, int $nodes)
    {
        $vals = [];
        for ($row =0; $row < $nodes; $row++){
            $vals[$row] = array_rand($array, 1);
        }
        return $vals;
    }

    // Returns breaker's attempt
    public function getColorCodeBreakAttemptValidation()
    {
        $res = $this->choice(
            'What is the code? <fg=blue> separate choices with commas',
            $this->$colorsDS,
            null,
            $maxAttempts = 3,
            $multipleSelectionsAllowed = true
        );
        return $this->validateBreakAttempt($res);
    }

    // Validating braker's attempt
    public function validateBreakAttempt(array $res)
    {
        $res = [];

        if(count($res) != count($this->secretPatternMaker)) {
            $this->error(count($this->secretPatternMaker). 'colors are required');
            return $this->getColorCodeBreakAttemptValidation();
        }

        foreach ($res as $val) {
            $res[] = array_search($val, $this->colorsDS);
        }

        return $res;
    }

    // Verifies $breakAttempt and returns $attemptFeedback
    public function getSecretPatterMakerFeedBack(array $breakAttempt)
    {
        $attemptFeedback = [];
        $secret = $this->secretPatternMaker;
        foreach($breakAttempt as $key => $value) {

            if (isset($secret[$key]) && $secret[$key] == $value) 
            {
                $attemptFeedback[] = 'black';
                unset($secret[$key]);

            } 
            elseif (in_array($value, $secret, true)) 
            {
                $i = array_search($value, $secret);
                unset($secret[$i]);
                $attemptFeedback[] = 'white';

            }
            else
             {
                $attemptFeedback[] = '';
            }
        }
        return $attemptFeedback;
    }


}
