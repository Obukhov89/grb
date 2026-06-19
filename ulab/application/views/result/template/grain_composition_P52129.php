<div class="measurement-wrapper" id="grainWrapper">
    <h3 class="mb-3">Зерновой состав по ГОСТ-32761</h3>
	<input type="hidden" name="type" value="grain">
    <table class="table list_data" style="width: 100%;">
        <thead>
            <tr>
                <th>Испытание</th>
                <th>Сито</th>
                <th>Масса остатка на сите, г</th>
                <th>Масса мерной пробы, г</th>
                <th>Частные остатки на сите</th>
                <th>Содержание частиц порошка</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="3">1</td>
                <td>Мельче 1.25</td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_25" value="<?=$this->data['measuring']['m_1_25']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_1_25" value="<?=$this->data['measuring']['m_1_1_25']?>">
                </td>
                <td>
                    <input class="form-control" readonly type="number" step="any" name="a_1_25" value="<?=$this->data['measuring']['a_1_25']?>">
                </td>
                <td rowspan="2">
                    <div>Мельче 1.25</div>
                    <input class="form-control" readonly type="number" step="any" name="p_1_25" value="<?=$this->data['measuring']['p_1_25']?>">
                </td>
            </tr>
            <tr>
                <td>Мельче 0.315</td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_315" value="<?=$this->data['measuring']['m_0_315']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_315" value="<?=$this->data['measuring']['m_1_0_315']?>">
                </td>
                <td>
                    <input class="form-control" readonly type="number" step="any" name="a_0_315" value="<?=$this->data['measuring']['a_0_315']?>">
                </td>
            </tr>
            <tr>
                <td>Мельче 0.071</td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_071" value="<?=$this->data['measuring']['m_0_071']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_071" value="<?=$this->data['measuring']['m_1_0_071']?>">
                </td>
                <td>
                    <input class="form-control" readonly type="number" step="any" name="a_0_071" value="<?=$this->data['measuring']['a_0_071']?>">
                </td>
                <td rowspan="2">
                    <div>Мельче 0.315</div>
                    <input class="form-control" readonly type="number" step="any" name="p_0_315" value="<?=$this->data['measuring']['p_0_315']?>">
                </td>
            </tr>
            <tr>
                <td rowspan="3">2</td>
                <td>
                    Мельче 1.25
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_25_2" value="<?=$this->data['measuring']['m_1_25_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_1_25_2" value="<?=$this->data['measuring']['m_1_1_25_2']?>">
                </td>
                <td>
                    <input class="form-control" readonly type="number" step="any" name="a_1_25_2" value="<?=$this->data['measuring']['a_1_25_2']?>">
                </td>
            </tr>
            <tr>
                <td>
                    Мельче 0.315
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_315_2" value="<?=$this->data['measuring']['m_0_315_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_315_2" value="<?=$this->data['measuring']['m_1_0_315_2']?>">
                </td>
                <td>
                    <input class="form-control" readonly type="number" step="any" name="a_0_315_2" value="<?=$this->data['measuring']['a_0_315_2']?>">
                </td>
                <td rowspan="2">
                    <div>Мельче 0.071</div>
                    <input class="form-control" readonly type="number" step="any" name="p_0_071" value="<?=$this->data['measuring']['p_0_071']?>">
                </td>
            </tr>
            <tr>
                <td>
                    Мельче 0.071
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_071_2" value="<?=$this->data['measuring']['m_0_071_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_071_2" value="<?=$this->data['measuring']['m_1_0_071_2']?>">
                </td>
                <td>
                    <input class="form-control" readonly type="number" step="any" name="a_0_071_2" value="<?=$this->data['measuring']['a_0_071_2']?>">
                </td>
            </tr>
        </tbody>
    </table>

    <button type="button" id="calc" class="btn btn-primary calculate-average me-2">Рассчитать</button>
    <button type="submit" class="btn" class="btn btn-primary save">Сохранить</button>
</div>
