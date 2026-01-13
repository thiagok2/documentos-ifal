<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratório de Busca - ElasticSearch</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { background-color: #f4f6f9; padding-top: 50px; }
        .card-resultado:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); transition: 0.3s; }
        .destaque-termo { background-color: #fff3cd; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="text-center mb-5">
                <h2><i class="fa fa-search text-primary"></i> Laboratório de Busca</h2>
                <p class="text-muted">Testando integração API Laravel + ElasticSearch</p>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <form id="form-busca">
                        <div class="input-group input-group-lg">
                            <input type="text" id="termo-busca" class="form-control" placeholder="O que você procura? Ex: Edital, Portaria..." autofocus>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-search"></i> Pesquisar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="area-feedback"></div>
            <div id="lista-resultados">
                </div>

        </div>
    </div>
</div>

<script>
    const form = document.getElementById('form-busca');
    const input = document.getElementById('termo-busca');
    const lista = document.getElementById('lista-resultados');
    const feedback = document.getElementById('area-feedback');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Não recarrega a página
        
        const termo = input.value.trim();
        if (!termo) { alert('Digite algo!'); return; }

        // 1. Limpa e mostra "Carregando"
        lista.innerHTML = '';
        feedback.innerHTML = '<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br>Consultando ElasticSearch...</div>';

        // 2. Chama a SUA API
        fetch(`/api/search?q=${termo}`)
            .then(response => response.json())
            .then(data => {
                feedback.innerHTML = ''; // Limpa o carregando

                // Verifica se veio erro do servidor
                if (data.error) {
                    feedback.innerHTML = `<div class="alert alert-danger">Erro na API: ${data.error}</div>`;
                    return;
                }

                // Verifica se não achou nada
                if (!data.resultados || data.resultados.length === 0) {
                    feedback.innerHTML = `<div class="alert alert-warning text-center">Nenhum documento encontrado para "<strong>${termo}</strong>".</div>`;
                    return;
                }

                // Mostra contagem
                feedback.innerHTML = `<p class="text-muted mb-3">Encontramos <strong>${data.total_encontrado}</strong> documentos. Exibindo os top ${data.quantidade_exibida}.</p>`;

                // 3. Monta os Cards
                let html = '';
                data.resultados.forEach(doc => {
                    html += `
                        <div class="card card-resultado mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <a href="${doc.link_original}" target="_blank" style="text-decoration: none;">
                                        ${doc.titulo}
                                    </a>
                                </h5>
                                <div class="mb-2">
                                    <span class="badge badge-info">${doc.tipo || 'Documento'}</span>
                                    <small class="text-muted ml-2"><i class="fa fa-calendar"></i> ${doc.ano || '-'} | Nº ${doc.numero || '-'}</small>
                                </div>
                                <p class="card-text text-secondary" style="font-size: 0.95em;">
                                    ${doc.resumo_conteudo}
                                </p>
                                <a href="${doc.link_original}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    Abrir Arquivo <i class="fa fa-external-link"></i>
                                </a>
                            </div>
                        </div>
                    `;
                });

                lista.innerHTML = html;
            })
            .catch(error => {
                console.error(error);
                feedback.innerHTML = `<div class="alert alert-danger">Falha na conexão. A API está rodando?</div>`;
            });
    });
</script>

</body>
</html>