import os, base64
from crawler.config import DOWNLOAD_DIR, es, INDEX_NAME, cursor, conn

def clean_none_fields(obj):
    """
    Remove de forma recursiva todos os pares chave: valor onde valor é None.
    """
    if isinstance(obj, dict):
        return {
            k: clean_none_fields(v)
            for k, v in obj.items()
            if v is not None
        }
    elif isinstance(obj, list):
        return [clean_none_fields(v) for v in obj if v is not None]
    else:
        return obj

def create_ato_documento(
    filename,
    titulo='Titulo não informado',
    tags=None,
    ANO=0,
    URL='Url não informada',
    numero='00',
    tipo='Edital',
    data='nada',
    publico=True
):
    # valores padrão
    if data in (None, 'nada'):
        data = f"{ANO}-01-01"
    if tags is None:
        tags = []
    elif not isinstance(tags, list):
        tags = [tags]

    ato = {
        "ano": ANO,
        "arquivo": filename,
        "ato_id": "A002",
        "data_publicacao": data,
        "ementa": titulo,
        "fonte": {
            "esfera": "Campus",
            "orgao": "Instituto Federal de Alagoas",
            "sigla": "IFAL",
            "uf": "AL",
            "uf_sigla": "AL",
            "url": URL
        },
        "numero": f'{numero}/{ANO}',
        "tags": tags,
        "tipo_doc": tipo,
        "titulo": titulo,
        "publico": publico
    }

    # Remove todos os campos None
    return clean_none_fields(ato)

def ler_multiplos_registros(caminho_arquivo):
    registros = []
    registro_atual = {}
    with open(caminho_arquivo, 'r', encoding='utf-8') as arquivo:
        for linha in arquivo:
            linha = linha.strip()
            if not linha:
                continue
            if linha == '+':
                if registro_atual:
                    registros.append(registro_atual)
                    registro_atual = {}
                continue
            if '=' in linha:
                chave, valor = linha.split('=', 1)
                chave = chave.strip()
                valor = valor.strip()
                # remove aspas simples ou duplas
                if (valor.startswith('"') and valor.endswith('"')) or \
                   (valor.startswith("'") and valor.endswith("'")):
                    valor = valor[1:-1]
                # converte tipos
                lower = valor.lower()
                if lower == 'true':
                    valor = True
                elif lower == 'false':
                    valor = False
                elif valor.isdigit():
                    valor = int(valor)
                elif ',' in valor:
                    valor = [item.strip() for item in valor.split(',')]
                registro_atual[chave] = valor
        if registro_atual:
            registros.append(registro_atual)
    return registros

# --- fluxo principal ---
caminho = "crawler/info.txt"
registros = ler_multiplos_registros(caminho)
atos_criados = []

for i, reg in enumerate(registros, start=1):
    # pegando campos (pode vir None)
    ANO = reg.get("ANO")
    DATA = reg.get("DATA_PUBLICACAO")  # pode ser None
    TAGS = reg.get("TAGS")
    TITULO = reg.get("TITULO")
    TIPO_DOC = reg.get("TIPO_DOC")
    URL = reg.get("URL")
    NUMERO = reg.get("NUMERO") or "00"
    FILENAME = reg.get("FILENAME")
    PUBLICO = reg.get("PUBLICO")

    if not FILENAME:
        print(f"Registro ignorado por ausência de FILENAME: {reg}")
        continue

    if not ANO:
        print(f"Registro ignorado por ausência de ANO: {reg}")
        continue

    if not TITULO:
        print(f"Registro ignorado por ausência de TITULO: {reg}")
        continue

    ato = create_ato_documento(
        filename=FILENAME,
        titulo=TITULO,
        tags=TAGS,
        ANO=ANO,
        URL=URL,
        numero=NUMERO,
        tipo=TIPO_DOC,
        data=DATA,
        publico=PUBLICO
    )
    atos_criados.append(ato)

for ato in atos_criados:
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
    data_publicacao = ato["data_publicacao"] or f'{ano}-01-01'
    numero = ato["numero"]
    #tipo_doc = ato["tipo_doc"] Perguntar ao prof
    url = ato["fonte"]["url"] or 'https://www2.ifal.edu.br/'
    filename = ato["arquivo"]
    query = """
        INSERT INTO documentos (ano, titulo, numero, ementa, arquivo, url, data_publicacao, user_id, assunto_id, unidade_id, publico)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    cursor.execute(query, (
        ano,
        titulo,
        numero,
        titulo,
        elastic_id,
        url,
        data_publicacao,
        1,  # user_id
        4,  # assunto_id
        1,   # unidade_id
        ato["publico"]
    ))
    conn.commit()
    print(f"SALVO NO BANCO: {filename}")


print("Fim do Processo")