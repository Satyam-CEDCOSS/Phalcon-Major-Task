<?php

use Phalcon\Mvc\Controller;

session_start();

class ProductController extends Controller
{
    public function indexAction()
    {
        if ($_SESSION['type'] != 'admin') {
            echo "<h2>Access Denied :(</h2>";
            die;
        }
    }
    public function addAction()
    {
        $url = "http://172.23.0.4/product/create?role=admin";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $this->response->redirect('/product/done');
    }
    public function doneAction()
    {
        // redirect to view
    }
    public function displayAction()
    {
        if ($_SESSION['type'] != 'admin') {
            echo "<h2>Access Denied :(</h2>";
            die;
        } else {
            $data = $this->mongo->data->find();
            $display = "";
            foreach ($data as $value) {
                $display .= '<tr>
                <td>' . $value->name . '</td><td>' . $value->type . '</td>
                <td>' . $value->year . '</td>
                <td><a href="/product/edit?id=' . $value->_id . '" class="btn btn-warning">Edit</a></td>
                <td><a href="/product/delete?id=' . $value->_id . '" class="btn btn-danger">Delete</a></td>
                </tr>';
            }
            $this->view->display  = $display;
        }
    }
    public function editAction()
    {
        $id = $_GET['id'];
        $this->view->data = $this->mongo->data->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }
    public function updateAction()
    {
        $id = $_GET['id'];
        $url = "http://172.23.0.4/product/update/$id?role=admin";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'put');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_POST));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $this->response->redirect('/product/display');
    }
    public function deleteAction()
    {
        $id = $_GET['id'];
        $url = "http://172.23.0.4/product/delete/$id?role=admin";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'delete');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $this->response->redirect('/product/display');
    }
}
