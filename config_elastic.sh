#!/bin/bash

echo -e "\nCriando o pipeline de ingestão..."
curl -s -X PUT "http://localhost:9200/_ingest/pipeline/attachment" \
  -H "Content-Type: application/json" \
  -d '{
           "description": "Extract attachment information",
           "processors": [
             {
               "attachment": {
                 "field": "data",
                 "indexed_chars": "-1"
               }
             }
           ]
         }' \
  --insecure | jq '.acknowledged' && echo "Pipeline de ingestão criado com sucesso." || echo "Falha ao criar o pipeline de ingestão."

echo -e "\nDeletando o índice 'documentos_ifal'..."
curl -s -X DELETE "http://localhost:9200/documentos_ifal" \
  --insecure | jq '.acknowledged' && echo "Índice 'documentos_ifal' deletado com sucesso." || echo "Falha ao deletar o índice 'documentos_ifal'."

echo -e "\nCriando o índice 'documentos_ifal' com configurações específicas..."
curl -s -X PUT "http://localhost:9200/documentos_ifal" \
     -H "Content-Type: application/json" \
     -d '{
  "settings": {
    "analysis": {
      "filter": {
        "synonym_test": {
          "type": "synonym", 
          "synonyms": [
            "9394/96, 9.394/96, Diretrizes e Bases da Educação, Diretrizes e Bases  => LDB",
            "Educação para Jovens e Adultos, Jovens e Adultos => EJA",
            "Educação a Distância => EAD",
            "Técnico => Profissionalizante",
            "Profissional => Profissionalizante",
            "Ensino Técnico, Curso FIC => PRONATEC",
            "Capacitação => Qualificação",
            "infância => criança",
            "diversidade, deficiência, surdo, cego, síndrome de down, autismo, educação Especial,  dificuldades auditivas => inclusiva",
            "13.146,  Lei Brasileira de Inclusão => Inclusão"
          ]
        },
        "brazilian_stop": {
          "type": "stop",
          "stopwords":  "_brazilian_" 
        },
        "brazilian_keywords": {
          "type": "keyword_marker",
          "keywords":   [] 
        },
        "brazilian_stemmer": {
          "type": "stemmer",
          "language":   "brazilian"
        }
      },
      "analyzer": {
        "my_analyzer": {
          "tokenizer": "standard",
          "filter": [
            "lowercase",
            "synonym_test",
            "brazilian_stop",
            "brazilian_keywords",
            "brazilian_stemmer"
          ]
        }
      }
    }
  },
  "mappings": {
    "properties": {
      "ato": {
        "properties": {
          "arquivo": {
            "type": "text",
            "fields": {
              "keyword": {
                "type": "keyword"
              }
            }
          },
          "titulo": {
            "type": "text",
            "fields": {
              "keyword": {
                "type": "keyword"
              }
            }
          },
          "ato_id": {
            "type": "text",
            "fields": {
              "keyword": {
                "type": "keyword"
              }
            }
          },
          "numero": {
            "type": "text",
            "fields": {
              "keyword": {
                "type": "keyword"
              }
            }
          },
          "data_publicacao": {
            "type": "date"
          },
          "ano": {
            "type": "integer"
          },
          "ementa": {
            "type": "text",
            "analyzer": "my_analyzer",
            "fields": {
              "keyword": {
                "type": "keyword"
              }
            }
          },
          "tipo_doc": {
            "type": "text",
            "fielddata": true
          },
          "tags": {
            "type": "text",
            "fielddata": true,
            "fields": {
              "keyword": {
                "type": "keyword",
                "ignore_above": 256
              }
            }
          },
          "fonte": {
            "properties": {
              "orgao": {
                "type": "text",
                "fields": {
                  "keyword": {
                    "type": "keyword"
                  }
                }
              },
              "uf": {
                "type": "keyword"
              },
              "uf_sigla": {
                "type": "keyword"
              },
              "sigla": {
                "type": "keyword"
              },
              "esfera": {
                "type": "keyword"
              },
              "url": {
                "type": "keyword"
              }
            }
          }
        }
      },
      "attachment": {
        "properties": {
          "content": {
            "type": "text",
            "analyzer": "my_analyzer",
            "fields": {
              "keyword": {
                "type": "keyword",
                "ignore_above": 256
              }
            }
          }
        }
      }
    }
  }
}
' --insecure | jq '.acknowledged' && echo "Índice 'documentos_ifal' criado com sucesso." || echo "Falha ao criar o índice 'documentos_ifal'."

echo -e "\nAtualizando o mapeamento do índice 'documentos_ifal'..."
curl -s -X PUT "http://localhost:9200/documentos_ifal/_mapping/" \
  -H "Content-Type: application/json" \
  -d '{  
     "properties": {
        "ato.tags": {
            "type": "text",
            "fielddata": true,
            "fields": {
                "keyword": {
                    "type": "keyword",
                    "ignore_above": 256
                }
            }
        }
    }}' \
  --insecure | jq '.acknowledged' && echo "Mapeamento do índice 'documentos_ifal' atualizado com sucesso." || echo "Falha ao atualizar o mapeamento do índice 'documentos_ifal'."

echo -e "\nObtendo o mapeamento do índice 'documentos_ifal'..."
curl -s -X GET "http://localhost:9200/documentos_ifal/_mapping" \
  --insecure | jq '.' && echo "Mapeamento obtido com sucesso." || echo "Falha ao obter o mapeamento."
