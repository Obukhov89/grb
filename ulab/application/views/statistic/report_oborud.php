<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="2023-01-01">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?=date('Y-m-d')?>">
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>


<table id="journal_gost" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">Оборудование</th>
        <th scope="col" class="text-nowrap">Инвентарный номер</th>
        <th scope="col" class="text-nowrap">Идентификация оборудования</th>
        <th scope="col" class="text-nowrap">Количество использований</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <select class="form-control search">
                <option value="">Все</option>
                <option value="SI">СИ</option>
                <option value="IO">ИО</option>
                <option value="VO">ВО</option>
                <option value="TS">ТС</option>
                <option value="SO">CO</option>
                <option value="REACT">Реактивы</option>
                <option value="OOPP">Оборудование для
                    отбора/подготовки проб
                </option>
            </select>
        </th>
        <th scope="col">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
