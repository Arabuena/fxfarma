# Move App

Aplicativo de transporte estilo Uber desenvolvido com MERN Stack (MongoDB, Express, React, Node.js).

## 🏷️ Administração de Produtos: Tags e Exibição na Home

Esta documentação descreve como configurar as seções da Home usando tags de produto e como a tag `sem-receita` se relaciona com a flag de receita.

### Sumário rápido
- `tag=destaque`: alimenta a seção "Promoção" (carrossel de banners).
- `tag=finance`: alimenta a seção "Praticidade nas finanças".
- `sem-receita` (tag): força `requiresPrescription=false` no backend ao salvar.
- `bannerFeatured=true`: ainda controla os banners do topo (rotador principal).

### Como configurar no Admin
- Acesse `http://localhost:3012/#/login/admin` → "Produtos".
- Edite tags inline nos cartões ou pelo formulário de edição:
  - Escreva tags separadas por vírgula (ex.: `destaque, dermocosméticos`).
  - As tags são normalizadas e comparadas sem diferenciação de maiúsculas/minúsculas.
- Para aparecer em "Promoção": inclua a tag `destaque`.
- Para aparecer em "Finanças": inclua a tag `finance`.
- Para marcar como "Sem receita": inclua a tag `sem-receita` (o backend ajusta a flag `requiresPrescription` para `false`).
- O topo (rotador principal) continua sendo controlado pelo checkbox "Mostrar no banner do carousel" (`bannerFeatured=true`).

### API e comportamento no backend
- Listagem pública com filtro por tag:
  - `GET /products?active=true&tag=destaque` → usados em "Promoção".
  - `GET /products?active=true&tag=finance` → usados em "Praticidade nas finanças".
- Promoção (frontend):
  - `frontend/src/components/PromoCarousel.js` consulta `GET /products?active=true&tag=destaque`.
  - Fallback: se não houver produtos com `destaque`, são exibidos banners estáticos.
- Finanças (frontend):
  - `frontend/src/components/FinanceCarousel.js` consulta `GET /products?active=true&tag=finance`.
  - Fallback: banners estáticos caso não haja produtos com a tag.
- Sem receita (backend):
  - `backend/src/routes/products.js` ajusta `requiresPrescription=false` automaticamente quando as tags incluem `sem-receita`.
  - Precedência: mesmo que o payload venha com `requiresPrescription=true`, a presença de `sem-receita` força `false`.
  - Observação: remover a tag `sem-receita` não redefine automaticamente `requiresPrescription=true`. Se desejar esse comportamento, pode ser implementado.

### Imagens e cache
- Imagens relativas (`imageUrl`) são convertidas para URLs absolutas com `REACT_APP_API_URL`.
- Versão de cache: é adicionada como `?v=<timestamp>` para evitar exibição de imagem antiga após atualização.

### Solução de problemas
- "Promoção" sem itens: verifique se há produtos ativos com a tag `destaque`. Caso contrário, os banners padrão aparecem.
- "Finanças" sem itens: verifique produtos ativos com a tag `finance`.
- Flag de receita: se um produto aparece como "sem receita" mas a flag não mudou, confirme que a tag salva está exatamente `sem-receita`.
- Ambientes: certifique-se de que `REACT_APP_API_URL` aponta para o backend correto em desenvolvimento/produção.

### Referências de código
- Frontend:
  - `frontend/src/components/PromoCarousel.js` (Promoção por `tag=destaque`).
  - `frontend/src/components/FinanceCarousel.js` (Finanças por `tag=finance`).
  - `frontend/src/pages/pharmacy/ProductsAdmin.js` (edição de produtos, incluindo editor inline de tags).
- Backend:
  - `backend/src/routes/products.js` (filtros por `tag`, criação/atualização e vínculo da tag `sem-receita` à flag `requiresPrescription`).

### Extensões opcionais
- Unificar banners do topo por tag: migrar o rotador principal para `tag=destaque` (substituindo `bannerFeatured=true`).
- Combinar regras de "Promoção": exibir itens com `bannerFeatured=true` OU `tag=destaque` (removendo duplicados).
- Catálogo "Sem receita": criar filtro/aba que liste `requiresPrescription=false` ou `tag=sem-receita`.

## 🛠️ Tecnologias Utilizadas

- **Backend**: 
  - Node.js
  - Express
  - MongoDB
  - Socket.IO para comunicação em tempo real
  - JWT para autenticação

- **Frontend**:
  - React
  - Tailwind CSS
  - Google Maps API
  - Socket.IO Client

## 🔮 Evolução Planejada: Nova Home do Passageiro (Pós-Login)

- Objetivo: substituir a tela inicial do passageiro por uma Home moderna, com saudação, mini-mapa, busca “Para onde vamos?”, destinos recentes, banners informativos e dock de ações — seguindo exatamente o visual da referência.
- Referência visual:
  - A imagem está sendo usada como guia de layout, tipografia e paleta.
  - Pré-visualização no README:
    - ![Home do Passageiro - Preview](frontend/public/images/passenger-home-preview.jpeg)
    - Observação: o arquivo deverá ser adicionado em `frontend/public/images/passenger-home-preview.jpeg`.

