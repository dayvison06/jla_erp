# JLA ERP

### 🛠️ Ferramentas Utilizadas

- [Laravel](https://laravel.com/) - Framework PHP
- [Vue.js](https://vuejs.org/) - Framework JavaScript
- [Inertia.js](https://inertiajs.com/) - Framework ponte de comunicação entre Laravel e Vue
- [Tailwind CSS](https://tailwindcss.com/) - Framework CSS
- [PostgreSQL](https://www.postgresql.org/) - Banco de Dados
- [Lucide Icons](https://lucide.dev/) - Ícones
- [Shadcn UI](https://shadcn-vue.com/) - Componentes UI

### 🗄️ Modelo de Banco Inicial

Visando uma estrutura inicial para gerenciamento de Usuários, Times, Cargos e Permissões.

 > 💡 **Instale a extensão Mermaid para visualizar o diagrama ou veja na web.**

``` mermaid
erDiagram
users ||--o{ user_roles : has
roles ||--o{ user_roles : assigned_to
roles ||--o{ role_permissions : has
permissions ||--o{ role_permissions : assigned_to
users ||--o{ user_permissions : overrides
permissions ||--o{ user_permissions : defined_for
users }o--|| teams : belongs_to

    users {
        int id PK
        string name
        string email
        string password_hash
        int team_id FK
        datetime created_at
        datetime updated_at
    }

    teams {
        int id PK
        string name
        string description
    }

    roles {
        int id PK
        string name
        string description
    }

    permissions {
        int id PK
        string name
        string description
    }

    user_roles {
        int id PK
        int user_id FK
        int role_id FK
        datetime created_at
        datetime updated_at
    }

    role_permissions {
        int id PK
        int role_id FK
        int permission_id FK
        datetime created_at
        datetime updated_at
    }

    user_permissions {
        int id PK
        int user_id FK
        int permission_id FK
        boolean granted
        datetime created_at
        datetime updated_at
    }
```



