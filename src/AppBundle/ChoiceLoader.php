<?php

namespace AppBundle;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class ChoiceLoader implements ChoiceLoaderInterface
{
    protected $choices;

    public function __construct($choices)
    {
        $this->choices = $choices;
    }

    public function loadChoiceList($value = null)
    {
        return new ArrayChoiceList($this->choices);
    }

    public function loadChoicesForValues(array $values, $value = null)
    {
        $result = [ ];

        foreach ($values as $val)
        {
            $key = array_search($val, $this->choices, true);

            if ($key !== false)
                $result[ ] = $key;
        }

        return $result;
    }

    public function loadValuesForChoices(array $choices, $value = null)
    {
        $result = [ ];

        foreach ($choices as $label)
        {
            if (isset($this->choices[ $label ]))
                $result[ ] = $this->choices[ $label ];
        }

        return $result;
    }
}
