# 🤝 Guia de Contribuição - Disparador WhatsApp

Obrigado por considerar contribuir para o projeto Disparador WhatsApp! Este guia irá ajudá-lo a contribuir de forma efetiva.

## 📋 Índice

- [Como Contribuir](#como-contribuir)
- [Padrões de Código](#padrões-de-código)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Executando Testes](#executando-testes)
- [Processo de Pull Request](#processo-de-pull-request)
- [Reportando Bugs](#reportando-bugs)

## 🚀 Como Contribuir

### 1. Fork e Clone

```bash
# Fork o repositório no GitHub
# Clone seu fork
git clone https://github.com/SEU-USUARIO/disparador.git
cd disparador

# Adicione o repositório original como upstream
git remote add upstream https://github.com/emer5om/disparador.git
```

### 2. Configuração do Ambiente

```bash
# Instale dependências
composer install
npm install

# Configure ambiente
cp .env.example .env
php artisan key:generate

# Execute migrations
php artisan migrate
```

### 3. Crie uma Branch

```bash
# Sempre crie uma branch para sua feature
git checkout -b feature/minha-nova-feature

# Ou para bug fixes
git checkout -b bugfix/corrige-problema-x
```

## 📝 Padrões de Código

### PHP (Laravel)

- Siga as [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use type hints sempre que possível
- Escreva PHPDoc para métodos públicos
- Use nomes descritivos para variáveis e métodos

```php
<?php

/**
 * Disparador WhatsApp - Sistema de Disparo de Mensagens
 * 
 * @package DisparadorWhatsApp
 * @author Emerson <https://github.com/emer5om>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/emer5om/disparador
 */

namespace App\Services;

class ExemploService
{
    /**
     * Processa a campanha de mensagens
     *
     * @param Campaign $campaign
     * @return array
     */
    public function processarCampanha(Campaign $campaign): array
    {
        // Implementação
    }
}
```

### Frontend (Blade/CSS/JS)

- Use TailwindCSS para estilização
- Mantenha componentes Livewire pequenos e focados
- Use Alpine.js para interações simples
- Comente código JavaScript complexo

### Banco de Dados

- Use migrations para todas as mudanças no schema
- Crie seeders para dados de exemplo
- Use foreign keys e índices apropriados

## 🏗️ Estrutura do Projeto

```
disparador/
├── app/
│   ├── Http/Controllers/Admin/    # Controllers administrativos
│   ├── Models/                   # Models Eloquent
│   ├── Services/                # Services para lógica de negócio
│   ├── Jobs/                    # Jobs para filas
│   └── Livewire/               # Componentes Livewire
├── resources/
│   ├── views/admin/            # Views administrativas
│   ├── css/                    # Arquivos CSS
│   └── js/                     # Arquivos JavaScript
├── database/
│   ├── migrations/             # Migrations
│   └── seeders/               # Seeders
└── tests/                     # Testes automatizados
```

## 🧪 Executando Testes

```bash
# Execute todos os testes
php artisan test

# Execute testes específicos
php artisan test --filter=CampaignTest

# Execute com coverage
php artisan test --coverage
```

### Escrevendo Testes

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class CampaignTest extends TestCase
{
    public function test_pode_criar_campanha(): void
    {
        $response = $this->post('/admin/campaigns', [
            'title' => 'Campanha Teste',
            'message' => 'Mensagem de teste',
            'instance_id' => 1,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('campaigns', [
            'title' => 'Campanha Teste'
        ]);
    }
}
```

## 📤 Processo de Pull Request

### 1. Antes de Criar o PR

```bash
# Certifique-se que está atualizado
git fetch upstream
git checkout main
git merge upstream/main

# Rebase sua branch
git checkout sua-branch
git rebase main
```

### 2. Checklist do PR

- [ ] Código segue os padrões estabelecidos
- [ ] Testes foram escritos/atualizados
- [ ] Documentação foi atualizada se necessário
- [ ] Commits têm mensagens descritivas
- [ ] Não há conflitos com a branch main

### 3. Template do PR

```markdown
## 📝 Descrição
Breve descrição das mudanças implementadas.

## 🔄 Tipo de Mudança
- [ ] Bug fix
- [ ] Nova feature
- [ ] Breaking change
- [ ] Documentação

## 🧪 Testes
- [ ] Testes unitários passaram
- [ ] Testes de integração passaram
- [ ] Testado manualmente

## 📋 Checklist
- [ ] Código revisado
- [ ] Documentação atualizada
- [ ] Testes adicionados/atualizados
```

## 🐛 Reportando Bugs

### Template de Issue para Bugs

```markdown
## 🐛 Descrição do Bug
Descrição clara e concisa do bug.

## 🔄 Passos para Reproduzir
1. Vá para '...'
2. Clique em '....'
3. Role até '....'
4. Veja o erro

## ✅ Comportamento Esperado
Descrição do que deveria acontecer.

## 📱 Ambiente
- SO: [ex: macOS 12.0]
- Browser: [ex: Chrome 95]
- PHP: [ex: 8.1]
- Laravel: [ex: 9.0]

## 📋 Logs/Screenshots
Se aplicável, adicione logs de erro ou screenshots.
```

## 🏷️ Convenções de Commit

Use [Conventional Commits](https://www.conventionalcommits.org/):

```bash
# Features
git commit -m "feat: adiciona sistema de agendamento de campanhas"

# Bug fixes
git commit -m "fix: corrige erro de validação nos botões da campanha"

# Documentação
git commit -m "docs: atualiza README com instruções de instalação"

# Refatoração
git commit -m "refactor: otimiza queries do relatório de campanhas"
```

## 📞 Contato

- **GitHub Issues**: Para bugs e features
- **GitHub Discussions**: Para perguntas gerais
- **Author**: [@emer5om](https://github.com/emer5om)

---

**Obrigado por contribuir! 🙏**