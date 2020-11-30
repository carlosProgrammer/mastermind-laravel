<?php
namespace App\Traits;

trait ColorsDataSetGenerator {

    public function getColorsArray()
    {
        return [
            'white',
            'black',
            'yellow',
            'blue',
            'red',
            'green',
            'cyan',
            'pink'
        ];
    }

    public function getRandomColorsIndexValues(int $nodes)
    {
        $values = [];
        for ($row = 0; $row < $nodes; $row++) 
        {
            $values[$row] = array_rand(array_flip($this->getColorsArray()), 1);
        }
        return $values;
    }
}