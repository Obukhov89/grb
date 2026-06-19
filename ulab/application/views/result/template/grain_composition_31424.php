<div class="measurement-wrapper" id="grainWrapper">
	<h3 class="mb-3">зернового состава и модуля крупности ГОСТ 8735-88 п.3</h3>
	<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][type]" value="grain">
	<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][zern]" value="212">
	<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][ugtp_id]" value="<?=$this->data['ugtp_id']?>">
	<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][method_id]" value="<?=$this->data['method_id']?>">

	<div class="wrapper-mass mb-4">
		<div class="form-group row">
			<div class="col">
				<label for="total_mass">Общая масса, г</label>
				<input type="number" id="total_mass" class="form-control total-mass" name="form_data[<?=$this->data['ugtp_id']?>][total_mass]" min="0"
					   step="0.01" value="<?=$this->data['measuring']['total_mass'] ?? 2000 ?>">
			</div>
			<div class="col">
				<label for="mass_on_sieve">Масса на каждом сите, г</label>
				<input type="number" id="mass_on_sieve" class="form-control mass-on-sieve" name="form_data[<?=$this->data['ugtp_id']?>][mass_on_sieve]" min="0"
					   step="0.01" value="<?=$this->data['measuring']['mass_on_sieve'] ?? 1000 ?>">
			</div>
		</div>
	</div>

	<table class="table table-striped measuring-sheet list_data mb-4">
		<thead>
		<tr class="table-info">
			<th scope="col" class="text-nowrap">Диаметр отверстий сит, мм</th>
			<th scope="col">10</th><input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][0]" value="10">
			<th scope="col">5</th><input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][1]" value="5">
			<th scope="col">2,5</th> <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][2]" value="2,5">
			<th scope="col">1,25</th> <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][3]" value="1,25">
			<th scope="col">0,63</th> <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][4]" value="0,63">
			<th scope="col">0,315</th> <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][5]" value="0,315">
			<th scope="col">0,16</th> <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][6]" value="0,16">
			<th scope="col">менее 0,16</th> <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][7]" value="Менее 0,16">
			<!--<th scope="col">0,05</th>
			<th scope="col" class="text-nowrap">&lt; 0,05</th>-->
		</tr>
		</thead>
		<tbody>
        <?php for($k = 0; $k < 8; $k++):?>
        <input class="form-check-input" type="hidden" value="<?=$k?>" id="checkbox_<?=$k?>"
               name="form_data[<?=$this->data['ugtp_id']?>][in_protocol][<?=$k?>]">
        <?php endfor;?>
		<tr>
			<th scope="row" class="text-nowrap">Частный остаток, г</th>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-10" data-sieve="10"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][0] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-5" data-sieve="5"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][1] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-2_5" data-sieve="2.5"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][2] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-1_25" data-sieve="1.25"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][3] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-0_63" data-sieve="0.63"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][4] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-0_315" data-sieve="0.315"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][5] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-0_16" data-sieve="0.16"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][6] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-0_071" data-sieve="0.071"
					   name="form_data[<?=$this->data['ugtp_id']?>][m][]" step="0.01" min="0"
					   value="<?=$this->data['measuring']['m'][7] ?? '' ?>">
			</td>
			<!--<td>
				<input type="number" class="form-control private-remainder private-remainder-0_05" data-sieve="0.05" name="private_remainder[0.05]" step="0.01" min="0" required value="< ?= $plSand8735FinenessModule['private_remainder_0_05'] ?? '' ?>">
			</td>
			<td>
				<input type="number" class="form-control private-remainder private-remainder-less_0_05" data-sieve="less_0.05" name="private_remainder[less_0.05]" step="0.01" min="0" required value="< ?= $plSand8735FinenessModule['private_remainder_less_0_05'] ?? '' ?>">
			</td>-->
		</tr>
		<tr>
			<th scope="row" class="text-nowrap">ЧО на ситах, % по массе</th>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-10"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="10" step="any"
					   value="<?=$this->data['measuring']['a'][0] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-5"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="5" step="any"
					   value="<?=$this->data['measuring']['a'][1] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-2_5"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="2.5" step="any"
					   value="<?=$this->data['measuring']['a'][2] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-1_25"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="1.25" step="any"
					   value="<?=$this->data['measuring']['a'][3] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_63"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="0.63" step="any"
					   value="<?=$this->data['measuring']['a'][4] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_315"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="0.315" step="any"
					   value="<?=$this->data['measuring']['a'][5] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_16"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="0.16" step="any"
					   value="<?=$this->data['measuring']['a'][6] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_071"
					   name="form_data[<?=$this->data['ugtp_id']?>][a][]" data-sieve="0.071" step="any"
					   value="<?=$this->data['measuring']['a'][7] ?? '' ?>" >
			</td>
			<!--<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_05"
				name="private_remainder_by_mass[0_05]" data-sieve="0.05" value="< ?= $plSand8735FinenessModule['private_remainder_by_mass_0_05'] ?? '' ?>" readonly>
			</td>
			<td>
				<input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-less_0_05" name="private_remainder_by_mass[less_0_05]" data-sieve="less_0.05" value="< ?= $plSand8735FinenessModule['private_remainder_by_mass_less_0_05'] ?? '' ?>" readonly>
			</td>-->
		</tr>
		<tr>
			<th scope="row" class="text-nowrap">ПО на ситах, % по массе</th>
			<td>
				<input type="number" class="form-control total-remainders-by-mass total-remainders-by-mass-10"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="10" step="any"
					   value="<?=$this->data['measuring']['p'][0] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-5"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="5" step="any"
					   value="<?=$this->data['measuring']['p'][1] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-2_5"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="2.5" step="any"
					   value="<?=$this->data['measuring']['p'][2] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-1_25"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="1.25" step="any"
					   value="<?=$this->data['measuring']['p'][3] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_63"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="0.63" step="any"
					   value="<?=$this->data['measuring']['p'][4] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_315"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="0.315" step="any"
					   value="<?=$this->data['measuring']['p'][5] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_16"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="0.16" step="any"
					   value="<?=$this->data['measuring']['p'][6] ?? '' ?>" >
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_071"
					   name="form_data[<?=$this->data['ugtp_id']?>][p][]" data-sieve="0.071" step="any"
					   value="<?=$this->data['measuring']['p'][7] ?? '' ?>" >
			</td>
			<!--<td>
				<input type="number" class="form-control total-remainder-by-mass" name="total_remainder_by_mass[0_05]" data-sieve="0.05" value="< ?= $plSand8735FinenessModule['total_remainder_by_mass_0_05'] ?? '' ?>" readonly>
			</td>
			<td>
				<input type="number" class="form-control total-remainder-by-mass" name="total_remainder_by_mass[less_0_05]" data-sieve="less_0.05" value="< ?= $plSand8735FinenessModule['total_remainder_by_mass_less_0_05'] ?? '' ?>" readonly>
			</td>-->
		</tr>
		</tbody>
	</table>

	<div class="form-group row mb-4">
        <div class="col">
            <label for="finenessModule">Модуль крупности</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control"
                       name="form_data[<?= $this->data['ugtp_id'] ?>][tech_fineness_module]"
                       placeholder="Введите Нормативный документ"
                       value="<?= $this->data['measuring']['tech_fineness_module'] ?? '' ?>">
                <span class="input-group-text">/</span>
                <input type="text" class="form-control"
                       name="form_data[<?= $this->data['ugtp_id'] ?>][spec_fineness_module]"
                       placeholder="Введите характеристику"
                       value="<?= $this->data['measuring']['spec_fineness_module'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="text" class="form-control"
                       name="form_data[<?= $this->data['ugtp_id'] ?>][norm_fineness_module]"
                       placeholder="Введите нормативное значение"
                       value="<?= $this->data['measuring']['norm_fineness_module'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="number" step="any" class="form-control fineness-module" placeholder="Введите значение"
                       name="form_data[<?= $this->data['ugtp_id'] ?>][fineness_module]"
                       value="<?= $this->data['measuring']['fineness_module'] ?? '' ?>">
                <span class="input-group-text">Соответствие:</span>
                <select class="form-select" name="form_data[<?= $this->data['ugtp_id'] ?>][match_fineness_module]">
                    <option value="0">-</option>
                    <option value="1">Соответствует</option>
                    <option value="2">Не соответствует</option>
                </select>
            </div>
        </div>
	</div>
	<div class="form-group row mb-4">
		<div class="col">
			<label for="finenessModule">Полный остаток на сите 0,63</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][tech_p063]"
                       placeholder="Введите Нормативный документ" value="<?= $this->data['measuring']['tech_p063'] ?? '' ?>">
                <span class="input-group-text">/</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][spec_p063]"
                       placeholder="Введите характеристику" value="<?= $this->data['measuring']['spec_p063'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][norm_p063]"
                       placeholder="Введите нормативное значение" value="<?= $this->data['measuring']['norm_p063'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="number" step="any" class="form-control p063" placeholder="Введите значение"
                       name="form_data[<?= $this->data['ugtp_id'] ?>][p063]"
                       value="<?= $this->data['measuring']['p063'] ?? '' ?>">
                <span class="input-group-text">Соответствие:</span>
                <select class="form-select" name="form_data[<?= $this->data['ugtp_id'] ?>][match_p063]">
                    <option value="0">-</option>
                    <option value="1">Соответствует</option>
                    <option value="2">Не соответствует</option>
                </select>
            </div>
		</div>
	</div>
	<div class="form-group row mb-4">
		<div class="col">
			<label for="finenessModule">Содержание зерен крупностью св. 10 мм</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][tech_z10]"
                       placeholder="Введите Нормативный документ" value="<?= $this->data['measuring']['tech_z10'] ?? '' ?>">
                <span class="input-group-text">/</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][spec_z10]"
                       placeholder="Введите характеристику" value="<?= $this->data['measuring']['spec_z10'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][norm_z10]"
                       placeholder="Введите нормативное значение" value="<?= $this->data['measuring']['norm_z10'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="number" step="any" class="form-control z10" placeholder="Введите значение"
                       name="form_data[<?= $this->data['ugtp_id'] ?>][z10]"
                       value="<?= $this->data['measuring']['z10'] ?? '' ?>">
                <span class="input-group-text">Соответствие:</span>
                <select class="form-select" name="form_data[<?= $this->data['ugtp_id'] ?>][match_z10]">
                    <option value="0">-</option>
                    <option value="1">Соответствует</option>
                    <option value="2">Не соответствует</option>
                </select>
            </div>
		</div>
	</div>
    <div class="form-group row mb-4">
        <div class="col">
            <label for="finenessModule">Содержание зерен крупностью св. 5 мм</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][tech_z5]"
                       placeholder="Введите Нормативный документ"
                       value="<?= $this->data['measuring']['tech_z5'] ?? '' ?>">
                <span class="input-group-text">/</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][spec_z5]"
                       placeholder="Введите характеристику" value="<?= $this->data['measuring']['spec_z5'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][norm_z5]"
                       placeholder="Введите нормативное значение" value="<?= $this->data['measuring']['norm_z5'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="number" step="any" class="form-control z5"
                       placeholder="Введите значение" name="form_data[<?= $this->data['ugtp_id'] ?>][z5]"
                       value="<?= $this->data['measuring']['z5'] ?? '' ?>">
                <span class="input-group-text">Соответствие:</span>
                <select class="form-select" name="form_data[<?= $this->data['ugtp_id'] ?>][match_z5]"
                >
                    <option value="0">-</option>
                    <option value="1">Соответствует</option>
                    <option value="2">Не соответствует</option>
                </select>
            </div>
        </div>
    </div>
	<div class="form-group row mb-4">
		<div class="col">
			<label for="finenessModule">Содержание зерен крупностью менее 0,16 мм</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][tech_z016]"
                       placeholder="Введите Нормативный документ" value="<?= $this->data['measuring']['tech_z016'] ?? '' ?>">
                <span class="input-group-text">/</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][spec_z016]"
                       placeholder="Введите характеристику" value="<?= $this->data['measuring']['spec_z016'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="text" class="form-control" name="form_data[<?= $this->data['ugtp_id'] ?>][norm_z016]"
                       placeholder="Введите нормативное значение" value="<?= $this->data['measuring']['norm_z016'] ?? '' ?>">
                <span class="input-group-text">:</span>
                <input type="number" step="any" class="form-control z016" placeholder="Введите значение"
                       name="form_data[<?= $this->data['ugtp_id'] ?>][z016]"
                       value="<?= $this->data['measuring']['z016'] ?? '' ?>">
                <span class="input-group-text">Соответствие:</span>
                <select class="form-select" name="form_data[<?= $this->data['ugtp_id'] ?>][match_z016]">
                    <option value="0">-</option>
                    <option value="1">Соответствует</option>
                    <option value="2">Не соответствует</option>
                </select>
            </div>
		</div>
	</div>
	<div class="row mb-4">
		<div class="col flex-grow-0">
			<button type="button"  id="finenessModuleCalculate" class="btn btn-primary fineness-module-calculate"
					name="fineness_module_calculate">Расчитать
			</button>
		</div>
		<div class="col">
			<?php if (true): ?>
				<button type="submit" class="btn btn-primary save" name="fineness_modulus_save">Сохранить</button>
			<?php else: ?>
				<div class="form-control text-center text-uppercase bg-light-secondary">Сохранить</div>
			<?php endif; ?>
		</div>
	</div>
</div>
