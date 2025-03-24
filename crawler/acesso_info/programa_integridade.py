import re
import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.config import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS

###incompleto

def main():
    response = requests.get("https://www2.ifal.edu.br/acesso-a-informacao/programa-de-integridade", headers=HEADERS)

    soup = BeautifulSoup(response.content, 'html.parser')

    content_div = soup.find("div", id="content-core")
    ahrefs = content_div.find_all('a', class_='internal-link')

    pdfs = []

    for a in ahrefs[:-10]:
        if a['href'].endswith('.pdf'):
            heading = a.find_previous('b')
            print(heading)
main()