-- Estrutura completa das tabelas usadas pelo Sistema de Vendas AlphaSolutions
-- Use este arquivo no phpMyAdmin (Importar) para criar o banco em um servidor novo.
-- Certifique-se de selecionar a base de dados correta antes de importar.

/* ===========================================================
   TABELA: usuarios
   -----------------------------------------------------------
   - Armazena utilizadores do sistema (admin e clientes)
   - Usada para login e associação de vendas (cliente_id)
   =========================================================== */

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  sobrenome VARCHAR(100) DEFAULT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo VARCHAR(20) NOT NULL DEFAULT 'cliente', -- 'admin' ou 'cliente'
  data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ===========================================================
   TABELA: clientes
   -----------------------------------------------------------
   - Usada no módulo de gestão de clientes (clientes/editar.php, clientes/apagar.php)
   - Independente da tabela usuarios (pode guardar clientes externos, etc.)
   =========================================================== */

CREATE TABLE IF NOT EXISTS clientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  telefone VARCHAR(30) DEFAULT NULL,
  data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ===========================================================
   TABELA: produtos
   -----------------------------------------------------------
   - Catálogo de produtos vendidos
   - Usada em produtos.php, produtos/listar.php, adicionar.php, etc.
   =========================================================== */

CREATE TABLE IF NOT EXISTS produtos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(150) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  quantidade INT UNSIGNED NOT NULL DEFAULT 0,
  descricao TEXT DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ===========================================================
   TABELA: vendedores
   -----------------------------------------------------------
   - Cadastro de vendedores/consultores que recebem comissão
   - `codigo` é usado no campo "Código de consultor" na tela de pagamento
   - IDs começam em 101 (VEND-101, VEND-102, ...)
   =========================================================== */

CREATE TABLE IF NOT EXISTS vendedores (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(20) NOT NULL UNIQUE,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) DEFAULT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE vendedores AUTO_INCREMENT = 101;


/* ===========================================================
   TABELA: vendas
   -----------------------------------------------------------
   - Registo de cada venda realizada
   - Liga cliente (usuarios), produto e, opcionalmente, vendedor
   - Armazena também a comissão calculada
   =========================================================== */

CREATE TABLE IF NOT EXISTS vendas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_id INT UNSIGNED NOT NULL,
  produto_id INT UNSIGNED NOT NULL,
  quantidade INT UNSIGNED NOT NULL,
  data_venda DATETIME NOT NULL,
  vendedor_id INT UNSIGNED NULL,
  vendedor_codigo VARCHAR(20) DEFAULT NULL,
  comissao_percentual DECIMAL(5,2) NOT NULL DEFAULT 5.00,
  comissao_valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id),
  KEY idx_cliente (cliente_id),
  KEY idx_produto (produto_id),
  KEY idx_vendedor (vendedor_id),
  CONSTRAINT fk_vendas_cliente  FOREIGN KEY (cliente_id)  REFERENCES usuarios(id),
  CONSTRAINT fk_vendas_produto  FOREIGN KEY (produto_id)  REFERENCES produtos(id),
  CONSTRAINT fk_vendas_vendedor FOREIGN KEY (vendedor_id) REFERENCES vendedores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ===========================================================
   NOTAS
   -----------------------------------------------------------
   - Se já existirem algumas dessas tabelas, o CREATE TABLE IF NOT EXISTS
     simplesmente as ignora (não sobrescreve dados existentes).
   - Para um servidor novo (ex.: InfinityFree), basta:
       1) Criar a base de dados no painel
       2) Selecionar a base no phpMyAdmin
       3) Importar este arquivo schema_alphasolutions.sql
   =========================================================== */

