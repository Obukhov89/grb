<style>
    .select2-selection {
        min-height: 40px;
    }
    .select2-selection__rendered {
        margin-top: 6px;
    }

    [data-js-form-object] select {
        width: 200px;
    }
</style>

<div method="post" data-js-form-object>

    <table id="obj">
        <tbody id="obj_body">

        <tr>
            <td>Название</td>
            <td><input class="tz" type="text" name="NAME" value="">
            </td>
        </tr>
        <tr>
            <td>Клиент</td>
<!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
            <td>
                <select list="gost" class="res gost" data-js-clients name="ID_COMPANY">
                    <option value=""></option>
                    <?php foreach ($this->data["companyList"] as $company): ?>
                        <option value="<?= $company["id"] ?>"><?= $company["title"] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Город</td>
            <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
            <td>
                <select data-js-cities name="CITY">
                    <option value=""></option>
                    <?php foreach ($this->data["cityList"] as $city): ?>
                        <option value="<?= $city["id"] ?>"><?= $city["name"] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Километраж</td>
            <td><input type="number" name="KM"></td>
        </tr>
        <tr>
            <td>Координаты</td>
            <td>
                <input data-js-coords class="res" type="number" step="0.000001" name="coord[0][0]" value="0" data-id="0">
                <input data-js-coords class="res" type="number" step="0.000001" name="coord[0][1]" value="0" data-id="0">
            </td>
        </tr>
        <tr>
            <td>Координаты</td>
            <td>
                <input data-js-coords class="res" type="number" step="0.000001" name="coord[1][0]" value="0" data-id="1">
                <input data-js-coords class="res" type="number" step="0.000001" name="coord[1][1]" value="0" data-id="1">
            </td>
        </tr>

        </tbody>
    </table>
    <input type="hidden" name="ID" value="">
    <input type="button" id="add" value="Добавить координаты ">
    <input type="button" id="save" value="Сохранить">
</div>

