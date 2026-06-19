<div class="measurement-wrapper" id="grainWrapper">
    <h3 class="mb-3">Зерновой состав по ГОСТ 8269.0-97</h3>
    <label for="initial_mass">Масса пробы, г</label>
	<input type="hidden" name="type" value="grain">
    <input class="form-control initial_mass" type="number" name="initial_mass" value="<?=$this->data['measuring']['initial_mass']?>">

    <span>Зерновой состав</span>
    <table class="table list_data graincomposition">
        <thead>
        <tr>
            <th class="text-center">Размер сит, мм</th>
            <th class="text-center">120</th>
            <th class="text-center">80</th>
            <th class="text-center">40</th>
            <th class="text-center">20</th>
            <th class="text-center">10</th>
            <th class="text-center">5</th>
            <th class="text-center">2.5</th>
            <th class="text-center">0.63</th>
            <th class="text-center">0.16</th>
            <th class="text-center">0.05</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-center">m<sub>i</sub></td>
            <td>
                <input class="form-control" type="number" step="any" name="m_120" value="<?=$this->data['measuring']['m_120']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_80" value="<?=$this->data['measuring']['m_80']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_40" value="<?=$this->data['measuring']['m_40']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_20" value="<?=$this->data['measuring']['m_20']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_10" value="<?=$this->data['measuring']['m_10']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_5" value="<?=$this->data['measuring']['m_5']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_25" value="<?=$this->data['measuring']['m_25']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_063" value="<?=$this->data['measuring']['m_063']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_016" value="<?=$this->data['measuring']['m_016']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="m_005" value="<?=$this->data['measuring']['m_005']?>">
            </td>
        </tr>
        <tr>
            <td class="text-center">a<sub>i</sub></td>
            <td>
                <input class="form-control" type="number" step="any" name="a_120" value="<?=$this->data['measuring']['a_120']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_80" value="<?=$this->data['measuring']['a_80']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_40" value="<?=$this->data['measuring']['a_40']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_20" value="<?=$this->data['measuring']['a_20']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_10" value="<?=$this->data['measuring']['a_10']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_5" value="<?=$this->data['measuring']['a_5']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_25" value="<?=$this->data['measuring']['a_25']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_063" value="<?=$this->data['measuring']['a_063']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_016" value="<?=$this->data['measuring']['a_016']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="a_005" value="<?=$this->data['measuring']['a_005']?>">
            </td>
        </tr>
        <tr>
            <td class="text-center">П<sub>i</sub></td>
            <td>
                <input class="form-control" type="number" step="any" name="p_120" value="<?=$this->data['measuring']['p_120']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_80" value="<?=$this->data['measuring']['p_80']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_40" value="<?=$this->data['measuring']['p_40']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_20" value="<?=$this->data['measuring']['p_20']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_10" value="<?=$this->data['measuring']['p_10']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_5" value="<?=$this->data['measuring']['p_5']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_25" value="<?=$this->data['measuring']['p_25']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_063" value="<?=$this->data['measuring']['p_063']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_016" value="<?=$this->data['measuring']['p_016']?>">
            </td>
            <td>
                <input class="form-control" type="number" step="any" name="p_005" value="<?=$this->data['measuring']['p_005']?>">
            </td>
        </tr>
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary save-average">Сохранить</button>
</div>
