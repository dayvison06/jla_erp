# Guia de Estilo de Componentes

## Paleta de Cores
- **Primária:** #222931 (azul petróleo escuro)
- **Secundária:** #cc2127 (vermelho marcante)
- **Texto Principal:** #222931
- **Texto Secundário:** #555
- **Fundo:** #f7f8fa
- **Borda:** #e0e3e8
- **Sucesso:** #2e7d32
- **Aviso:** #f9a825
- **Erro:** #cc2127
- **Info:** #1976d2

## Botões
- **Primário:**
  - Fundo: #222931
  - Texto: #fff
  - Hover: #1a2026
  - Borda: none
  - Raio: 6px
  - Padding: 0.75rem 1.5rem
  - Peso da fonte: 600
  - Sombra leve ao passar o mouse
- **Secundário:**
  - Fundo: #cc2127
  - Texto: #fff
  - Hover: #a81a1f
  - Borda: none
  - Raio: 6px
  - Padding: 0.75rem 1.5rem
  - Peso da fonte: 600
- **Desabilitado:**
  - Fundo: #e0e3e8
  - Texto: #aaa
  - Cursor: not-allowed

## Cards
- **Fundo:** #fff
- **Borda:** 1px sólida #e0e3e8
- **Raio:** 10px
- **Sombra:** 0 2px 8px rgba(34,41,49,0.04)
- **Padding:** 2rem
- **Título:**
  - Cor: #222931
  - Peso: 700
  - Tamanho: 1.25rem
- **Subtítulo:**
  - Cor: #555
  - Peso: 500
  - Tamanho: 1rem

## Inputs e Selects
- **Fundo:** #fff
- **Borda:** 1px sólida #e0e3e8
- **Raio:** 6px
- **Padding:** 0.75rem 1rem
- **Foco:**
  - Borda: 1.5px sólida #cc2127
  - Sombra: 0 0 0 2px rgba(204,33,39,0.08)

## Feedbacks
- **Sucesso:**
  - Fundo: #e8f5e9
  - Texto: #2e7d32
- **Aviso:**
  - Fundo: #fffde7
  - Texto: #f9a825
- **Erro:**
  - Fundo: #ffebee
  - Texto: #cc2127
- **Info:**
  - Fundo: #e3f2fd
  - Texto: #1976d2

## Tabelas
- **Cabeçalho:**
  - Fundo: #222931
  - Texto: #fff
  - Peso: 600
- **Linhas Alternadas:**
  - Fundo: #f7f8fa
- **Borda:** #e0e3e8

## Acessibilidade
- Garantir contraste mínimo de 4.5:1 para texto.
- Foco visível em todos os elementos interativos.
- Tamanho mínimo de fonte: 16px.
- Espaçamento mínimo entre elementos: 8px.

## Exemplo de CSS
```css
.button-primary {
  background: #222931;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  transition: background 0.2s;
}
.button-primary:hover {
  background: #1a2026;
}
.button-secondary {
  background: #cc2127;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  transition: background 0.2s;
}
.button-secondary:hover {
  background: #a81a1f;
}
.card {
  background: #fff;
  border: 1px solid #e0e3e8;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(34,41,49,0.04);
  padding: 2rem;
}
```

---

Siga este guia para garantir harmonia visual, acessibilidade e consistência em todos os componentes da aplicação.
