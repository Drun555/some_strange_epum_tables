<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>EPUM</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="jquery-3.4.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="epum.css?1">
</head>

<body>
    <!-- Modal -->
    <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Удаление</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="delete_name">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="a_delete_button" onclick="" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div id="form_position">
        <table id="first" class="table">
            <tbody>
                <thread>
                    <tr id="search_row" class="table-borderless">
                        <td width="45%">
                            <input id="search_query" class="form-control" type="text" placeholder="Print here">
                        </td>
                        <td colspan="3">
                            <button onclick="add_search_query()" id="search_button" type="button" class="btn btn-primary" style="width:49%">Search</button>
                            <button onclick="reveal_add_row()" id="search_button" type="button" class="btn btn-success button-width" style="width:46%">Add one</button>
                        </td>
                    </tr>
                    <tr id="main_row">
                        <td width="45%">Name</td>
                        <td width="10%">
                            <a onclick="setSorting(1)">Count</a>
                            <a onclick="setSorting(1)" id="count_triangle" class="triangle">▲</a>
                        </td>
                        <td width="15%">
                            <a onclick="setSorting(2)">Price</a>
                            <a onclick="setSorting(2)" id="price_triangle" class="triangle">▲</a>
                        </td>
                        <td width="50%">Actions</td>
                    </tr>
                </thread>
            </tbody>
        </table>
        <table id="second" class="table table-hover">
            <tbody id="second_body">
                <!-- JS code -->
            </tbody>
        </table>
        <form id="third_form">
            <div class="form-group">
                <table id="third_table" class="table table-borderless">
                    <tbody>
                        <thread>
                            <tr id="third_body">
                                <td width="45%">
                                    <input required maxlength=15 id="add_name" class="form-control" type="text" placeholder="Name">
                                </td>
                                <td width="10%">
                                    <input onkeypress="return isNumberKey(event)" type="number" min="0" id="add_count" class="form-control" placeholder="C-nt" pattern="[0-9]">
                                </td>
                                <td width="15%">
                                    <div class="input-group mb-3">
                                        <input onkeypress="return isNumberKey_price(event)" type="text" id="add_price" class="form-control" placeholder="Price" aria-label="Price">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">$</span>
                                        </div>
                                    </div>
                                </td>
                                <td width="50%">
                                    <button id="add_update" onclick="add_query()" type="button" class="btn btn-outline-primary" style="width:100%">Add</button>
                                </td>
                            </tr>
                            <tr id="third_errors">
                                <td width="45%">
                                    <p id="name_error"></p>
                                </td>
                                <td width="15%">
                                    <p id="name_count"></p>
                                </td>
                                <td width="50%">
                                    <p id="name_count"></p>
                                </td>
                            </tr>
                        </thread>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <script type="text/javascript">
    var test;
    $("#third_table").hide();

    // Первый запуск
    get_file();




    var QueryListView = new Array();
    // Сюда будем подгружать файлы из JSON-файла. Операции добавления, редактирования и удаления проводим на нём, но показываем не его, а SortingView - массив-копию, который будет пересоздаваться каждый раз для сортировки.

    var isItSorting = 0;
    // 0 - без сортировки. 1 - по количеству. 2 - по цене

    var searchQuery = '';

    // Показать строку добавления/редактирования
    function reveal_add_row() {
        setSorting(3);
        $("#third_table").show();
        $("#add_update").removeClass(" btn-danger");
        $("#add_update").addClass("btn-outline-primary");
        $("#add_update").attr("onclick", "add_query()");
        $("#add_update").text("Add");
        $("#add_name").val("");
        $("#add_count").val("");
        $("#add_price").val("");
    }

    // Фетчим файл с диска без пхп.
    function get_file() {
        fetch('epum.json')
            .then(response => response.json())
            .then(jsonResponse => execute(jsonResponse))
    }
    // https://stackoverflow.com/questions/14446447/how-to-read-a-local-text-file

    // Потом добавляем его распарсенный вариант в массив.
    function execute(file_query) {
        QueryListView = file_query;
        for (var v in QueryListView) {
            QueryListView[v].price = Number(QueryListView[v].price);
            QueryListView[v].count = Number(QueryListView[v].count);
            // PHP почему-то решила преобразовывать все номера в стринги, когда записывает файл. Разбираться не очень хотелось, ведь это некритично - потерь все равно нет. 
        }
        update_query_view();
    }

    // Основная функция для вывода таблицы.
    function update_query_view() {

        // Весь вывод работает через SortingView - это клон QueryListView. Все операции проводим 
        // непосредственно в QueryListView. RefreshID - функкция, которая будет помогать сохранять индексы, чтобы ничего не перемешалось.
        refreshID();
        
        // Все созданные строки в таблице имеют этот класс. Перед обновлением всё нужно подчистить. 
        $(".jquery_row").remove();

        // Это будет строка, в которую мы будем складывать наш набор строк. 
        // Потом добавим его в основную таблицу.
        temp_second_body = '';
        row_string_id = 0;

        // sort_view - функция, которая отдаёт клон QueryListView, отсортированный в зависимости
        // от ключа.
        SortingView = sort_view(isItSorting);

        // Выводим отсортированную строку
        for (var v in SortingView) {
            row_string_id++;

            // Следующие три строки - валидация поиска. searchQuery тоже в верхнем регистре.
            // Этот метод позволяет сделать регистронезависимый поиск на инклудах
            var temp_for_temp = String(SortingView[v].name).toUpperCase();
            if (temp_for_temp.includes(searchQuery))
                temp_second_body += `
            <tr id="row_string_id_` + v + `" class="jquery_row">
              <td width="45%">` + SortingView[v].name + `</td>
              <td width="10%">` + SortingView[v].count + `</td>
              <td width="15%">$` + String(SortingView[v].price) + `</td>
              <td width="50%">
              <button onclick="edit_query(` + SortingView[v].real_id + `)" type="button" class="btn btn-warning button-width">Edit</button>
              <button onclick="delete_query(` + SortingView[v].real_id + `)" type="button" class="btn btn-outline-danger button-width">Delete</button>
              </td>
            </tr>`;
        }

        // Бомбим полученными строками в нашу таблицу
        $(temp_second_body).insertAfter($("#main_row"));
    }

    // Функция заполняет необходимые поля для того, чтобы получалась магия при 
    // нажатии на "Edit"
    function edit_query(row_id) {
        $("#third_table").show();
        $("#add_update").removeClass("btn-outline-primary");
        $("#add_update").addClass("btn-danger");
        $("#add_update").attr("onclick", "actually_edit_query(" + row_id + ")");
        $("#add_update").text("Edit");

        $("#add_name").val(QueryListView[row_id].name);
        $("#add_count").val(QueryListView[row_id].count);
        $("#add_price").val(QueryListView[row_id].price);
        $(".is-invalid").removeClass("is-invalid");
    }

    // А эта функция уже по-настоящему изменяет строку
    function actually_edit_query(row_id) {
        if (!CheckForms()) return;

        // Удаляем доллар и запятые из красивого представления строки, чтобы потом перевести
        // её в Number.
        number = $("#add_price").val().replace("$", "");
        while (number.indexOf(',') > -1) number = number.replace(",", "");

        QueryListView[row_id].name = $("#add_name").val();
        QueryListView[row_id].count = $("#add_count").val();
        QueryListView[row_id].price = Number(number);

        // Записываем изменённый массив в файл
        write_file();

        // Обновляем представление в таблице
        update_query_view();

        // Эта штука также умеет сбрасывать инпуты и менять свойства кнопки  
        reveal_add_row();

    }

    // Про это уже объяснял выше. Помогает не запутываться в индексах. Используется при
    // рефреше представления.
    function refreshID() {
        for (var v in QueryListView) QueryListView[v].real_id = v;
    }

    // Открываем модальное окно, подгоняем текст и функцию в кнопку.
    function delete_query(row_id) {
        $('#deleteConfirm').modal('show');
        name = QueryListView[row_id].name;
        string = `<p>Вы точно хотите удалить "` + name + `"?</p>`;
        $("#delete_name").html(string);
        $("#a_delete_button").attr("onclick", "actually_delete_query(" + row_id + ")")

    }

    // Собственно, скрываем модал и удалем строку
    function actually_delete_query(row_id) {
        $('#deleteConfirm').modal('hide');
        QueryListView.splice(row_id, 1);
        write_file();
        update_query_view();
    }

    // Добавление строки в массив. Сначала в массив, потом обновляем представление.
    function add_query() {
        if (!CheckForms()) return;

        // удаляем доллар и все запятые
        number = $("#add_price").val().replace("$", "");
        while (number.indexOf(',') > -1) number = number.replace(",", "");

        temp = { "name": $("#add_name").val(), "count": Number($("#add_count").val()), "price": Number(number), "real_id": QueryListView.length };

        QueryListView.push(temp);
        write_file();
        update_query_view();
    }


    // SORTING

    // Это не совсем относится к сортировке, но относится к треугольникам, которые её показывают
    function triangles_classes_toggle(a) {
        $(".triangle").removeClass("triangle_top triangle_bottom");

        switch (a) {
            case 1:
                $("#count_triangle").addClass("triangle_top");
                break;
            case -1:
                $("#count_triangle").addClass("triangle_bottom");
                break;
            case 2:
                $("#price_triangle").addClass("triangle_top");
                break;
            case -2:
                $("#price_triangle").addClass("triangle_bottom");
                break;
        }
    }

    var button;
    function setSorting(a) {
        // Нажмём один раз - получим 1. Нажмём второй раз на ту же кнопку - получим инверт.
        button = a;

        if (Math.abs(isItSorting) == a) { isItSorting = -isItSorting } else { isItSorting = a; }
        triangles_classes_toggle(isItSorting);
        console.log(isItSorting);
        update_query_view();
    }

    // Собственно, функция сортировки. Трюк с compare подсмотрел.
    function sort_view(arg) {

        // Странный способ склонировать массив объектов.
        SortingString = JSON.parse(JSON.stringify(QueryListView));

        // То, что ниже - чистой воды позор
        if (arg == 1) {
            function compare(a, b) {
                if (a.count < b.count) return -1;
                if (a.count > b.count) return 1;
                return 0;
            }
            SortingString.sort(compare);
        }
        if (arg == 2) {
            function compare(a, b) {
                if (a.price < b.price) return -1;
                if (a.price > b.price) return 1;
                return 0;
            }
            SortingString.sort(compare);
        }

        if (arg == -1) {
            function compare(a, b) {
                if (a.count > b.count) return -1;
                if (a.count < b.count) return 1;
                return 0;
            }
            SortingString.sort(compare);
        }
        if (arg == -2) {
            function compare(a, b) {
                if (a.price > b.price) return -1;
                if (a.price < b.price) return 1;
                return 0;
            }
            SortingString.sort(compare);
        }

        return SortingString;
    }

    // Просто переводим значение из формы в апперкейс, а потом включаем обновление.
    function add_search_query() {
        searchQuery = $("#search_query").val().toUpperCase();
        update_query_view();
    }

    // В задании не было PHP, но записывать на диск как-то нужно
    function write_file() {
        $.ajax({
            type: 'POST',
            url: '/write.php',
            dataType: 'json',
            data: { QueryListView },
            success: function(data) {
                if (!data.success) {
                    console.log(data); // 4444
                }
            }
        });
    }

    // Ниже валидируем наше всё. 

    function CheckForms() {
      // Кому нужны регулярные выражения, если можно заполнить всё if'ками
      $(".name_error_a").remove();

      // bored - наша сигнатура того, что что-то неправильно и сохранять нашу строку нельзя.
      bored = true;

      // Это к стоимости. Если он undefined, то всё хорошо. Если NaN - значит, 
      // пользователь накосячил. 
      if (test == undefined) {} else if (isNaN(test)) {
          $("<p class='name_error_a'>Не ставьте запятые в цене. Себе дороже. </p>").insertAfter("#name_error");
          bored = false;
          $("#add_price").addClass("is-invalid");
      } else $("#add_name").remove("is-invalid");

      if ($("#add_name").val().length > 15) {
          $("<p class='name_error_a'>Имя не может содержать более 15 символов. </p>").insertAfter("#name_error");
          bored = false;
      }

      // С тримом тоже подсмотрел. Работает же, и здорово.
      if (document.getElementById("add_name").value.trim() === '') {
          $("<p class='name_error_a'>Имя не может состоять только из пробелов или быть пустой. </p>").insertAfter("#name_error");
          $("#add_name").addClass("is-invalid");
          bored = false;
      } else $("#add_name").removeClass("is-invalid");

      return bored;

    }

    // При смещении фокуса после ввода цены мы её должны провалидировать и привести
    // в нужную нам по заданию форму 
    $("#add_price").focusout(function() {

        // Меняем запятые на точки для правильного преобразования
        test = $("#add_price").val();
        while (test.indexOf(',') > -1) test = test.replace(",", ".");

        // Приводим к Number
        test = Number(test);

        // Если введено некорректное значение
        if (isNaN(test)) {
            $(".name_error_a").remove();
            $("<p class='name_error_a'>Не ставьте столько запятых. Себе дороже выйдет. </p>").insertAfter("#name_error");
            $("#add_price").val(null);
            $("#add_price").addClass("is-invalid");
            test = 0;            
        } else $("#add_price").removeClass("is-invalid");

        // https://stackoverflow.com/questions/149055/how-to-format-numbers-as-currency-string
        reforged_num = (test).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        
        // Если всё хорошо преобразовалось, то выводим в строку.
        if (reforged_num != "NaN") $("#add_price").val("$" + reforged_num);
    });

    // Ну, тут всё ясно
    $("#add_name").focusout(function() {
        CheckForms();
    });

    // Тут тоже
    $("#add_price").click(function() {
        $("#add_price").val(null);
    });


    // Две функции для того, чтобы ограничить спектр нужных символов в инпуте.
    // Первая - для инпута Count, только с цифрами
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    // Вторая - для Price. С долларом, запятой и точкой.
    function isNumberKey_price(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode == 36 || charCode == 44 || charCode == 46) return true
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
    // https://stackoverflow.com/questions/13952686/how-to-make-html-input-tag-only-accept-numerical-values
    </script>
</body>

</html>