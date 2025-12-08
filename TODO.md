# TODO

- [x] Implement the site controllers `link`, `optout`, and `tracking` with full business logic, keeping Itemid propagation and CSRF where applicable.
- [x] Ensure existing site routing, menus, and templates pass Itemid, include CSRF tokens on forms, and rely on JRoute/JText/JHtml plus view.html.php metadata setup across available pages.
- [ ] Add additional frontend views/templates as the component grows, ensuring each has a matching `tmpl` XML manifest and translations in all eight site languages.
- [x] Review administrator menu/toolbar entries to keep ACL mappings, sidebar actions, and menu XML in sync for existing views (revisit when adding features).
- [x] Render the administrator sidebar in remaining views so submenu navigation appears consistently across the backend.
- [x] Corrigir guards `_JEXEC` em importarquivo (model/controller) e validar sintaxe PHP do componente inteiro.
- [x] Registrar tracking, sessão, IP e usuário nos fluxos de opt-out, abertura de e-mail e clique de link, incluindo colunas de auditoria nas tabelas de logs.
