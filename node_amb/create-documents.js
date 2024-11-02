const axios = require('axios');
const { randomUUID } = require('crypto');
const fs = require('fs');
const path = require('path');

function urlize(filename) {
    return filename
        .replace(/\..+$/, '')
        .replace(/[^a-zA-Z0-9]/g, '-')
        .toLowerCase();
}

function encodePDFToBase64(filePath) {
    const fileBuffer = fs.readFileSync(filePath);
    return fileBuffer.toString('base64');
}

function createDocument(filePath, atoDocumento) {
    const base64Content = encodePDFToBase64(filePath);
    return {
        ato: atoDocumento,
        data: base64Content,
        attachment: {
            content: base64Content
        }
    };
}

async function indexDocumentToElasticsearch(document, documentID) {
    const ELASTIC_URL = 'http://localhost:9200/documentos_ifal/_doc/?pipeline=attachment';
    const HEADERS = {
        'Content-Type': 'application/json'
    };
    try {
        const response = await axios.post(ELASTIC_URL, document, { headers: HEADERS });
        console.log('Documento indexado com sucesso:', response.data);
    } catch (error) {
        console.error('Erro ao indexar documento:', error);
    }
}

async function main(data) {
    const filePath1 = data.filePath;
    const atoDocumento1 = {
        ano: data.ano,
        arquivo: data.arquivo,
        ato_id: "A002",
        data_publicacao: data.data_publicacao,
        ementa: data.ementa,
        fonte: {
            esfera: "Estadual",
            orgao: "Intituto Federal de Alagoas",
            sigla: "IFAL-RL",
            uf: "Rio Largo",
            uf_sigla: "RL",
            url: "https://www2.ifal.edu.br/campus/riolargo"
        },
        numero: data.numero,
        tags: ["edital"],
        tipo_doc: "edital",
        titulo: data.titulo
    };
    console.log(atoDocumento1)


    const documentID1 = urlize(filePath1.split('/').pop());
    const document1 = createDocument(filePath1, atoDocumento1);

    console.log(document1, documentID1);


    await indexDocumentToElasticsearch(document1, documentID1);
}
//2024
// [
//     {
//         filePath: "node_amb/pdfs/edital-2024-IFAL-rio-largo/CONVOCACAO_DE_CADASTRO_RESERVA_DO_EDITAL_N01_2024DG__IFALCAMPUS_RIO_LARGO_assinado.pdf",
//         ano: 2024,
//         arquivo: "CONVOCACAO_DE_CADASTRO_RESERVA_DO_EDITAL_N01_2024DG__IFALCAMPUS_RIO_LARGO_assinado.pdf",
//         numero: "01/2024",
//         data_publicacao: "2024-04-08",
//         tags: ["edital", "auxilio", "permanência", "cadastro"],
//         titulo: "1° Convocação de cadastro reserva (Programa Auxilio Permanência)",
//         ementa: " Convocação de estudante do cadastro reserva para o Programa Auxílio Permanência, conforme a Política de Assistência Estudantil do IFAL - Campus Rio Largo. Instruções para envio de documentos e dados bancários no período de 08 a 10 de abril de 2024."
//     },
//     {
//         filePath: "node_amb/pdfs/edital-2024-IFAL-rio-largo/CONVOCACAO_DE_CADASTRO_RESERVA_DO_EDITAL_N01_2024DG__IFALCAMPUS_RIO_LARGO_assinado.pdf",
//         ano: 2024,
//         arquivo: "CONVOCACAO_DE_CADASTRO_RESERVA_DO_EDITAL_N01_2024DG__IFALCAMPUS_RIO_LARGO_assinado.pdf",
//         numero: "01/2024-DG",
//         data_publicacao: "2024-07-16",
//         tags: ["edital", "auxilio", "permanência", "cadastro"],
//         titulo: "3° Convocação de cadastro reserva (Programa Auxilio Permanência)",
//         ementa: " Convocação de estudante do cadastro reserva para o Programa Auxílio Permanência, conforme a Política de Assistência Estudantil do IFAL - Campus Rio Largo. Instruções para envio de documentos e dados bancários no período de 08 a 10 de abril de 2024."
//     },
//     {
//         filePath: "node_amb/pdfs/edital-2024-IFAL-rio-largo/EditalProjetodeEnsino2024assinadoDEeDGeAnexoIpdf.pdf",
//         ano: 2024,
//         arquivo: "EditalProjetodeEnsino2024assinadoDEeDGeAnexoIpdf.pdf",
//         numero: "03/2024",
//         data_publicacao: "2024-07-12",
//         tags: ["edital", "projeto", "ensino", "cadastro"],
//         titulo: "SELEÇÃO DE PROJETOS DE ENSINO",
//         ementa: `O Departamento de Ensino do Campus Rio Largo, no uso de suas atribuições,
// torna pública a abertura de Edital que disciplina os procedimentos para
// submissão, análise, seleção, execução, acompanhamento e avaliação de
// Projetos de Ensino (PE) no Campus Rio Largo/Instituto Federal de Alagoas -
// IFAL, a serem desenvolvidos por Servidores/as, em até 05 (seis) meses, no
// período compreendido entre agosto a dezembro de 2024.`
//     },
//     {
//         filePath: "node_amb/pdfs/edital-2024-IFAL-rio-largo/resultadofinalmatriculasubsequente.pdf",
//         ano: 2024,
//         arquivo: "resultadofinalmatriculasubsequente.pdf",
//         numero: "03/2024",
//         data_publicacao: "2024-07-12",
//         tags: ["edital", "projeto", "ensino", "cadastro"],
//         titulo: `Resultado Final Chamada Regular
// Ensino Técnico Subsequente 2024.1 - Curso de Informática para Internet`,
//         ementa: ``
//     },
//     {
//         filePath: "node_amb/pdfs/edital-2024-IFAL-rio-largo/resultadofinalmatriculasubsequente.pdf",
//         ano: 2024,
//         arquivo: "resultadofinalmatriculasubsequente.pdf",
//         numero: "03/2024",
//         data_publicacao: "2024-07-12",
//         tags: ["edital", "projeto", "ensino", "cadastro"],
//         titulo: `Resultado Final Chamada Regular
// Ensino Técnico Subsequente 2024.1 - Curso de Informática para Internet`,
//         ementa: ``
//     },
// ].forEach(async (dados) => await main(dados))