### Escopo Visual
- Header roxo com avatar, saudação “Olá, {nome}” e um ícone decorativo; indicador simples de notificações (ponto vermelho).
- Mini-mapa em card com altura aproximada de 200px, exibindo a localização atual e “carros próximos”.
- Card de busca “Para onde vamos?” com campo digitável inline, autocomplete e histórico local.
- Lista de destinos recentes (2–5 itens) clicáveis.
- Carrossel de banners informativos (ex.: “Aproveite a lucratividade…”, “Também aos Finais de Semana — MOVE Pay”) com indicadores de página.
- Recomendações (cards informativos) abaixo dos banners.
- Header desaparece com o scroll para dar foco ao conteúdo.

### Comportamento & Fluxo
- A nova Home substitui a página de entrada do passageiro em `/passenger` (index).
- Selecionar um destino abre uma tela intermediária de estimativa, com o destino pré-preenchido.
- Busca com Google Places Autocomplete; histórico armazenado no `localStorage`.
- Banners com swipe manual e indicadores; CTAs navegam para rotas internas.
- Dock inferior opcional com ações principais (Viagem, Entrega, Pay), mantendo paleta e realce conforme o visual.

### Dados & Integrações
- Avatar e nome do usuário vêm do backend (`user.avatarUrl`, `user.name` via AuthContext).
- Localização via `navigator.geolocation`, com fallback de última posição.
- “Carros próximos” inicialmente simulados ao redor da posição do usuário; evoluir para endpoint dedicado futuramente.
- Banners e recomendações começam estáticos; planejamos API (`/api/passenger/announcements`) para conteúdos dinâmicos e cache leve.

### Considerações de Performance (Android WebView)
- Minimizar re-renders do mini-mapa, componentizando e memoizando.
- Carrossel leve com CSS scroll snap; sem libs pesadas.
- Imagens em SVG/otimizadas; evitar sombras complexas em devices modestos.
- Fallback visual quando mapa não carregar (placeholder com pin).

### Roadmap de Implementação
- Fase 1 (UI estática e fluxo): 
  - Nova página “Home do Passageiro” como índice de `/passenger`.
  - Header, mini-mapa, busca com histórico local, banners estáticos, recentes, e navegação para tela intermediária.
- Fase 2 (dados dinâmicos e integrações):
  - API de anúncios/banners e recomendações.
  - Endpoint de “carros próximos” e notificações via Socket.
- Fase 3 (otimizações e métricas):
  - Telemetria de cliques em banners/dock.
  - Ajustes de performance específicos para Android WebView.

## Deploy no Render

Este projeto já inclui um `render.yaml` na raiz para deploy via Blueprint.

- Serviço: tipo `web` com ambiente `node`.
- Build: `npm run build` na raiz (agora usando `npm ci` para reprodutibilidade).
- Start: `npm run start` inicia o backend e serve o build do frontend.
- Health check: `GET /api/health`.

### Variáveis de ambiente necessárias
- `MONGODB_URI` (obrigatória) — conexão do MongoDB.
- `JWT_SECRET` (obrigatória) — segredo para tokens.
- `REACT_APP_GOOGLE_MAPS_API_KEY` — chave do Google Maps.
- `REACT_APP_API_URL` — normalmente `https://farmaformulaentregagoiania.onrender.com`.
- `FRONTEND_URL` — mesmo domínio do serviço web.
- Opcional: `REACT_APP_ENABLE_PHARMACY_LOGIN` — vazio/habilitado; `false` desabilita.

### Secret File (.env)
Se preferir manter segredos em um arquivo:
- Em Render → Environment → Secret Files → Add Secret File.
- Caminho de montagem: `/opt/render/project/src/.env`.
- O backend lê esse arquivo via `dotenv` em `backend/src/app.js`.
- Observação: para que o build do frontend veja `REACT_APP_*`, também adicione essas variáveis em “Environment Variables”.

### Passo a passo
1. No Render, clique em “New” → “Blueprint” e selecione este repositório.
2. Preencha as variáveis em “Environment Variables” (e opcionalmente crie o Secret File).
3. Faça “Manual Deploy” para executar o build.
4. Valide em `/api/health` e navegue pela UI.

### Dicas
- Use `npm ci` (já configurado) para builds determinísticos.
- Garanta que seu MongoDB permita conexão a partir do Render.
- Em caso de CORS, o backend já permite o domínio `onrender.com` configurado no `FRONTEND_URL`.

### Rotas & Navegação
- Rota de entrada: `/passenger` → Home do Passageiro.
- Placeholders:
  - `/passenger/pay` para CTAs de finanças.
  - `/passenger/delivery` para ação de entregas.
- Fluxo de solicitação de corrida permanece, acionado a partir da Home.

### Status & Próximos Passos
- Design consolidado e alinhado com a referência de imagem.
- Próximo passo: implementação da Home com conteúdo estático e histórico local, seguida de preview para validação visual.

## 📁 Estrutura do Projeto
