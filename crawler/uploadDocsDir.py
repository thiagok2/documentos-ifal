import os, base64
from crawler.service import DOWNLOAD_DIR, es, INDEX_NAME, cursor, conn, create_ato_documento_dir, ler_multiplos_registros, convert_doc_type, convert_subject


# --- fluxo principal ---
caminho = "crawler/info.txt"
registros = ler_multiplos_registros(caminho)
atos_criados = []

for i, reg in enumerate(registros, start=1):
    # pegando campos (pode vir None)
    ANO = reg.get("ANO")
    DATA = reg.get("DATA_PUBLICACAO") or f'{ANO}-01-01'
    TAGS = reg.get("TAGS") or []
    TITULO = reg.get("TITULO")
    TIPO_DOC = reg.get("TIPO_DOC") or "Indeterminado"
    URL = reg.get("URL") or 'https://www2.ifal.edu.br/'
    NUMERO = reg.get("NUMERO") or "00"
    FILENAME = reg.get("FILENAME")
    PUBLICO = reg.get("PUBLICO") or True
    ASSUNTO = reg.get("ASSUNTO") or "Assunto Desconhecido"

    if not FILENAME:
        print(f"Registro ignorado por ausência de FILENAME: {reg}")
        continue

    if not ANO:
        print(f"Registro ignorado por ausência de ANO: {reg}")
        continue

    if not TITULO:
        print(f"Registro ignorado por ausência de TITULO: {reg}")
        continue

    ato = create_ato_documento_dir(
        filename=FILENAME,
        titulo=TITULO,
        tags=TAGS,
        ANO=ANO,
        URL=URL,
        numero=NUMERO,
        tipo=TIPO_DOC,
        data=DATA,
        publico=PUBLICO,
        i=i
    )

    caminho_arquivo = os.path.join(DOWNLOAD_DIR, ato["arquivo"])
    
    if not os.path.exists(caminho_arquivo):
        print(f"Arquivo não encontrado: {caminho_arquivo}")
        continue
    with open(caminho_arquivo, "rb") as pdf_file:
        encoded_doc = base64.b64encode(pdf_file.read()).decode("utf-8")
    doc = {
        "filename": caminho_arquivo,
        "data": encoded_doc,
        "ato": ato,
        "attachment": {
            "content": encoded_doc
        }
    }
    response = es.index(index=INDEX_NAME, pipeline="attachment", body=doc)
    elastic_id = response["_id"]
    print(f"INDEXADO NO ELASTIC: {elastic_id}")
    
    titulo = ato["titulo"]
    ano = ato["ano"]
    data_publicacao = ato["data_publicacao"]
    numero = ato["numero"]
    tipo_doc = convert_doc_type(ato["tipo_doc"])
    url = ato["fonte"]["url"] 
    filename = ato["arquivo"]
    publico = ato["publico"]
    assunto = convert_subject(ASSUNTO)
    
    query = """
        INSERT INTO documentos (ano, titulo, numero, ementa, arquivo, url, data_publicacao, tipo_documento_id ,user_id, assunto_id, unidade_id, publico)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    cursor.execute(query, (
        ano,
        titulo,
        numero,
        titulo,
        elastic_id,
        url,
        data_publicacao,
        tipo_doc,
        1,  # user_id
        assunto,  # assunto_id
        1,   # unidade_id
        publico  
    ))
    conn.commit()
    print(f"SALVO NO BANCO: {filename}")


print("Fim do Processo")