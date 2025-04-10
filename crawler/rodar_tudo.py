import subprocess

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

for script in scripts:
    print(f"\n=== Executando {script} ===")
    try:
        subprocess.run(["python3", "-m", script], check=True)
    except subprocess.CalledProcessError as e:
        print(f"Erro ao executar {script}: {e}")
