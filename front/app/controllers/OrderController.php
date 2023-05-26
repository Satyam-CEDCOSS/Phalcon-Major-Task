<?php

use Phalcon\Mvc\Controller;

session_start();

class OrderController extends Controller
{
    public function indexAction()
    {
        $data = $this->mongo->data->find();
        $txt = '<option selected disabled>-Select-</option>';
        foreach ($data as $value) {
            $txt .= '<option value=' . $value->name . '>' . $value->name . '</option>';
        }
        $this->view->result = $txt;
    }
    public function addAction()
    {
        $url = "http://172.23.0.4/order/create?role=admin";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        print_r($result);die;
        $this->response->redirect('/order/done');
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
            $data = $this->mongo->orders->find();
            $display = "";
            foreach ($data as $value) {
                $display .= '<tr>
                <td>' . $value->name . '</td><td>' . $value->pincode . '</td>
                <td>' . $value->product . '</td><td>' . $value->qunatity . '</td>
                <td><a href="/order/edit?id='.$value->_id.'" class="btn btn-warning">Edit</a></td>
                <td><a href="/order/delete?id='.$value->_id.'" class="btn btn-danger">Delete</a></td>
                </tr>';
            }
            $this->view->display  = $display;
        }
    }
    public function editAction()
    {
        $data = $this->mongo->data->find();
        $txt = '<option selected disabled>-Select-</option>';
        foreach ($data as $value) {
            $txt .= '<option value=' . $value->name . '>' . $value->name . '</option>';
        }
        $this->view->result = $txt;
        $id = $_GET['id'];
        $this->view->data = $this->mongo->orders->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);


    }
    public function updateAction()
    {
        $id = $_GET['id'];
        $url = "http://172.23.0.4/order/update/$id?role=admin";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'put');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_POST));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $this->response->redirect('/order/display');
    }
    public function deleteAction()
    {
        $id = $_GET['id'];
        $url = "http://172.23.0.4/order/delete/$id?role=admin";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'delete');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $this->response->redirect('/order/display');
    }
}
