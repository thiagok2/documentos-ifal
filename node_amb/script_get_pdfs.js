const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');
const path = require('path');

// Verificar se a URL e o nome da subpasta foram passados como argumentos
const url = process.argv[2];
const subFolderName = process.argv[3];

if (!url || !subFolderName) {
    console.error("Por favor, forneça a URL e o nome da subpasta como argumentos.");
    process.exit(1);
}

// Definir a pasta raiz onde os PDFs serão salvos
const rootFolderName = 'pdfs';
const dirPath = path.join(__dirname, rootFolderName, subFolderName);

// Função para baixar PDFs
async function downloadPDFs(url) {
    try {
        // Fazer uma requisição GET para a URL
        const response = await axios.get(url);
        
        // Carregar o HTML da página com Cheerio
        const $ = cheerio.load(response.data);

        // Criar o diretório se não existir
        if (!fs.existsSync(dirPath)) {
            fs.mkdirSync(dirPath, { recursive: true });
            console.log(`Diretório '${dirPath}' criado com sucesso!`);
        } 

        // Encontrar todos os links para PDFs
        $('a[href$=".pdf"]').each(async (index, element) => {
            // Obter a URL do PDF
            const pdfUrl = $(element).attr('href');

            // Resolver URLs relativas para URLs absolutas
            const pdfAbsoluteUrl = new URL(pdfUrl, url).href;

            // Obter o nome do arquivo
            const pdfName = path.basename(pdfAbsoluteUrl);

            // Fazer o download do PDF
            const pdfResponse = await axios({
                url: pdfAbsoluteUrl,
                method: 'GET',
                responseType: 'stream'
            });

            // Criar um fluxo para salvar o PDF localmente
            const pdfPath = path.join(dirPath, pdfName);
            const writer = fs.createWriteStream(pdfPath);

            pdfResponse.data.pipe(writer);

            writer.on('finish', () => {
                console.log(`Baixado: ${pdfName}`);
            });

            writer.on('error', (err) => {
                console.error(`Erro ao salvar o arquivo ${pdfName}:`, err);
            });
        });

    } catch (error) {
        console.error("Erro ao baixar PDFs:", error);
    }
}

// Chamar a função para baixar os PDFs
downloadPDFs(url);
