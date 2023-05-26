<?php

declare(strict_types=1);

namespace Tests\Unit;

use MyApp\Controllers\UserController;
use MyApp\Models\Users;
use MyApp\Models\Products;

class DatabaseTest extends AbstractUnitTest
{
    public function testAddUser()
    {
        $arr = [
            'name'=>'Satyam',
            'email'=>'b@b.com',
            'password'=>'123',
            'type'=>'user'
        ];
        $success = $this->mongo->users->insertOne($arr);
        $count = $success->getInsertedCount();
        $this->assertEquals($count, 1);
    }

    public function testUpdateUser()
    {
        $arr = [
            '_id' => '6574387292302389',
            'name'=>'Satyam Bajpai',
            'email'=>'sb@sb.com',
            'password'=>'12345',
            'type'=>'user'
        ];
        $id = $arr->id;
        $success = $this->mongo->data->updateOne(array("_id" =>
        new MongoDB\BSON\ObjectId($id)), array('$set' => $arr));
        $this->assertEquals($success, true);
    }

    public function testDeleteUser()
    {
        $success = $this->mongo->data->deleteOne(array("_id" =>
        new MongoDB\BSON\ObjectId($id)));
        $this->assertEquals($success, true);
    }

    public function testAddProduct()
    {
        $product = new Products();
        $product->name = 'OnePlus';
        $product->price = 60000;
        $product->type = 'Electronics';
        $product->quantity = 99;
        $success = $product->save();
        $this->assertEquals($success, true);
    }

    public function passwordcheck()
    {
        $password = 'Satyam@123';
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $result = true;
        } else {
           $result = false;
        }
        $this->assertEquals($result, true);
    }
}
