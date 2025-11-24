# FX Farma — Site

Landing page estática para apresentação da plataforma FX Farma.

## Visão Geral
- Proposta: plataforma de atendimento e entregas farmacêuticas.
- Perfis: cliente, entregador e balcão.
- Benefícios: pedidos rápidos, acompanhamento em tempo real, segurança (HTTPS), localização precisa.

## Site ao Vivo
- Domínio: `fxfarma.online`.
- Hospedagem: GitHub Pages.
- Publicação: branch `main` (raiz), arquivo `CNAME` configurado.

## Estrutura
- `index.html`: landing principal, com hero, benefícios, como funciona, perfis, vídeo e FAQ.
- `images/`: imagens estáticas usadas no site.
- `lead.php`: endpoint opcional para captura de leads via PHP/SMTP (não usado pelo site atual, que usa FormSubmit).
- `.nojekyll`: desativa Jekyll no GitHub Pages.
- `CNAME`: configura o domínio do site.

## Formulário de Contato
- Backend: FormSubmit.
- Onde editar: `index.html` na seção `#contato`.
- Campos e configuração:
  - `action="https://formsubmit.co/SEU_EMAIL"`
  - `method="POST"`
  - `input type="hidden" name="_subject" value="Contato FX Farma"`
  - `input type="hidden" name="_next" value="https://fxfarma.online/#contato-sucesso"`
- Envio: JavaScript envia `FormData` e respeita `_next` para redirecionar.

## Download do App
- Seção “Baixe o nosso app” com link para APK no Google Drive.
- Onde editar: texto e URL em `index.html` na seção `download-section`.

## Desenvolvimento Local
- Via XAMPP ou PHP embutido:
  - `php -S localhost:8081 -t c:\xampp\htdocs` e abrir `http://localhost:8081/FX%20Farma/index.html`.
- Apenas estático: abra `index.html` no navegador.

## Deploy
- GitHub Pages:
  - Commit na branch `main` publica o site.
  - `CNAME` define `fxfarma.online`.
  - `.nojekyll` garante que arquivos estáticos sejam servidos sem interferência.
- Arquivos grandes:
  - Evite versionar vídeos. `images/*.mp4` está em `.gitignore`.
  - Use Google Drive para vídeos incorporados (iframe).

 

## Suporte
- Dúvidas e melhorias: abra uma issue ou envie contato pelo formulário no site.
