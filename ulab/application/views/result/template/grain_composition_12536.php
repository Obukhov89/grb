<div class="measurement-wrapper" id="grainWrapper">
    <h3 class="mb-3">Зерновой состав ГОСТ 32761</h3>
    <label for="average_mass">Масса средней пробы грунта, взятой для анализа, г</label>
	<input type="hidden" name="type" value="grain">
    <input class="form-control initial_mass" type="number" name="initial_mass"
           value="<?=$this->data['measuring']['initial_mass']?>">

    <table class="table list_data">
        <thead>
        <tr>
            <th class="text-center"></th>
            <th class="text-center">10</th>
            <th class="text-center">5</th>
            <th class="text-center">2</th>
            <th class="text-center">1</th>
            <th class="text-center">0.5</th>
            <th class="text-center">0.25</th>
            <th class="text-center">0.1</th>
            <th class="text-center">0.05</th>
            <th class="text-center">0.005</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-center">g<sub>i</sub></td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_10"
                       value="<?=$this->data['measuring']['g_10']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_5"
                       value="<?=$this->data['measuring']['g_5']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_2"
                       value="<?=$this->data['measuring']['g_2']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_1"
                       value="<?=$this->data['measuring']['g_1']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_05"
                       value="<?=$this->data['measuring']['g_05']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_025"
                       value="<?=$this->data['measuring']['g_025']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_01"
                       value="<?=$this->data['measuring']['g_01']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_005"
                       value="<?=$this->data['measuring']['g_005']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="g_0005"
                       value="<?=$this->data['measuring']['g_0005']?>">
            </td>
        </tr>
        <tr>
            <td class="text-center">a<sub>i</sub></td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_10"
                       value="<?=$this->data['measuring']['a_10']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_5"
                       value="<?=$this->data['measuring']['a_5']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_2"
                       value="<?=$this->data['measuring']['a_2']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_1"
                       value="<?=$this->data['measuring']['a_1']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_05"
                       value="<?=$this->data['measuring']['a_05']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_025"
                       value="<?=$this->data['measuring']['a_025']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_01"
                       value="<?=$this->data['measuring']['a_01']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_005"
                       value="<?=$this->data['measuring']['a_005']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="a_0005"
                       value="<?=$this->data['measuring']['a_0005']?>">
            </td>
        </tr>
        <tr>
            <td class="text-center">П<sub>i</sub></td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_10" data-id="1"
                       value="<?=$this->data['measuring']['p_10']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_5" data-id="2"
                       value="<?=$this->data['measuring']['p_5']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_2" data-id="3"
                       value="<?=$this->data['measuring']['p_2']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_1" data-id="4"
                       value="<?=$this->data['measuring']['p_1']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_05" data-id="5"
                       value="<?=$this->data['measuring']['p_05']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_025" data-id="6"
                       value="<?=$this->data['measuring']['p_025']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_01" data-id="7"
                       value="<?=$this->data['measuring']['p_01']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_005" data-id="8"
                       value="<?=$this->data['measuring']['p_005']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any"
                       name="p_0005" data-id="9"
                       value="<?=$this->data['measuring']['p_0005']?>">
            </td>
        </tr>
        </tbody>
    </table>

    <label for="content_clay_particles">Содержание пылевидных и глинистых частиц, %</label>
    <input class="form-control" readonly type="number" step="any" name="content_clay_particles"
           value="<?=$this->data['measuring']['content_clay_particles']?>">

    <label for="name_soil">Наименование грунта</label>
    <input class="form-control" readonly type="text" step="any" name="name_soil"
           value="<?=$this->data['measuring']['name_soil']?>">

    <div>
        <button type="submit" class="btn" class="btn btn-primary save">Сохранить</button>
    </div>
</div>
