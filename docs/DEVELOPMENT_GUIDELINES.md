# 📘 Documento Técnico — Padrões (Admin-only) para `com_crm_joomla`

> Este documento consolida somente o que usaremos neste projeto (admin-only). **Antes de codar**, alinhe o time com o `DEVELOPMENT_GUIDELINES.md`.

---

## 1) Escopo do componente

- **Joomla 5**  
- **Somente Administrador** (`administrator/`)  
- **Site/** terá apenas controladores mínimos para:
  - `link.acesso` (redirect + tracking de clique)  
  - `tracking.open` (pixel 1×1 de abertura)  
  - `optout.unsubscribe` (descadastro)  

---

## 2) Estrutura de pastas

```
/com_crm_joomla/
├─ administrator/
│  ├─ config/                     # params, esquemas, presets
│  ├─ controllers/                # ex.: LeadsController, CampanhasController
│  ├─ models/                     # Table*, Form*, ModelList, ModelItem
│  ├─ services/                   # integrações (Google, Mailchimp, etc.)
│  │  ├─ import/
│  │  ├─ validate/
│  │  ├─ export/
│  │  └─ shortlinks/
│  ├─ views/                      # MVC Admin (grids, forms)
│  ├─ sql/
│  │  ├─ install.sql              # criação de tabelas
│  │  └─ updates/mysql/           # scripts de migração
│  ├─ helpers/                    # Slug, MetaFetcher, EmailValidator...
│  ├─ language/                   # pt-BR, en-GB
│  ├─ access.xml                  # regras de ACL
│  ├─ config.xml                  # parâmetros do componente
│  └─ com_crm_joomla.php          # entrypoint Admin
├─ site/
│  ├─ controllers/                # link.acesso, tracking.open, optout.unsubscribe
│  └─ router.php                  # rotas dessas ações
├─ media/com_crm_joomla/          # assets Admin (js, css, imgs)
├─ com_crm_joomla.xml             # manifest (instalação)
└─ index.html
```

---

## 3) Padrões de Desenvolvimento

Para detalhes sobre a arquitetura MVC, convenções de nomenclatura, e exemplos de código para este projeto, por favor, consulte o nosso guia de desenvolvimento completo:

**➡️ [Guia de Desenvolvimento Joomla 5 para o Componente CRM](./JOOMLA5_DEVELOPMENT_GUIDE.md)**

Este documento consolidado contém todas as informações necessárias para desenvolver novas funcionalidades, módulos e plugins de forma consistente e alinhada com as melhores práticas do Joomla 5.

---
