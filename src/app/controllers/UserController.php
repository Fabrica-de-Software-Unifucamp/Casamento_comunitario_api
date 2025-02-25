<?php

class UserController {

    private $userModel;
    private $userView;

    public function __construct($userModel, $userView) {
        $this->userModel = $userModel;
        $this->userView = $userView;
    }

    public function createUser() {
        $data = json_decode(file_get_contents("php://input"));

        if (
            !isset($data->nome) ||
            !isset($data->cpf) ||
            !isset($data->rg) ||
            !isset($data->dataNascimento) ||
            !isset($data->estadoCivil) ||
            !isset($data->numero) ||
            !isset($data->rua) ||
            !isset($data->bairro) ||
            !isset($data->cidade) ||
            !isset($data->uf) ||
            !isset($data->cep) ||
            !isset($data->telefone1) ||
            !isset($data->telefone2)
        ) {
            $this->userView->showError("Todos os campos são obrigatórios");
            return;
        }

        $created = $this->userModel->createUser(
            $data->nome,
            $data->cpf,
            $data->rg,
            $data->dataNascimento,
            $data->estadoCivil,
            $data->numero,
            $data->rua,
            $data->bairro,
            $data->cidade,
            $data->uf,
            $data->cep,
            $data->telefone1,
            $data->telefone2
        );

        if ($created) {
            $this->userView->showSuccess("User created successfully");
        } else {
            $this->userView->showError("Failed to create user");
        }
    }
}
?>
