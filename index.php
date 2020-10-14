<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universidad</title>
    <link rel="stylesheet" href="public/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <!-- <link rel="stylesheet" href="public/fontawesome/css/all.css"> -->
    <link rel="stylesheet" href="public/style.css">
    <style>
        #chart_histograma { width: 100% !important; }
        table { width: 100% !important; }
        td input { width: 100%; height: 100%; }
        td { padding: 0px !important; border: 0px solid !important; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <form id="create_file" action="api/query.php" method="post">
            <input type="hidden" name="ajax" value="create_file">
            <div class="col-12">
                <label>Nombre</label>
                <input type="name" name="name" required>
                <input type="file" name="excel" id="excel" accept=".csv,.xlsx,.xls" required>
                <button class="btn btn-primary float-right">Crear</button>
            </div>
            <div id="cards" class="row col-md-12"></div>
        </form>
    
        <form id="save" method="post" action="api/save_data.php" style="display: none;">
            <a href="" class="btn btn-info float-left"><i class="fas fa-arrow-left"></i> Volver</a>
            <button class="btn btn-primary float-right"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btn_eliminar" class="btn btn-danger float-right"><i class="fa fa-times"></i> Eliminar</button>
            <input type="hidden" id="archivo_id" name="archivo">
            <input type="hidden" id="deletes" name="deletes">

            <div id="chart_histograma"></div>
            <table id="table" class="table">
              <thead>
                <th>Código</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Dane_mpio</th>
                <th>Dane_dtpt</th>
                <th>Semestre</th>
                <th>Estrato</th>
                <th>Promedio</th>
                <th>Cod_programa</th>
                <th>Programa</th>
                <th><button type="button" id="btn_add_row" onclick="add_tr_in_table([[]])" class="btn-success"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg></button></th>
              </thead>
              <tbody></tbody>
            </table>
        </form>
    </div>

    <script src="public/jquery-3.5.1.js"></script>
    <script src="public/popper.min.js"></script>
    <script src="public/bootstrap.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        $(btn_eliminar).on('click', function(e){
            const id = window.location.hash.replace('#id=', '');
            if (confirm('¿Estás seguro de realizar esta operación?')) $.post('api/query.php', { ajax: 'delete_archivo', id }, function (){
                window.location.href = "";
            });
        });

        (async () => {
            const files = JSON.parse(await $.post('api/query.php', { ajax: 'get_files' }));
            var html = '';
            for (const archivo of files)
                html += `<div class="col-md-3 p-2">
                    <a href="#id=${archivo.id}" class="card m-2 card-1 text-dark cursor">
                        <div class="card-header">
                            <center><i class="far fa-10x fa-file-excel"></i></center>
                        </div>
                        <div class="card-body text-center m-0 p-0"><h4>${archivo.nombre}</h4> ${archivo.fecha_creacion}</div>
                    </a>
                </div>`;
            $(cards).append(html);

            if (window.location.hash)
                window.onhashchange();
        })();

        window.onhashchange = async function () {
            const id = window.location.hash.replace('#id=', '');
            const data = JSON.parse(await $.post('api/query.php', { id, ajax: 'get_by_id' }));
            add_tr_in_table(data.table, true);
            archivo_id.value = id;
            $(create_file).hide();
            $(save).show();

            const his_categorie = [], his_value = [];
            for (const histograma of data.histograma) {
                his_value.push(histograma.count);
                his_categorie.push(`Semestre ${histograma.semestre}`);
            }

            Highcharts.chart('chart_histograma', {
                chart: { type: 'column' },
                title: { text: 'HISTOGRAMA' },
                xAxis: { categories: his_categorie, crosshair: true },
                yAxis: { min: 0, title: { text: 'Número de estudiantes' } },
                plotOptions: { column: { pointPadding: 0.2, borderWidth: 0 } },
                series: [{ name: 'Semestre', data: his_value }]
            });
        }

        $(create_file).submit(async function (e) {
            e.preventDefault();
            const id = await $.post(this.action, $(this).serialize());

            const formData = new FormData();
            formData.append(excel.name, excel.files[0]);
            $.ajax({
                url: `api/read_excel.php?archivo_id=${id}`,
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) { window.location.href = `#id=${id}`; }
            });
        });

        const _deletes = [];
        function add_tr_in_table(data, reset = false) {
            var html = '';
            for (const row in data)
            html += `<tr>
                <td><input type="text" name="codigo[]" value="${data[row].codigo || ''}"></td>
                <td><input type="text" name="nombre[]" value="${data[row].nombre || ''}"></td>
                <td><input type="text" name="apellido[]" value="${data[row].apellido || ''}"></td>
                <td><input type="text" name="dane_mpio[]" value="${data[row].dane_mpio || ''}"></td>
                <td><input type="text" name="dane_dpto[]" value="${data[row].dane_dpto || ''}"></td>
                <td><input type="text" name="semestre[]" value="${data[row].semestre || ''}"></td>
                <td><input type="text" name="estrato[]" value="${data[row].estrato || ''}"></td>
                <td><input type="text" name="promedio[]" value="${data[row].promedio || ''}"></td>
                <td><input type="text" name="cod_programa[]" value="${data[row].cod_programa || ''}"></td>
                <td><input type="text" name="programa[]" value="${data[row].programa || ''}"></td>
                <td>
                    <input type="hidden" name="id[]" value="${data[row].id}">
                    <button type="button" data-id="${data[row].id}" class="btn-danger new">x</button>
                </td>
            </tr>`;

            if (reset) $(table).find('tbody').html('');

            $(table).find('tbody').append(html);
            $(table).find('tbody button.btn-danger.new').on('click', function () {
            var sw = true;
            $(this).parents('tr').find('input').each(function () { if (this.value) sw = false });
                if (sw || confirm('Estás putas seguro de eliminar esa verga')) {
                    _deletes.push(this.dataset.id);
                    deletes.value = _deletes.join(',')
                    $(this).parents('tr').remove();
                }
            }).removeClass('new');
        }
    </script>
</body>
</html>