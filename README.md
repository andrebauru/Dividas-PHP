# Dividas-PHP
Aqui está um exemplo básico de documentação que você pode usar no GitHub para o projeto:

---

# Gerenciador de Dívidas

Este projeto é uma aplicação web de gerenciamento de dívidas, que permite aos usuários adicionar, visualizar e editar dívidas. Ele também inclui uma seção para ver dívidas pagas e um sistema básico de autenticação.

## Funcionalidades

- **Adicionar Dívidas**: Permite adicionar novas dívidas com informações como descrição, valor, data de vencimento e quantidade de parcelas.
- **Gerenciar Dívidas**: Atualize, exclua ou marque dívidas como pagas.
- **Visualizar Dívidas Pagas**: Veja uma lista de dívidas pagas e o total pago durante o mês e o ano.
- **Autenticação**: Acesse o sistema com uma senha para proteger os dados.

## Configuração

### Pré-requisitos

- PHP 7.4 ou superior
- Servidor MySQL ou MariaDB
- Servidor web como Apache ou Nginx

### Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/gerenciador-dividas.git
   ```
2. Configure o banco de dados:
   - Crie um banco de dados no MySQL:
     ```sql
     CREATE DATABASE gerenciador_dividas;
     ```
   - Crie a tabela `dividas` com o seguinte SQL:
     ```sql
     CREATE TABLE dividas (
         id INT AUTO_INCREMENT PRIMARY KEY,
         descricao VARCHAR(255) NOT NULL,
         valor_total DECIMAL(10, 2) NOT NULL,
         data_vencimento DATE NOT NULL,
         parcelas INT NOT NULL,
         pago TINYINT(1) DEFAULT 0
     );
     ```
3. Configure o acesso ao banco de dados:
   - Crie um arquivo `conexao.php` com as credenciais de conexão:
     ```php
     <?php
     $host = 'localhost';
     $user = 'seu_usuario';
     $password = 'sua_senha';
     $dbname = 'gerenciador_dividas';
     $conn = new mysqli($host, $user, $password, $dbname);
     if ($conn->connect_error) {
         die('Erro de Conexão: ' . $conn->connect_error);
     }
     ?>
     ```

### Uso

1. Inicie o servidor web e acesse o aplicativo na URL apropriada.
2. Use a senha padrão `230788` para fazer login no sistema.

## Segurança

- **Proteção de Senha**: A senha de acesso é atualmente armazenada no código. Certifique-se de usar HTTPS e alterá-la regularmente.
- **Proteção de Dados**: Os dados das dívidas são armazenados no banco de dados. Certifique-se de configurar adequadamente as permissões de acesso.

## CONTATO
andretsc@gmail.com

---

Você pode personalizar essa documentação conforme necessário, adicionando mais detalhes sobre os requisitos ou informações técnicas.
![alt text](image.png)