$(document).ready(function () {

    $("select[name=estado_id]").change(function () {
        var uf = $(this).val();

        if (uf != "BR") {
            $.get("/api/unidades/" + uf + "/municipios-todos", function (busca) {
                $("select[id=municipio_id]").empty();
                if (busca.length !== 0) {
                    $("select[id=municipio_id]").append(
                        '<option value="">Selecione</option>'
                    );
                    $.each(busca, function (key, value) {
                        $("select[id=municipio_id]").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.nome + (value.criado === true ? '*': '')+
                                "</option>"
                        );
                    });
                } else {
                    $("select[id=municipio_id]").append(
                        '<option value="" disabled>Não há municípios disponíveis</option>'
                    );
                }
            });
        } else {
            $("select[id=municipio_id]").empty();
            $("select[id=municipio_id]").append(
                '<option value="99999">Especial</option>'
            );
        }
    });
});
