<div class="measurement-wrapper">
    <h3 class="mb-3">Результаты спектрометра</h3>
    <div>
        <table class="table table-fixed align-middle text-center mb-3">
            <thead>
            <tr class="table-info">
                <th scope="col"></th>
                <th scope="col">Испытание 1</th>
                <th scope="col">Испытание 2</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Файл</td>
                <td><input id="parse-file_1" data-file="test-1" type="file" class="form-control parse-file" name="file" value=""></td>
                <td><input id="parse-file_2" data-file="test-2" type="file" class="form-control parse-file" name="file" value="" disabled></td>
                <td><button type="button" class="btn btn-primary clear-all">Очистить всё</button></td>
            </tr>
            <tr>
                <td>Название</td>
                <td><input type="text" class="form-control name-test-1" name="form[file_name_1]" value="<?=$this->data['measuring']['form']['file_name_1']?? ''?>"></td>
                <td><input type="text" class="form-control name-test-2" name="form[file_name_2]" value="<?=$this->data['measuring']['form']['file_name_2']?? ''?>"></td>
                <td></td>
            </tr>
            </tbody>
        </table>

        <table class="table table-fixed align-middle text-center parse-table mb-3">
            <thead>
            <tr class="table-info">
                <th scope="col">Компонент</th>
                <th scope="col">Массовая доля компонента (Испытание 1), %</th>
                <th scope="col">Массовая доля компонента (Испытание 2), %</th>
                <th scope="col">Предел повторяемости</th>
                <th scope="col">Среднее</th>
            </tr>
            </thead>
            <?php foreach ($this->data['measuring']['form']['results'] as $i => $row): ?>
                <tr class="tr-results" data-element="<?=$row['element']?>">
                    <td><input type="text" class="form-control" name="form[results][<?=$i?>][element]" value="<?=$row['element']?>" readonly></td>
                    <td><input type="text" class="form-control result-test-1" name="form[results][<?=$i?>][result_test_1]" value="<?=$row['result_test_1']?>" readonly></td>
                    <td><input type="text" class="form-control result-test-2" name="form[results][<?=$i?>][result_test_2]" value="<?=$row['result_test_2']?>" readonly></td>
                    <td><input type="text" class="form-control result-delta" name="form[results][<?=$i?>][result_delta]" value="<?=$row['result_delta']?>" readonly></td>
                    <td><input type="text" class="form-control result-average" name="result_value" value="<?=$this->data['measuring']['result_value']?>" readonly></td>
                </tr>
            <?php endforeach; ?>
            <tbody>
            </tbody>
        </table>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</div>