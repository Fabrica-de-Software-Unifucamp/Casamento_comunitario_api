<?php

class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createUser($nome, $cpf, $rg, $dataNascimento, $estadoCivil, $numero, $rua, $bairro, $cidade, $uf, $cep, $telefone1, $telefone2) {
        $stmt = $this->db->prepare("INSERT INTO Conjuge 
            (Nome, CPF, RG, Data_Nascimento, Estado_Civil, Numero, Rua, Bairro, Cidade, UF, CEP, Telefone1, Telefone2) 
            VALUES (:Nome, :CPF, :RG, :Data_Nascimento, :Estado_Civil, :Numero, :Rua, :Bairro, :Cidade, :UF, :CEP, :Telefone1, :Telefone2)");

        $stmt->bindParam(':Nome', $nome);
        $stmt->bindParam(':CPF', $cpf);
        $stmt->bindParam(':RG', $rg);
        $stmt->bindParam(':Data_Nascimento', $dataNascimento);
        $stmt->bindParam(':Estado_Civil', $estadoCivil);
        $stmt->bindParam(':Numero', $numero);
        $stmt->bindParam(':Rua', $rua);
        $stmt->bindParam(':Bairro', $bairro);
        $stmt->bindParam(':Cidade', $cidade);
        $stmt->bindParam(':UF', $uf);
        $stmt->bindParam(':CEP', $cep);
        $stmt->bindParam(':Telefone1', $telefone1);
        $stmt->bindParam(':Telefone2', $telefone2);

        return $stmt->execute();
    }
}

?>
