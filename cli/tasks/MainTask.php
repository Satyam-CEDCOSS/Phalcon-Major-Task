<?php

declare(strict_types=1);

namespace MyApp\Tasks;

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction(string $name, string $email, string $password)
    {
        $arr = [
            "name"=>$name,
            "email"=>$email,
            "password"=>$password,
            "type"=>"user",
        ];
        $this->mongo->users->insertOne($arr);
        echo "Inserted". PHP_EOL;

    }
}