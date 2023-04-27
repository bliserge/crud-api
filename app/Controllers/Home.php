<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }
    public function appendHeader()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) == "OPTIONS") {
            $this->response->appendHeader('Access-Control-Allow-Origin', '*');
            $this->response->appendHeader('Access-Control-Allow-Methods', '*');
            $this->response->appendHeader('Access-Control-Allow-Credentials', 'true');

            $this->response->appendHeader('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

            $this->response->setJSON(array("success", "okay"));
            $this->response->send();
            exit();
        }
        $this->response->appendHeader("Access-Control-Allow-Origin", "*");
        $this->response->appendHeader("Access-Control-Allow-Methods", "*");
        $this->response->appendHeader("Access-Control-Max-Age", 3600);
        $this->response->appendHeader("Access-Control-Allow-Headers", "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }
    public function createUser() {
        $this->appendHeader();
        $mdl = new UsersModel();
        $input = json_decode(file_get_contents("php://input"));
        try {
            $id = $mdl->insert([
                "names" => $input->names,
                "password" => $input->password
            ]);
        }catch(\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(["error" => $e->getMessage()]);
        }
        return $this->response->setJSON(["message" => "User created successfully","id" => $id]);
    }

    public function getUsers() {
        $this->appendHeader();
        $mdl = new UsersModel();
        $result = $mdl->select("names,id,DATE(created_at) as date,password")->get()->getResultArray();
        return $this->response->setJSON($result);
    }

    public function EditUser() {
        $this->appendHeader();
        $mdl = new UsersModel();
        $input = json_decode(file_get_contents("php://input"));
        try {
            $mdl->save([
                "id" => $input->id,
                "names" => $input->names,
                "password" => $input->password
            ]);
        }catch(\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(["error" => $e->getMessage()]);
        }
        return $this->response->setJSON(["message" => "User updated successfully"]);
    }

    public function deleteUser($id = '', $temp =null)
    {
        $this->appendHeader();
        $mdl = new UsersModel();
        try {
            $mdl->delete(['id' => $id]);
            return $this->response->setJSON(["message" => "User deleted successfully"]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(["error" => $e->getMessage()]);
        }
    }
}
