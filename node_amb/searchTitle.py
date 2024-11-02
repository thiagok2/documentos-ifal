import fitz  # PyMuPDF

def extrair_titulo(pdf_path):
    # Abre o documento PDF
    documento = fitz.open(pdf_path)
    
    titulo = None
    maior_fonte = 0

    # Itera sobre as páginas (geralmente o título está na primeira página)
    for pagina_num in range(len(documento)):
        pagina = documento.load_page(pagina_num)
        blocos = pagina.get_text("dict")["blocks"]
        
        for bloco in blocos:
            # Verifica se o bloco contém a chave 'lines'
            if "lines" not in bloco:
                continue
            
            for linha in bloco["lines"]:
                for span in linha["spans"]:
                    tamanho_fonte = span["size"]
                    texto = span["text"].strip()
                    
                    # Verifica se o texto atual tem o maior tamanho de fonte
                    if tamanho_fonte > maior_fonte and texto:
                        maior_fonte = tamanho_fonte
                        titulo = texto

        if titulo:
            break  # Se o título for encontrado, sair do loop

    documento.close()

    return titulo if titulo else "Título não encontrado"

# Caminho para o arquivo PDF
pdf_path = "./pdfs/edital-2024-IFAL-rio-largo/SeleodeMonitoresEspao4.0RioLargo2024assinado.pdf"  # Substitua pelo caminho do seu arquivo PDF

titulo = extrair_titulo(pdf_path)
print("Título do PDF:", titulo)
