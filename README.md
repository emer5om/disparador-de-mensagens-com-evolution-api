# 📱 Disparador WhatsApp

> Sistema completo para disparo de mensagens WhatsApp em massa com interface web moderna

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.3+-blue?style=for-the-badge&logo=php" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/WhatsApp-API-25D366?style=for-the-badge&logo=whatsapp" alt="WhatsApp API">
</p>

## 🚀 Sobre o Projeto

O **Disparador WhatsApp** é uma plataforma web robusta desenvolvida em Laravel para gerenciar campanhas de disparo de mensagens WhatsApp em massa. O sistema oferece uma interface intuitiva para criar, gerenciar e monitorar campanhas de marketing via WhatsApp, com recursos avançados de controle e relatórios detalhados.

### 🎯 Principais Funcionalidades

#### 📊 **Dashboard Administrativo**
- Visão geral completa das campanhas
- Estatísticas em tempo real
- Gráficos de performance
- Monitoramento de instâncias WhatsApp

#### 📱 **Gerenciamento de Instâncias WhatsApp**
- Conexão múltipla de contas WhatsApp
- Status de conexão em tempo real
- Configuração via QR Code
- Monitoramento de saúde das instâncias

#### 🎯 **Sistema de Campanhas**
- **Criação de campanhas** com interface intuitiva
- **Tipos de mensagem suportados:**
  - Mensagens de texto simples
  - Mensagens com botões interativos
  - Mensagens com listas de opções
  - Enquetes (polls)
  - Botões com URL
- **Controles de campanha:**
  - Iniciar/Pausar/Retomar/Parar campanhas
  - Duplicação de campanhas
  - Edição de campanhas em rascunho
- **Agendamento** de campanhas
- **Delay configurável** entre mensagens
- **Preview** de mensagens antes do envio

#### 📋 **Gerenciamento de Leads**
- Importação de listas de contatos
- Validação automática de números
- Categorização de leads
- Histórico de interações
- **Formatos de importação suportados:**
  - CSV
  - TXT (um número por linha)
  - Colar texto diretamente

#### 📈 **Relatórios e Analytics**
- **Métricas detalhadas:**
  - Taxa de entrega
  - Taxa de sucesso
  - Mensagens enviadas/falhadas
  - Tempo de execução
- **Relatórios por período**
- **Exportação de dados**
- **Campanhas com melhor performance**

#### ⚙️ **Sistema de Filas e Jobs**
- Processamento assíncrono de mensagens
- Sistema de filas Redis/Database
- Controle de velocidade de envio
- Retry automático para falhas
- Logs detalhados de execução

#### 🔧 **Recursos Técnicos**
- **Livewire** para componentes reativos
- **Real-time updates** via WebSockets
- **API RESTful** para integrações
- **Sistema de cache** para performance
- **Logs estruturados** para debugging
- **Backup automático** de dados

## 🛠️ Tecnologias Utilizadas

- **Backend:** Laravel 12.x, PHP 8.3+
- **Frontend:** TailwindCSS, AlpineJS, Livewire
- **Database:** MySQL/PostgreSQL
- **Cache:** Redis
- **Queues:** Redis/Database
- **WhatsApp API:** Evolution API
- **Monitoramento:** Laravel Telescope (desenvolvimento)

## 📋 Pré-requisitos

- PHP 8.3 ou superior
- Composer
- Node.js e NPM
- MySQL/PostgreSQL
- Redis (opcional, mas recomendado)
- Evolution API configurada

## ⚡ Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/emer5om/disparador.git
cd disparador
```

### 2. Instale as dependências
```bash
# Dependências PHP
composer install

# Dependências Node.js
npm install
```

### 3. Configure o ambiente
```bash
# Copie o arquivo de configuração
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

### 4. Configure o banco de dados
```bash
# Execute as migrations
php artisan migrate

# Execute os seeders (opcional)
php artisan db:seed
```

### 5. Compile os assets
```bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

### 6. Configure as filas
```bash
# Inicie o worker das filas
php artisan queue:work
```

## ⚙️ Configuração

### Configurações Principais (.env)

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=disparador
DB_USERNAME=root
DB_PASSWORD=

# Redis (Cache e Filas)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Evolution API
EVOLUTION_API_URL=http://localhost:8080
EVOLUTION_API_KEY=your-api-key

# Filas
QUEUE_CONNECTION=redis
```

## 🚀 Como Usar

### 1. **Configurar Instância WhatsApp**
- Acesse `/admin/instances`
- Clique em "Nova Instância"
- Escaneie o QR Code com WhatsApp
- Aguarde a conexão ser estabelecida

### 2. **Criar Lista de Leads**
- Acesse `/admin/leads`
- Importe seus contatos via CSV ou colagem de texto
- Aguarde a validação automática dos números

### 3. **Criar Campanha**
- Acesse `/admin/campaigns`
- Clique em "Nova Campanha"
- Configure mensagem, botões e delays
- Selecione a lista de leads ou adicione números manualmente

### 4. **Executar Campanha**
- Na lista de campanhas, clique em "Iniciar"
- Monitore o progresso em tempo real
- Use controles para pausar/retomar conforme necessário

### 5. **Analisar Resultados**
- Acesse `/admin/reports` para relatórios detalhados
- Visualize métricas de performance
- Exporte dados para análise externa

## 📊 Estrutura do Banco de Dados

### Principais Tabelas
- `campaigns` - Campanhas de disparo
- `messages` - Mensagens individuais da campanha
- `instances` - Instâncias WhatsApp conectadas
- `lead_lists` - Listas de leads importadas
- `leads` - Contatos individuais
- `message_logs` - Logs detalhados de envio

## 🔒 Segurança

- Validação de entrada em todas as rotas
- CSRF protection habilitado
- Rate limiting nas APIs
- Logs de auditoria
- Sanitização de dados de entrada

## 🤝 Contribuição

Contribuições são sempre bem-vindas! Para contribuir:

1. Faça um Fork do projeto
2. Crie sua Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a Branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Autor

**Emerson** - [@emer5om](https://github.com/emer5om)

## 📞 Suporte

Para suporte e dúvidas:
- Abra uma [Issue](https://github.com/emer5om/disparador/issues)
- Entre em contato via GitHub

---

<p align="center">
  Desenvolvido com ❤️ usando Laravel
</p># disparador-de-mensagens-com-evolution-api
