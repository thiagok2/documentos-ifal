import subprocess
import os
from datetime import datetime

scripts = [
    "crawler.acesso_info.revi_consoli",
    "crawler.docs-institucionais.cepe_res",
    "crawler.docs-institucionais.consup_res",
    "crawler.docs-institucionais.proad_in",
    "crawler.gestao-pessoas.contrata_remo",
    "crawler.gestao-pessoas.dev_pessoas",
    "crawler.gestao-pessoas.legi_norma_in",
    "crawler.o-ifal.procu_fed_parecer",
    "crawler.pro-reitorias.pesquisa.antigos",
    "crawler.adm.normas",
    "crawler.todo_resto"
]

# Criar pasta de logs, se não existir
os.makedirs("logs", exist_ok=True)

print(f"\n==== Iniciando execução dos crawlers ({len(scripts)} scripts) ====\n")

for script in scripts:
    log_name = f"logs/{script.replace('.', '_')}.log"
    print(f"▶ Executando: {script} → log em {log_name}")

    with open(log_name, "w") as log_file:
        log_file.write(f"== Executando {script} em {datetime.now()} ==\n")
        try:
            result = subprocess.run(
                ["python3", "-m", script],
                stdout=log_file,
                stderr=subprocess.STDOUT,
                check=True,
                text=True
            )
            print(f"✅ Finalizado com sucesso: {script}\n")
        except subprocess.CalledProcessError as e:
            print(f"❌ ERRO em {script} (verifique {log_name})\n")
            log_file.write(f"\n[ERRO] Código de retorno: {e.returncode}\n")
