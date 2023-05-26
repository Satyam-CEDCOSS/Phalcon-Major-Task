<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;

class Users extends Model
{

    public $name;
    public $email;
    public $password;

    public function validation()
    {
        $validator = new Validation();

        // Robot name must be unique
        $validator->add(
            'name',
            new Uniqueness(
                [
                    'field'   => 'email',
                    'message' => 'Email must be unique',
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