// [
//     {
//         filePath: "node_amb/pdfs/edital-2023-IFAL-rio-largo/resultado_parcial_estagio.pdf",
//         ano: 2023,
//         arquivo: "resultado_parcial_estagio.pdf",
//         numero: "01/2023",
//         data_publicacao: "2023-03-07",
//         tags: ["edital", "auxilio", "permanência", "cadastro"],
//         titulo: `SELEÇÃO  DE  ESTAGIÁRIOS/AS
// INTERNOS NÃO REMUNERADOS DESTINADOS À FORMAÇÃO DE CADASTRO RESERVA`,
//         ementa: ""
//     },
//     {
//         filePath: "node_amb/pdfs/edital-2023-IFAL-rio-largo/2Edital_em_Construcao_Oculos2023.2_1_assinado.pdf",
//         ano: 2023,
//         arquivo: "2Edital_em_Construcao_Oculos2023.2_1_assinado.pdf",
//         numero: "04/2023",
//         data_publicacao: "2023-08-01",
//         tags: ["edital", "auxilio", "oculos", "cadastro"],
//         titulo: `EDITAL DE SELEÇÃO DO PROGRAMA DE APOIO ÀS ATIVIDADES
// ESTUDANTIS/ CONCESSÃO DE ÓCULOS`,
//         ementa: "A Direção Geral do Campus Rio Largo do Instituto Federal de Alagoas, atuando conforme suas atribuições e em conformidade com a Política de Assistência Estudantil aprovada pela Resolução nº 16/CS em 11 de dezembro de 2017 e alterada pela Resolução nº 21 em 20 de abril de 2020, comunica a abertura das inscrições para o Programa de Apoio às Atividades Estudantis, que visa à concessão de óculos corretivos aos estudantes."
//     },
//     {
//         filePath: "node_amb/pdfs/edital-2023-IFAL-rio-largo/2Edital_em_Construcao_Oculos2023.2_1_assinado.pdf",
//         ano: 2023,
//         arquivo: "2Edital_em_Construcao_Oculos2023.2_1_assinado.pdf",
//         numero: "04/2023",
//         data_publicacao: "2023-08-01",
//         tags: ["edital", "auxilio", "oculos", "cadastro"],
//         titulo: `EDITAL DE SELEÇÃO DO PROGRAMA DE APOIO ÀS ATIVIDADES
// ESTUDANTIS/ CONCESSÃO DE ÓCULOS`,
//         ementa: "A Direção Geral do Campus Rio Largo do Instituto Federal de Alagoas, atuando conforme suas atribuições e em conformidade com a Política de Assistência Estudantil aprovada pela Resolução nº 16/CS em 11 de dezembro de 2017 e alterada pela Resolução nº 21 em 20 de abril de 2020, comunica a abertura das inscrições para o Programa de Apoio às Atividades Estudantis, que visa à concessão de óculos corretivos aos estudantes."
//     },
// ].forEach(async (dados) => await main(dados))



[
    
].forEach(async (dados) => await main(dados))

