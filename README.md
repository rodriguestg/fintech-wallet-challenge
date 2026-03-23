# Carteira Digital P2P - Desafio Técnico Sênior

MVP de uma carteira digital P2P onde usuários podem enviar e receber dinheiro de forma segura. Desenvolvido com Laravel 10 (Backend) e Vue.js 3 (Frontend).

## Links de Deploy

- **Frontend (Aplicação):** [https://fintech-wallet-challenge.vercel.app/]
- **Backend (API):** [https://fintech-wallet-challenge-production-9186.up.railway.app/]

### Credenciais para Teste
- **E-mail:** user1@example.com
- **Senha:** password

- **E-mail:** user2@example.com
- **Senha:** password

- **E-mail:** user3@example.com
- **Senha:** password

---

## Decisões Técnicas e Arquitetura

Como Desenvolvedor, priorizei a simplicidade, segurança e a manutenção do código, evitando over-engineering:

### Backend (Laravel)
- **Service Layer Pattern:** A lógica de transferência foi isolada no `TransferService`, mantendo o `TransferController` limpo e responsável apenas por orquestrar a requisição HTTP.
- **Transações Atômicas (DB::transaction):** Implementado na transferência para garantir que débito e crédito ocorram juntos. Se houver falha, ocorre o rollback automático, evitando inconsistências financeiras.
- **Form Requests:** Utilizados para validação de dados de entrada (`TransferRequest`), garantindo fail-fast antes da lógica de negócio.
- **API Resources:** Utilizado `TransactionResource` para padronizar o payload de resposta da API e ocultar dados sensíveis do banco.
- **Autenticação:** Implementada com Laravel Sanctum.

### Frontend (Vue.js 3)
- **Composition API:** Utilizada para melhor organização e reuso de lógica nos componentes.
- **Gerenciamento de Estado (Pinia):** Criação do `authStore` para centralizar dados do usuário, saldo da carteira e token de autenticação, evitando prop-drilling.
- **Axios Interceptors:** Configurados para injetar automaticamente o token Bearer nas requisições e tratar erros globais (ex: redirecionar para login no erro 401).
- **Feedback Visual:** Implementação de estados de `loading` em botões e exibição de saldo para evitar cliques duplos e melhorar a UX.

---

## Como rodar o projeto localmente

### Pré-requisitos
- Docker e Docker Compose (Recomendado)
- Node.js 20+ (Para rodar o frontend localmente)

### Passo a Passo - Backend (API)

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/fintech-wallet-challenge.git
cd fintech-wallet-challenge

Configure as variáveis de ambiente:

cp backend/.env.example backend/.env

Suba os containers do Docker (Laravel + MySQL):

docker-compose up -d --build

Instale as dependências e gere a chave da aplicação:

docker-compose exec app composer install
docker-compose exec app php artisan key:generate

Rode as migrations e popule o banco de dados:

docker-compose exec app php artisan migrate:refresh --seed

A API estará rodando em http://localhost:8000

Passo a Passo - Frontend (Vue.js)

Acesse a pasta do frontend:

cd frontend

Instale as dependências:

npm install

Inicie o servidor de desenvolvimento:

npm run dev

A aplicação estará rodando em http://localhost:5173

```

## Testes Automatizados

O backend possui testes de Feature cobrindo as regras de negócio (Transferências, Autenticação e Filtros de Histórico).

Para rodar os testes localmente via Docker:

docker-compose exec app php artisan test


---