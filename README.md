# Dobbin
###### Plataforma de gestão de clientes, roteiros e vendas para agências de turismo.

Com o Dobbin você poderá cadastrar e exlcuir os clientes da sua plataforma, gerenciar seus parceiros, simular roteiros, criar roteiros e entre outros.

## Instalação do Dobbin
##### Requisitos básicos
- PHP: 7.1.*
- MySQL: ^5.4
- Bibliotecas PHP: PDO, ZIP, GD, JSON, OPENSSL, CURL
- eftec/bladeone: ^3.31-stable
- altorouter/altorouter: ^1.2-stable
- cocur/slugify: ^3.2-stable

#### Configuração do Banco de Dados
O Dobbin precisa das seguintes tabelas para funcionar:
- **clientes**: Tabela de dados dos seus clientes.
- **coordenadores**: Tabela de dados dos seus coordenadores.
- **historico_negoc**: Aqui ficarão todas as negociações e informações adicionais dos seus parceiros.
- **lista_bancos**: Uma lista de banco utilizada para cadastro de informações financeiras dos parceiros.
- **log**: Histórico de alterações e operações no Dobbin.
- **login**: Tabela de login dos usuários da plataforma.
- **parc_empresa**: Dados da empresa/parceiro de negócios. Pode ser pessoa jurídica ou física.
- **parc_financeiro**: Informações financeiras do seu parceiro de negócios. É possível cadastrar mais de uma conta bancária por parceiro.
- **parc_servico**: Tipo de serviço prestado pelo parceiro, beneficios inclusos e tarifário.
- **roteiros**: Lista de roteiros, clientes e coordenadores, tarifas do roteiro, despesas, lucro previsto, entre outras informações.

A seguir, os códigos para criação das tabelas usadas pelo Dobbin:

###### clientes
```
CREATE TABLE `clientes` (
 `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
 `nome` varchar(60) NOT NULL,
 `email` varchar(60) NOT NULL,
 `telefone` varchar(60) NOT NULL,
 `rg` varchar(15) NOT NULL,
 `cpf` varchar(15) NOT NULL,
 `nascimento` date NOT NULL,
 `estado_civil` enum('solteiro','casado','separado','divorciado','viuvo') NOT NULL,
 `endereco` varchar(120) NOT NULL COMMENT 'Endereço completo',
 `complemento` varchar(120) NOT NULL COMMENT 'Caso endereço seja insuficiente, continua nesse campo.',
 `ponto_referencia` varchar(120) NOT NULL,
 `bairro` varchar(30) NOT NULL,
 `cep` varchar(8) NOT NULL,
 `cidade` varchar(30) NOT NULL,
 `estado` char(2) NOT NULL,
 `sangue` enum('A+','A-','B+','B-','AB+','AB-','O+','O-','') NOT NULL DEFAULT '' COMMENT 'Tipo sanguíneo',
 `alergia` varchar(255) NOT NULL,
 `emergencia_nome` varchar(60) NOT NULL,
 `emergencia_tel` varchar(30) NOT NULL,
 `taxa_extra_casal` int(11) NOT NULL COMMENT 'Valor em centavos',
 `titular` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT 'Se 0 é um TITULAR; se for DEPENDENTE armazena ID do titular.',
 `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
 `atualizado_em` datetime NOT NULL DEFAULT current_timestamp(),
 `deletado_em` datetime DEFAULT NULL COMMENT 'Soft delete. Exclui o registro depois de 72h.',
 `deletado_por` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Usuário que apagou o cliente.',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4
```

###### coordenadores
```
CREATE TABLE `coordenadores` (
 `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
 `nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `telefone` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `rg` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `cpf` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `nascimento` date NOT NULL,
 `estado_civil` enum('solteiro','casado','separado','divorciado','viuvo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `endereco` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Endereço completo',
 `complemento` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Caso endereço seja insuficiente, continua nesse campo.',
 `ponto_referencia` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `bairro` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `cep` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `cidade` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `estado` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `sangue` enum('A+','A-','B+','B-','AB+','AB-','O+','O-','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Tipo sanguíneo',
 `alergia` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `emergencia_nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `emergencia_tel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
 `atualizado_em` datetime NOT NULL DEFAULT current_timestamp(),
 `deletado_em` datetime DEFAULT NULL COMMENT 'Soft delete. Exclui o registro depois de 72h.',
 `deletado_por` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Usuário que apagou o cliente.',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4
```

