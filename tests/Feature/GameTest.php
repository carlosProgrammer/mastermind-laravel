<?php

namespace Tests\Feature;

use App\Classes\Mastermind;
use App\Traits\ColorDSGenerator;
use Illuminate\Support\MessageBag;
use Tests\TestCase;

class MastermindGameTest extends TestCase
{
    public function test_trait_dependency_exists()
    {
        $this->assertTrue(trait_exists(ColorsDSGenerator::class));
    }

    public function test_mastermind_class_exists()
    {
        $this->assertTrue(class_exists(Mastermind::class));
    }

    public function test_mastermind_class_is_instantiatable()
    {
        $object = new Mastermind();
        $this->assertInstanceOf(Mastermind::class, $object);
    }

    public function test_secret_color_combination_is_defined_and_length_is_four()
    {
        $object = new Mastermind();
        $this->assertIsArray($object->getSecretColorCombination());
        $this->assertCount(4, $object->getSecretColorCombination());
    }

    public function test_colors_DS_is_defined()
    {
        $object = new Mastermind();
        $this->assertIsArray($object->getColorsDS());
        $this->assertGreaterThanOrEqual(4, $object->getColorsDS());
    }

    public function test_validation_error_when_secret_color_combination_is_not_valid()
    {
        $object = new Mastermind();
        $object->setSecretColorCombination([]);
        $this->assertInstanceOf(MessageBag::class, $object->getClues());
        $this->assertTrue(isset($object->getClues()->messages()['secret_combination'][0]));
    }

    public function test_secret_combination_color_cannot_be_custom_colors_out_of_dataset()
    {
        $object = new Mastermind();
        $object->setSecretColorCombination(['red', 'yellow', 'xxx', 'blue']);
        $this->assertInstanceOf(MessageBag::class, $object->getClues());
        $this->assertTrue(isset($object->getClues()->messages()['secret_combination'][0]));
    }

    public function test_secret_color_combination_can_be_defined()
    {
        $object = new Mastermind();
        $payload = ['yellow','blue','red', 'black'];
        $object->setSecretColorCombination($payload);
        $this->assertEquals($payload, $object->getSecretColorCombination());
    }

    public function test_validation_error_when_guessed_color_combination_is_not_valid()
    {
        $object = new Mastermind();
        $object->setGuessedColorCombination([]);
        $this->assertInstanceOf(MessageBag::class, $object->getClues());
        $this->assertTrue(isset($object->getClues()->messages()['guessed_combination'][0]));
    }

    public function test_guess_colors_must_exists_in_colors_DS()
    {
        $object = new Mastermind();
        $object->setGuessedColorCombination(['black', 'green', 'yellow', 'white']);
        $this->assertInstanceOf(MessageBag::class, $object->getClues());
        $this->assertTrue(isset($object->getClues()->messages()['guessed_combination'][0]));
    }

    public function test_guessed_color_can_be_defined()
    {
        $object = new Mastermind();
        $object->setGuessedColorCombination(['red','blue','yellow', 'black']);
        $this->assertEquals(['red','blue','yellow', 'black'], $object->getGuessedColorCombination());
    }

    public function test_game_precise_feedback()
    {
        $object = new Mastermind();
        $object->setSecretColorCombination(['black','white','green', 'yellow']);
        $object->setGuessedColorCombination(['black','white','green', 'yellow']);
        $this->assertEquals(['black', 'black', 'black', 'black'], $object->getClues());
    }

    public function test_game_has_coincidences_but_not_precise_feedback()
    {
        $object = new Mastermind();
        $object->setSecretColorCombination(['black','white','yellow', 'green']);
        $object->setGuessedColorCombination(['white', 'black', 'green', 'yellow']);
        $this->assertEquals(['white', 'white', 'white', 'white'], $object->getClues());
    }

    public function test_game_has_not_coincidences_feedback()
    {
        $object = new Mastermind();
        $object->setSecretColorCombination(['red','blue','green', 'white']);
        $object->setGuessedColorCombination(['black', 'yellow', 'cyan', 'pink']);
        $this->assertEquals(['', '', '', ''], $object->getClues());
    }

    public function test_game_has_coincidences_and_wrongs_feedback()
    {
        $object = new Mastermind();
        $object->setSecretColorCombination(['red','blue','green', 'pink']);
        $object->setGuessedColorCombination(['black', 'red', 'cyan', 'green']);
        $this->assertEquals(['', 'white', '', 'white'], $object->getClues());
    }

    public function test_game_has_mixed_feedback()
    {
        $object = new Mastermind();
        $object->setSecretColorCombination(['black','white','blue', 'green']);
        $object->setGuessedColorCombination(['black', 'blue', 'yellow', 'cyan']);
        $this->assertEquals(['black', 'white', '', ''], $object->getClues());
    }

}
