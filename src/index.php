<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

class CadastroConjuge {
    private $conn;
    private $cpf;

    // Construtor que recebe a conexão PDO e o CPF
    public function __construct($cpf) {
        $config = new Config();
        $this->conn = $config->getConnection();
        $this->cpf = $cpf;
    }

    // Função para enviar o arquivo
    private function enviarArquivo($error, $size, $name, $tmp_name) {
        $diretorio = "uploads/{$this->cpf}/";
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true); // Cria o diretório caso não exista
        }

        if ($error !== UPLOAD_ERR_OK) {
            return json_encode(["status" => "error", "mensagem" => "Erro ao enviar o arquivo: " . $error]);
        }

        if ($size > 5 * 1024 * 1024) {
            return json_encode(["status" => "error", "mensagem" => "Arquivo muito grande. O tamanho máximo é 5MB."]);
        }

        $nomeArquivo = uniqid() . "_" . basename($name);
        $caminhoArquivo = $diretorio . $nomeArquivo;

        if (move_uploaded_file($tmp_name, $caminhoArquivo)) {
            return $caminhoArquivo;
        } else {
            return json_encode(["status" => "error", "mensagem" => "Erro ao mover o arquivo para o diretório."]);
        }
    }

    // Função para cadastrar o cônjuge
    public function cadastrarConjuge($dadosConjuge) {
        try {
            // Inserir dados do cônjuge na tabela Conjuge
            $stmt = $this->conn->prepare("INSERT INTO Conjuge (Nome, CPF, RG, Data_Nascimento, Estado_Civil, Numero, Rua, Bairro, Cidade, UF, CEP, Telefone1, Telefone2) 
                                          VALUES (:nome, :cpf, :rg, :dataNascimento, :estadoCivil, :numero, :rua, :bairro, :cidade, :uf, :cep, :telefone1, :telefone2)");

            $stmt->bindParam(':nome', $dadosConjuge['nome']);
            $stmt->bindParam(':cpf', $this->cpf);
            $stmt->bindParam(':rg', $dadosConjuge['rg']);
            $stmt->bindParam(':dataNascimento', $dadosConjuge['dataNascimento']);
            $stmt->bindParam(':estadoCivil', $dadosConjuge['estadoCivil']);
            $stmt->bindParam(':numero', $dadosConjuge['numero']);
            $stmt->bindParam(':rua', $dadosConjuge['rua']);
            $stmt->bindParam(':bairro', $dadosConjuge['bairro']);
            $stmt->bindParam(':cidade', $dadosConjuge['cidade']);
            $stmt->bindParam(':uf', $dadosConjuge['uf']);
            $stmt->bindParam(':cep', $dadosConjuge['cep']);
            $stmt->bindParam(':telefone1', $dadosConjuge['telefone1']);
            $stmt->bindParam(':telefone2', $dadosConjuge['telefone2']);
            
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "mensagem" => "Cônjuge cadastrado com sucesso!", "id" => $this->conn->lastInsertId()]);
            } else {
                throw new Exception("Erro ao cadastrar o cônjuge.");
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "mensagem" => $e->getMessage()]);
        }
    }

    // Função para cadastrar os documentos do cônjuge
    public function cadastrarDocumentos($idConjuge, $documentos) {
        try {
            $caminhoDocumentos = [];

            foreach ($documentos as $key => $arquivo) {
                if ($arquivo) {
                    $caminhoArquivo = $this->enviarArquivo($arquivo['error'], $arquivo['size'], $arquivo['name'], $arquivo['tmp_name']);
                    if ($caminhoArquivo) {
                        $caminhoDocumentos[$key] = $caminhoArquivo;
                    } else {
                        return json_encode(["status" => "error", "mensagem" => "Erro ao enviar o arquivo: $key"]);
                    }
                }
            }

            if (!empty($caminhoDocumentos)) {
                $stmt = $this->conn->prepare("INSERT INTO Documentos (ID_Conjuge, Certidao_Nascimento, Copia_Identidade, Copia_Residencia, Certidao_Casamento, Certidao_Obito) 
                                              VALUES (:idConjuge, :certidaoNascimento, :copiaIdentidade, :copiaResidencia, :certidaoCasamento, :certidaoObito)");

                $stmt->bindParam(':idConjuge', $idConjuge);
                $stmt->bindParam(':certidaoNascimento', $caminhoDocumentos['certidao_nascimento'] ?? null);
                $stmt->bindParam(':copiaIdentidade', $caminhoDocumentos['copia_identidade'] ?? null);
                $stmt->bindParam(':copiaResidencia', $caminhoDocumentos['copia_residencia'] ?? null);
                $stmt->bindParam(':certidaoCasamento', $caminhoDocumentos['certidao_casamento'] ?? null);
                $stmt->bindParam(':certidaoObito', $caminhoDocumentos['certidao_obito'] ?? null);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => "success", "mensagem" => "Documentos cadastrados com sucesso!"]);
                } else {
                    throw new Exception("Erro ao cadastrar os documentos.");
                }
            } else {
                return json_encode(["status" => "error", "mensagem" => "Nenhum documento foi enviado."]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "mensagem" => $e->getMessage()]);
        }
    }
}

// Exemplo de uso
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cpf'])) {
        $cadastro = new CadastroConjuge($_POST['cpf']);
        $dadosConjuge = [
            'nome' => $_POST['nome'],
            'rg' => $_POST['rg'],
            'dataNascimento' => $_POST['dataNascimento'],
            'estadoCivil' => $_POST['estadoCivil'],
            'numero' => $_POST['numero'],
            'rua' => $_POST['rua'],
            'bairro' => $_POST['bairro'],
            'cidade' => $_POST['cidade'],
            'uf' => $_POST['uf'],
            'cep' => $_POST['cep'],
            'telefone1' => $_POST['telefone1'],
            'telefone2' => $_POST['telefone2'],
        ];
        
        // Cadastrar o cônjuge
        $cadastro->cadastrarConjuge($dadosConjuge);

        // Se houver documentos
        if (isset($_FILES['certidao_nascimento']) || isset($_FILES['copia_identidade']) || isset($_FILES['copia_residencia']) || isset($_FILES['certidao_casamento']) || isset($_FILES['certidao_obito'])) {
            $documentos = [
                'certidao_nascimento' => $_FILES['certidao_nascimento'] ?? null,
                'copia_identidade' => $_FILES['copia_identidade'] ?? null,
                'copia_residencia' => $_FILES['copia_residencia'] ?? null,
                'certidao_casamento' => $_FILES['certidao_casamento'] ?? null,
                'certidao_obito' => $_FILES['certidao_obito'] ?? null,
            ];
            $cadastro->cadastrarDocumentos($cadastro->conn->lastInsertId(), $documentos);
        }
    }
}
?>