###### historico_negoc
```
CREATE TABLE `historico_negoc` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `empresa_id` smallint(5) unsigned NOT NULL,
 `roteiro_id` smallint(5) unsigned NOT NULL,
 `etapa` enum('CONTATO','PEDIDO BLOQUEIO','PAGAMENTO','DESISTÊNCIA','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `detalhes` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `criado_por` smallint(5) unsigned NOT NULL COMMENT 'Usuário que criou registro.(login_id)',
 `criado_em` datetime NOT NULL,
 `atualizado_por` tinyint(3) unsigned DEFAULT NULL COMMENT 'Usuário que editou.',
 `atualizado_em` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COMMENT='Histórico de negociações'
```

###### lista_bancos
```
CREATE TABLE `lista_bancos` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `codigo` char(3) NOT NULL,
 `banco` varchar(100) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='Lista de bancos, de acordo com o Banco Central'
```

###### log
```
CREATE TABLE `log` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `grau` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1) Normal ou baixo; 2) Atenção; 3) Arriscado; 4) Perigoso!',
 `datahora` datetime NOT NULL DEFAULT current_timestamp(),
 `usuario` mediumint(9) NOT NULL COMMENT 'Usuário do sistema; se 0 é o próprio SISTEMA.',
 `evento` varchar(400) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4
```

###### login
```
CREATE TABLE `login` (
 `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
 `nome` varchar(30) NOT NULL,
 `sobrenome` varchar(30) NOT NULL,
 `email` varchar(60) NOT NULL,
 `usuario` varchar(20) NOT NULL,
 `senha` varchar(255) NOT NULL,
 `avatar` varchar(10) NOT NULL DEFAULT 'user00.png' COMMENT 'Nome do arquivo para o avatar',
 `nivel` tinyint(3) unsigned NOT NULL DEFAULT 1,
 `criado_em` datetime NOT NULL,
 `atualizado_em` datetime NOT NULL,
 `logado_em` datetime NOT NULL COMMENT 'Último login.',
 `tentativas` tinyint(4) NOT NULL COMMENT 'Tentativas de login.',
 PRIMARY KEY (`id`),
 UNIQUE KEY `email` (`email`),
 UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4
