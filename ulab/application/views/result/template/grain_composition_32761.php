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
                <td>Мельче 2.0</td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_2_0" value="<?=$this->data['measuring']['m_2_0']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_2_0" value="<?=$this->data['measuring']['m_1_2_0']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="a_2_0" value="<?=$this->data['measuring']['a_2_0']?>">
                </td>
                <td rowspan="2">
                    <div>Мельче 2.0</div>
                    <input class="form-control" type="number" step="any" name="p_2_0" value="<?=$this->data['measuring']['p_2_0']?>">
                </td>
            </tr>
            <tr>
                <td>Мельче 0.125</td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_125" value="<?=$this->data['measuring']['m_0_125']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_125" value="<?=$this->data['measuring']['m_1_0_125']?>">
                </td>
                <td>
                    <input class="form-control"  type="number" step="any" name="a_0_125" value="<?=$this->data['measuring']['a_0_125']?>">
                </td>
            </tr>
            <tr>
                <td>Мельче 0.063</td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_063" value="<?=$this->data['measuring']['m_0_063']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_063" value="<?=$this->data['measuring']['m_1_0_063']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="a_0_063" value="<?=$this->data['measuring']['a_0_063']?>">
                </td>
                <td rowspan="2">
                    <div>Мельче 0.125</div>
                    <input class="form-control" type="number" step="any" name="p_0_125" value="<?=$this->data['measuring']['p_0_125']?>">
                </td>
            </tr>
            <tr>
                <td rowspan="3">2</td>
                <td>
                    Мельче 2.0
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_2_0_2" value="<?=$this->data['measuring']['m_2_0_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_2_0_2" value="<?=$this->data['measuring']['m_1_2_0_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="a_2_0_2" value="<?=$this->data['measuring']['a_2_0_2']?>">
                </td>
            </tr>
            <tr>
                <td>
                    Мельче 0.125
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_125_2" value="<?=$this->data['measuring']['m_0_125_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_125_2" value="<?=$this->data['measuring']['m_1_0_125_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="a_0_125_2" value="<?=$this->data['measuring']['a_0_125_2']?>">
                </td>
                <td rowspan="2">
                    <div>Мельче 0.063</div>
                    <input class="form-control" type="number" step="any" name="p_0_063" value="<?=$this->data['measuring']['p_0_063']?>">
                </td>
            </tr>
            <tr>
                <td>
                    Мельче 0.063
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_0_063_2" value="<?=$this->data['measuring']['m_0_063_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="m_1_0_063_2" value="<?=$this->data['measuring']['m_1_0_063_2']?>">
                </td>
                <td>
                    <input class="form-control" type="number" step="any" name="a_0_063_2" value="<?=$this->data['measuring']['a_0_063_2']?>">
                </td>
            </tr>
        </tbody>
    </table>

    <button type="button" id="calc" class="btn btn-primary calculate-average me-2">Рассчитать</button>
    <button type="submit" class="btn" class="btn btn-primary save">Сохранить</button>
</div>
