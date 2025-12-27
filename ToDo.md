# ToDo.md

# Tarefas Atuais

- Remover Status Input
    - Deixar como badge em baixo do nome
- Remover Data de desligamento
    - Só será visível se o status for "desligado"
- Validar salvamento de Anexos
    - Verificar se o arquivo foi realmente salvo no backend
    - Melhoria de load e informação para o usuário


## Plano Detalhado para Tornar o Frontend de Funcionários Totalmente Funcional

### 1. Listagem e Busca de Funcionários
- [ ] Implementar busca reativa por nome, CPF, cargo, departamento e status, filtrando no backend.
  - Adicionar lógica para enviar os filtros para o backend via Inertia.
  - Exibir feedback de carregamento durante a busca.
- [ ] Implementar paginação real baseada nos dados do backend.
  - Usar os dados de paginação retornados pela API (links, meta, etc).
  - Exibir controles de página e atualizar a lista ao navegar.

### 2. Cadastro e Edição de Funcionários
- [] Adicionar validação visual e feedback de erro para todos os campos obrigatórios.
  - Bloquear envio se houver campos obrigatórios não preenchidos.
- [x] Exibir mensagens de sucesso/erro após operações.
  - Usar o sistema de toast para feedback ao usuário.
- [ ] Garantir atualização automática da lista após criar/editar.
  - Chamar loadEmployees() após sucesso.
- [ ] Refatorar lógica do formulário para componente próprio.
  - Criar um componente EmployeeForm.vue para reutilização.

### 3. Upload e Gerenciamento de Anexos
- [ ] Exibir progresso real do upload.
  - Atualizar barra de progresso durante upload.
- [ ] Melhorar preview de anexos (PDF, imagens, etc).
  - Exibir miniaturas e links para download/visualização.
- [ ] Permitir exclusão de anexos já enviados.
  - Adicionar botão de exclusão e lógica para remover do backend.
- [ ] Validar tamanho e tipo de arquivo antes do upload.
  - Exibir mensagem de erro se o arquivo não for permitido.

### 4. Dependentes
- [ ] Validar campos obrigatórios dos dependentes.
  - Exibir erros e impedir envio se faltar dados.
- [ ] Permitir edição de dependentes já cadastrados.
  - Adicionar lógica para editar e salvar dependentes.
  - Adicionar Campo de RG e Estado Civil.
  - 

### 5. Histórico de Cargos
- [ ] Finalizar CRUD do histórico de cargos.
  - Permitir adicionar, editar e remover cargos.
  - Exibir histórico na tela principal.
- [ ] Garantir consistência dos dados ao salvar histórico.
  - Validar datas e campos obrigatórios.

### 6. Importação e Exportação
- [ ] Finalizar/implementar importação em massa de funcionários via CSV/Excel.
  - Adicionar botão e lógica para upload/importação.
- [ ] Permitir exportação de dados filtrados.
  - Gerar CSV apenas com dados filtrados.

### 7. Acessibilidade e UX
- [ ] Garantir contraste adequado e navegação por teclado.
  - Revisar cores e navegação tab.
- [ ] Adicionar ARIA nos componentes interativos.
  - Melhorar acessibilidade para leitores de tela.
- [ ] Revisar responsividade em todos os formulários e tabelas.
  - Testar em diferentes tamanhos de tela.

### 8. Permissões e Segurança
- [ ] Exibir/ocultar ações conforme permissões do usuário logado.
  - Esconder botões de edição/exclusão para usuários sem permissão.
- [ ] Validar permissões no backend e frontend.
  - Garantir segurança dos endpoints.

### 9. Integração com Inertia.js
- [ ] Usar partial reload para otimizar carregamento após ações.
  - Utilizar a opção `only` do Inertia para recarregar apenas o necessário.
- [ ] Garantir que, após criar/editar, a lista seja recarregada e o usuário veja os dados atualizados.
- [ ] No update, retornar apenas os dados alterados, se possível, para otimizar tráfego.

### 10. Outras Melhorias
- [ ] Limpar cache/localStorage ao sair do formulário ou após sucesso.
- [ ] Adicionar testes unitários e de integração para os principais fluxos.
- [ ] Refatorar código para componentes menores e reutilizáveis.
- [ ] Preparar para internacionalização (i18n), se necessário.
- [ ] Adicionar dashboard de métricas rápidas (ativos, desligados, férias, etc).
- [ ] Exibir logs de auditoria/histórico de alterações.
- [ ] Integrar notificações para avisos de exames, vencimentos, etc.

### 11. Implementação de atribuição de Cargos
- [ ] Adicionar funcionalidade para atribuir múltiplos cargos a um funcionário.
- [ ] Permitir definir datas de início e término para cada cargo atribuído.
- [ ] Validar que não haja sobreposição de datas entre cargos atribuídos ao mesmo funcionário.
- [ ] Exibir lista de cargos atribuídos na visualização do funcionário, com opções para editar ou remover cada cargo.

---

## Observações
- Validar regras de negócio (ex: só permitir data de desligamento se status for "desligado").
- Garantir feedback visual claro para o usuário em todos os fluxos.
- Manter código limpo, comentado e organizado para facilitar manutenção.

---

Este checklist pode ser atualizado conforme novas necessidades ou ajustes no escopo do projeto.