```

###### parc_empresa
```
CREATE TABLE `parc_empresa` (
 `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
 `razao_social` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Razão social da empresa; se pessoa física, nome completo.',
 `nome_fantasia` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome fantasia da empresa; se pessoa física, é NULO.',
 `doc_tipo` enum('CNPJ','CPF','','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CPF (pessoa) ou CPNJ (empresa)',
 `doc_numero` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número do documento.',
 `endereco` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Endereço completo',
 `cidade` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `estado` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `responsavel` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Responsável ou dono ou contato.',
 `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4
```

###### parc_financeiro
```
CREATE TABLE `parc_financeiro` (
 `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
 `empresa_id` smallint(6) unsigned NOT NULL,
 `banco` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código do Banco (lista fornecida pelo Banco Central)',
 `agencia` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `agencia_dv` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `conta` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `conta_dv` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `tipo_conta` enum('CORRENTE','POUPANÇA','SALÁRIO','DIGITAL','PAGAMENTOS','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de conta.',
 `favorecido` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `obs_financeiro` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Observações sobre setor financeiro.',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4
```

###### parc_servico
```
CREATE TABLE `parc_servico` (
 `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
 `empresa_id` smallint(6) unsigned NOT NULL,
 `categoria` enum('Atração','Guia','Hospedagem','Parceria','Transporte') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `tipo` enum('Hotel','Pousada','Resort','Van','Ônibus Executivo','Ônibus Semi-Leito','Ônibus Leito Total','Escuna','Buggy','Lancha','Avião') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo de serviço. Pode ser NULO.',
 `passageiros` smallint(5) unsigned DEFAULT NULL COMMENT 'Qtd de passageiros. Pode ser NULO',
 `cidade` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pode ser NULO para quem não se aplicar.',
 `estado` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pode ser NULO para quem não se aplicar.',
 `tarifas` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Tarifas do serviço',
 `benef_gratis` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Lista de benefícios gratuitos',
 `benef_pago` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Lista de benefícios pagos.',
 `obs_servico` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Observações sobre o serviço',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4
```

###### roteiros
```
CREATE TABLE `roteiros` (
 `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
 `nome` varchar(60) NOT NULL,
 `data_ini` date NOT NULL,
 `data_fim` date NOT NULL,
 `passagens` smallint(5) unsigned NOT NULL COMMENT 'Quantidade de passagens (clientes). Não inclui coordenadores. Até 9999.',
 `qtd_coordenador` tinyint(3) unsigned NOT NULL COMMENT 'Quantidade de coordenadores. Até 250.',
 `coordenador` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Lista de coordenadores do roteiro.',
 `clientes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON com lista de clientes.',
 `despesas` varchar(2600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Até 20 despesas.',
 `parceiros` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Lista de parceiros nesse roteiro. Até 20',
 `qtd_rateio` smallint(6) NOT NULL COMMENT 'Quantidade clientes para fazer o rateio de despesas.',
 `taxa_lucro` tinyint(3) unsigned NOT NULL COMMENT 'Taxa de lucro. Varia de 0 a 100.',
 `lucro_previsto` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Lucro sobre rateio, lucro sobre poltronas livres, lucro previsto total',
 `tarifa` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON. Até 6 tarifas (cada tarifa 95 caracteres).',
 `reserva_qtd` smallint(6) NOT NULL COMMENT 'Quantidade de poltronas reservadas pelos clientes.',
 `reserva_obs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Informações detalhadas sobre as reservas.',
 `obs` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
 `criado_por` tinyint(3) unsigned NOT NULL COMMENT 'Usuário que criou.',
 `atualizado_em` datetime NOT NULL DEFAULT current_timestamp(),
 `deletado_em` datetime DEFAULT NULL,
 `deletado_por` tinyint(3) unsigned DEFAULT NULL COMMENT 'Usuário que apagou.',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4
```

###### vendas
```
CREATE TABLE `vendas` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID da venda/transação',
 `cliente_id` mediumint(8) unsigned NOT NULL COMMENT 'ID do cliente na plataforma.',
 `roteiro_id` smallint(5) unsigned NOT NULL COMMENT 'ID do roteiro',
 `tarifa_nome` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da tarifa que foi usada.',
 `qtd` tinyint(3) unsigned NOT NULL COMMENT 'Unidades vendidas. Máximo 100.',
 `valor_unitario` mediumint(8) unsigned NOT NULL COMMENT 'Valor para uma unidade vendida. Máximo 9.999.999 centavos.',
 `desconto` mediumint(9) NOT NULL COMMENT 'Desconto por valor unitário. Máximo de 9.999.999 centavos.',
 `desconto_total` mediumint(9) NOT NULL COMMENT 'Desconto total (valor unitario x qtd). Máximo de 9.999.999 centavos.',
 `valor_total` mediumint(9) NOT NULL COMMENT 'Valor total da venda [(valor unitário x qtd) - desconto_total]. Máximo 9.999.999 centavos.',
 `clientes_unitario` smallint(6) NOT NULL COMMENT 'Quantidade de clientes (poltronas) em cada unidade vendida.',
 `clientes_total` smallint(6) NOT NULL COMMENT 'Quantidade total de clientes nessa venda.',
 `status` enum('Aguardando','Paga','Cancelada','Devolvida') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Situação da venda.',
 `data_venda` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Data e hora da venda no sistema.',
 `usuario_id` tinyint(4) NOT NULL COMMENT 'ID do usuário que fez a venda.',
 `data_pagamento` datetime DEFAULT NULL COMMENT 'Data do pagamento (ou data em que a informação foi lançada no sistema)',
 `data_cancelado` datetime DEFAULT NULL COMMENT 'Data em que o pedido foi cancelado na plataforma.',
 `data_estorno` datetime DEFAULT NULL COMMENT 'Data em que o estorno foi realizado.',
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Venda dos roteiros.'
```
