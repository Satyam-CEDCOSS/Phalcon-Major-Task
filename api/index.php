<?php

require_once  __DIR__ . "/vendor/autoload.php";

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/models/',
    ]
);



$loader->register();

$container = new FactoryDefault();


$manager = new Manager();
$manager->attach(
    'micro:beforeExecuteRoute',
    function () {
        $role = $_GET['role'];
        $signer  = new Hmac();
        $builder = new Builder($signer);
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $builder
            ->setSubject($role)
            ->setPassphrase($passphrase);
        $token = $builder->getToken();
        $parser = new Parser();
        $tokenObject = $parser->parse($token->getToken());
        $role = $tokenObject->getclaims()->getpayload()['sub'];

        $acl = new Memory();
        $acl->addRole('user');
        $acl->addRole('admin');
        $new = $_GET['_url'];

        $ar = explode("/", $new);
        $acl->addComponent(
            'product',
            [
                'search',
                'get',

            ]
        );
        $acl->addComponent(
            'order',
            [
                'create',
                'update',

            ]
        );
        $acl->allow("admin", '*', '*');
        $acl->allow("user", 'product', 'search');
        $acl->allow("user", 'order', 'create');
        if (!($acl->isAllowed($role, $ar[1], $ar[2]))) {
            echo '<h1>Access denied :(</h1>';
            die;
        }
    }

);

$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            "mongodb+srv://root:Password123@mycluster.qjf75n3.mongodb.net/?retryWrites=true&w=majority"
        );

        return $mongo->api;
    },
    true
);

$app = new Micro($container);
$app->setEventsManager($manager);

$app->get(
    '/api/product',
    function (): void {
        $movies = $this->mongo->data->find();

        $data = [];

        foreach ($movies as $movie) {
            $data[] = [$movie];
        }
        echo json_encode($data);
    }
);

$app->get(
    '/api/product/{id}',
    function ($id): void {
        $movies = $this->mongo->data->findOne(array('_id' => new MongoDB\BSON\ObjectId($id)));
        print_r(json_encode($movies));
    }
);

$app->post(
    '/product/create',
    function () {
        $success = $this->mongo->data->insertOne($_POST);
        print_r($success);
    }
);

$app->put(
    '/product/update/{id}',
    function ($id) use ($app) {
        $data = $app->request->getJsonRawBody();
        $success = $this->mongo->data->updateOne(array("_id" =>
        new MongoDB\BSON\ObjectId($id)), array('$set' => $data));
        return $success;
    }
);

$app->delete(
    '/product/delete/{id}',
    function ($id) {
        $success = $this->mongo->data->deleteOne(array("_id" =>
        new MongoDB\BSON\ObjectId($id)));
        return $success;
    }
);

$app->post(
    '/order/create',
    function () {
        var_dump($_POST);die;
        $success = $this->mongo->orders->insertOne($_POST);
        print_r($success);
    }
);

$app->put(
    '/order/update/{id}',
    function ($id) use ($app) {
        $data = $app->request->getJsonRawBody();
        $success = $this->mongo->orders->updateOne(array("_id" =>
        new MongoDB\BSON\ObjectId($id)), array('$set' => $data));
        return $success;
    }
);

$app->delete(
    '/order/delete/{id}',
    function ($id) {
        $success = $this->mongo->orders->deleteOne(array("_id" =>
        new MongoDB\BSON\ObjectId($id)));
        return $success;
    }
);

$app->handle(
    $_SERVER["REQUEST_URI"]
);
