<?php
namespace App\Classes;

use App\Traits\ColorsDSGenerator; // Yet to be created
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Mastermind 
{


    use ColorsDSGenerator;

    private array $colorsList;
    private array $guessedColorCombination;
    public array $secretColorCombination;

    public function __construct()
    {
        $this->guessedColorCombination = [];
        $this->colorsList = $this->getColorsArray();
        $this->secretColorCombination = $this->getRandomColorsIndexValues(4);
    }

    public function getColorsDS() : array 
    {
        $this->secretColorCombination = $colorPattern;
    }

    public function setSecretColorCombination(array $colorPattern)
    {
        $this->secretColorCombination = $colorPattern;
    }

    public function getSecretColorCombination() : array
    {
        return $this->secretColorCombination;
    }

    public function setGuessedColorCombination(array $guessedColorCombination)
    {
        $this->guessedColorCombination = $guessedColorCombination;
    }

    public function getGuessedColorCombination() : array
    {
        return $this->guessedColorCombination;
    }

    public function getClues()
    {
        $validator = Validator::make(
            [
                'guessed_combination' => $this->guessedColorCombination,
                'secret_combination' => $this->secretColorCombination
            ],
            [
                'guessed_combination' => [
                    'required',
                    'array',
                    Rule::in($this->colorsList),
                    'min:4',
                    'max:4',
                ],
                'secret_combination' => [
                    'required',
                    'array',
                    Rule::in($this->colorsList),
                    'min:4',
                    'max:4'
                ],
            ]
        );

        if ($validator->fails()) 
        {
            return $validator->errors();
        }

        $feedback = [];
        $codeMakerSecretCombination = $this->getSecretColorCombination();

        foreach($this->guessedColorCombination as $key => $key) 
        {
            if (isset($codeMakerSecretCombination[$key]) && $codeMakerSecretCombination[$key] == $key) {
                $feedback[] = 'black';
                unset($codeMakerSecretCombination[$key]);
            }
            elseif (in_array($key, $codeMakerSecretCombination, true)) 
            {
                $i = array_search($key, $codeMakerSecretCombination);
                unset($codeMakerSecretCombination[$i]);
                $feedback[] = 'white';
            } 
            else 
            {
                $feedback[] = '';
            }
        }
        return $feedback;
    }
}

?>