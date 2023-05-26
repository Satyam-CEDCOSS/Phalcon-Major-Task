<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;


class Products extends Model
{
    public function validation()
    {
        $validator = new Validation();
        
        // Type must be: droid, mechanical or virtual
        $validator->add(
            "type",
            new InclusionIn(
                [
                    'message' => 'Type must be "Electronics", or "Jewellery"',
                    'domain' => [
                        'Electronics',
                        'Jewellery',
                        'Cloths',
                        'Cosmatics'
                    ],
                ]
            )
        );

        // Robot name must be unique
        $validator->add(
            'name',
            new Uniqueness(
                [
                    'field'   => 'name',
                    'message' => 'The Product name must be unique',
                ]
            )
        );

        // Year cannot be less than zero
        if ($this->price < 0) {
            $this->appendMessage(
                new Message('Price cannot be less than zero')
            );
        }

        // Check if any messages have been produced
        if ($this->validationHasFailed() === true) {
            return false;
        }
    }
}
