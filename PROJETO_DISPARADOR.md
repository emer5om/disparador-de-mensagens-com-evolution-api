# Sistema de Disparo de Mensagens - Evolution API

## Visão Geral
Sistema completo de disparo de mensagens personalizadas integrado com a Evolution API, com painel administrativo responsivo para gerenciar campanhas e instâncias do WhatsApp usando Laravel 12 e Blade.

## Funcionalidades Principais

### 1. Painel Administrativo
- Dashboard com relatórios em tempo real
- Interface responsiva com Tailwind CSS
- Sidebar de navegação
- Navbar com informações do usuário

### 2. Dashboard de Relatórios
- **Enviados com Sucesso**: Contador e lista de mensagens entregues
- **Envios Falhados**: Log de erros e tentativas falhadas
- **Total de Envios**: Estatísticas gerais
- **Gráficos de Performance**: Visualização temporal dos envios

### 3. Gerenciamento de Campanhas
- **Criar Campanhas**: 
  - Título da campanha
  - Mensagem personalizada
  - Botões de ação com links
  - Seleção de instância para envio
- **Listar Campanhas**: Visualização e edição de campanhas existentes
- **Status das Campanhas**: Ativa, pausada, finalizada

### 4. Gerenciamento de Instâncias
- **Criar Instância**: Geração automática de instância na Evolution API
- **QR Code**: Link direto para conectar WhatsApp
- **Status da Conexão**: Monitor em tempo real
- **Gerenciar Instâncias**: Listar, editar, deletar

## Estrutura do Projeto Laravel

```
disparador/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── CampaignController.php
│   │   │   ├── InstanceController.php
│   │   │   └── ReportController.php
│   │   └── Livewire/
│   │       ├── Dashboard/
│   │       │   ├── StatsCards.php
│   │       │   ├── ReportsChart.php
│   │       │   └── RecentActivity.php
│   │       ├── Campaigns/
│   │       │   ├── CampaignForm.php
│   │       │   ├── CampaignList.php
│   │       │   └── CampaignManager.php
│   │       └── Instances/
│   │           ├── InstanceForm.php
│   │           ├── InstanceList.php
│   │           └── QRCodeDisplay.php
│   ├── Models/
│   │   ├── Campaign.php
│   │   ├── Instance.php
│   │   ├── Message.php
│   │   └── MessageLog.php
│   ├── Services/
│   │   ├── EvolutionApiService.php
│   │   ├── CampaignService.php
│   │   ├── MessageDispatchService.php
│   │   └── InstanceService.php
│   └── Jobs/
│       ├── SendMessageJob.php
│       └── ProcessCampaignJob.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   └── navbar.blade.php
│   │   ├── dashboard/
│   │   │   └── index.blade.php
│   │   ├── campaigns/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── instances/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── qrcode.blade.php
│   │   └── livewire/
│   │       ├── dashboard/
│   │       ├── campaigns/
│   │       └── instances/
│   └── css/
│       └── app.css
├── database/
│   ├── migrations/
│   │   ├── create_instances_table.php
│   │   ├── create_campaigns_table.php
│   │   ├── create_messages_table.php
│   │   └── create_message_logs_table.php
│   └── seeders/
├── routes/
│   ├── web.php
│   └── api.php
└── config/
    └── evolution.php
```

## Models e Database

### Instances Table
```sql
- id (bigint, primary key)
- name (string)
- instance_key (string, unique)
- evolution_api_url (string)
- status (enum: 'disconnected', 'connecting', 'connected')
- qr_code (text, nullable)
- webhook_url (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Campaigns Table
```sql
- id (bigint, primary key)
- title (string)
- message (text)
- buttons (json, nullable)
- instance_id (bigint, foreign key)
- status (enum: 'draft', 'active', 'paused', 'completed')
- scheduled_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Messages Table
```sql
- id (bigint, primary key)
- campaign_id (bigint, foreign key)
- phone_number (string)
- message_content (text)
- buttons (json, nullable)
- status (enum: 'pending', 'sent', 'delivered', 'failed')
- sent_at (timestamp, nullable)
- delivered_at (timestamp, nullable)
- error_message (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Message Logs Table
```sql
- id (bigint, primary key)
- message_id (bigint, foreign key)
- status (string)
- response (json)
- error_message (text, nullable)
- created_at (timestamp)
```

## Fases de Desenvolvimento

### Fase 1: Setup Laravel e Layout Base
- [ ] Criar projeto Laravel 12
- [ ] Configurar Tailwind CSS e Livewire
- [ ] Criar migrations e models
- [ ] Layout responsivo (Sidebar + Navbar) com Blade
- [ ] Roteamento básico

### Fase 2: Dashboard e Relatórios
- [ ] Controller e view do Dashboard
- [ ] Livewire components para estatísticas
- [ ] Gráficos com Chart.js
- [ ] Lista de atividades recentes

### Fase 3: Gerenciamento de Instâncias
- [ ] CRUD de instâncias
- [ ] Integração com Evolution API
- [ ] Geração e exibição de QR Code
- [ ] Monitor de status com Livewire

### Fase 4: Sistema de Campanhas
- [ ] CRUD de campanhas
- [ ] Editor de mensagens com botões
- [ ] Livewire components para gerenciamento
- [ ] Validação e preview de campanhas

### Fase 5: Sistema de Disparo
- [ ] Jobs para disparo de mensagens
- [ ] Queue system
- [ ] Log de envios e webhooks
- [ ] Retry automático com exponential backoff

### Fase 6: Integrações e Otimizações
- [ ] Webhooks da Evolution API
- [ ] Relatórios avançados com exports
- [ ] Notificações em tempo real
- [ ] Cache e otimizações

## Tecnologias

- **Backend**: Laravel 12
- **Frontend**: Blade Templates + Livewire + Alpine.js
- **CSS**: Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Queue**: Redis/Database
- **Cache**: Redis
- **Charts**: Chart.js
- **QR Code**: SimpleSoftwareIO/simple-qrcode
- **HTTP Client**: Guzzle
- **Icons**: Heroicons

## Configuração da Evolution API

### Arquivo config/evolution.php:
```php
return [
    'base_url' => env('EVOLUTION_API_URL', 'http://localhost:8080'),
    'api_key' => env('EVOLUTION_API_KEY'),
    'webhook_url' => env('APP_URL') . '/api/webhooks/evolution',
];
```

### Endpoints Principais:
- `POST /instance/create` - Criar instância
- `GET /instance/qrcode/{instance}` - Obter QR Code
- `POST /message/sendText` - Enviar mensagem de texto
- `POST /message/sendButtons` - Enviar mensagem com botões
- `GET /instance/status/{instance}` - Status da instância

## Próximos Passos

1. **Fase 1**: Setup do Laravel e estrutura base
2. **Configurar ambiente de desenvolvimento**
3. **Implementar models e migrations**
4. **Criar layout responsivo com Blade**

---

## Comandos Artisan Personalizados

```bash
# Instalar dependências
composer require livewire/livewire
composer require simplesoftwareio/simple-qrcode
npm install -D tailwindcss @tailwindcss/forms

# Criar components Livewire
php artisan make:livewire Dashboard/StatsCards
php artisan make:livewire Campaigns/CampaignForm
php artisan make:livewire Instances/InstanceList

# Executar migrations
php artisan migrate

# Executar queues
php artisan queue:work
```

## Notas de Desenvolvimento

- Usar Livewire para interatividade sem JavaScript complexo
- Implementar middleware para autenticação
- Usar Form Requests para validação
- Implementar rate limiting para API calls
- Jobs assíncronos para melhor performance
- Logs detalhados para debugging