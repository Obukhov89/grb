<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <select id="selectStageGroup" class="form-control filter filter-stage-group">
                <option value='0' style="font-weight: bold" selected>Все статусы</option>
                <option value='1' style="font-weight: bold">Имеется УПД</option>
                <option value='2' style="font-weight: bold">УПД отправлен</option>
                <option value='3' style="font-weight: bold">Успешно завершенные</option>
                <option value='4' style="font-weight: bold">Не успешные заявки</option>
            </select>
        </div>
    </div>
</div>

<table id="journal_requests" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col"></th>
        <th scope="col" class="text-nowrap">Заявка</th>
        <th scope="col" class="text-nowrap">Дата</th>
    </tr>
    <tr class="header-search">
        <th scope="col"></th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" style="width: 600px !important;">
    <div class="offcanvas-header">
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">

    </div>
</div>

<div class='arrowLeft'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-left"/>
    </svg>
</div>
<div class='arrowRight'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-right"/>
    </svg>
</div>

<div class="line-dashed"></div>
