<div class="measurement-wrapper" id="grainWrapper">
	<h3 class="mb-3">Зерновой состав общий</h3>
	<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][type]" value="grain">
	<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][method_id]" value="<?=$this->data['measuring_property']['method_id']?>">
	<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][ugtp_id]" value="<?=$this->data['ugtp_id']?>" id="ugtpId">
	<div class="mb-3">
		<input class="form-check-input checkbox_grain" type="checkbox" value="1" name="form_data[<?=$this->data['ugtp_id']?>][checkbox]" <?=$this->data['measuring']['checkbox'] ? 'checked' : ''?>>
		<label class="form-check-label">Выводить только фактическое значение</label>
		<div class="result_value <?=$this->data['measuring']['checkbox'] ? '' : 'visually-hidden'?>">
			<label for="initial_mass">Фактическое значение</label>
			<input class="form-check-input <?=$this->data['measuring']['checkbox'] ? '' : 'disabled'?>" type="number" step="any" value="<?=$this->data['measuring']['result_value']?>" name="form_data[<?=$this->data['ugtp_id']?>][result_value]">
		</div>
	</div>
	<div class="zern <?=$this->data['measuring']['checkbox'] ? 'visually-hidden' : ''?>">
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="materialGroup">Выберите сита</label>
				<select class="form-select material-group" name="form_data[<?=$this->data['ugtp_id']?>][zern]"
					<option value="0" selected>Выбрать</option>
					<?php foreach ($this->data['measuring_property']['sieve'] as $val): ?>
						<option value="<?= $val['ID'] ?>"
							<?= $this->data['measuring']['zern'] === "{$val['ID']}" ? 'selected' : '' ?>>
							<?= $val['NAME'] ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-1 mt-4">
				<a class="no-decoration me-1 popup-with-form zern-link" href="/ulab/grain/card/<?=$this->data['measuring']['zern'] ?? 0?>" title="Заполнить данные по оплате" target="_blank">
					<svg class="icon" width="35" height="35">
						<use xlink:href="/ulab/assets/images/icons.svg#edit"></use>
					</svg>
				</a>
			</div>
		</div>
		<label for="initial_mass">Масса пробы, г</label>
		<input class="form-control initial_mass" type="number" name="form_data[<?=$this->data['ugtp_id']?>][initial_mass]" step="0.01" value="<?=$this->data['measuring']['initial_mass']?>">

		<?php if (!empty($this->data['measuring'])):?>
			<table class="table list_data graincomposition">
				<thead>
				<tr>
					<th class="text-center">Размер сит, мм</th>
					<th class="text-center"></th>
					<?php foreach ($this->data['measuring']['title'] as $k => $title):?>
						<th class="text-center">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="<?=$k?>" id="checkbox_<?=$k?>"
									   name="form_data[<?=$this->data['ugtp_id']?>][in_protocol][<?=$k?>]" <?=$this->data['measuring']['in_protocol'][$k] || $this->data['measuring']['in_protocol'][$k] == '0' ? 'checked' : ''?>>
								<label class="form-check-label" for="checkbox_<?=$k?>">
									<?=$title?>
								</label>
								<input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][title][<?=$k?>]" value="<?=$title?>">
						</th>
					<?php endforeach;?>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="text-center">m<sub>i</sub></td>
					<td class="text-center"></td>
					<?php foreach ($this->data['measuring']['m'] as $k => $m):?>
						<td>
							<input class="form-control calculate <?=$k == 0 ? 'first' : ''?>" data-col="<?=$k?>" type="number" step="any" name="form_data[<?=$this->data['ugtp_id']?>][m][<?=$k?>]" value="<?=$m?>">
						</td>
					<?php endforeach;?>
				</tr>
				<tr>
					<td class="text-center">a<sub>i</sub></td>
					<td class="text-center"><input class="form-check-input" type="checkbox" value="1" id="a" name="form_data[<?=$this->data['ugtp_id']?>][a_in_protocol]" <?=$this->data['measuring']['a_in_protocol'] == 1 ? 'checked' : ''?>></td>
					<?php foreach ($this->data['measuring']['a'] as $k => $a):?>
						<td>
							<input class="form-control <?=$k == 0 ? 'first' : ''?>" data-col="<?=$k?>" type="number" step="any" name="form_data[<?=$this->data['ugtp_id']?>][a][<?=$k?>]" value="<?=$a?>">
						</td>
					<?php endforeach;?>
				</tr>
				<tr>
					<td class="text-center">П<sub>i</sub></td>
					<td class="text-center"><input class="form-check-input" type="checkbox" value="1" id="p" name="form_data[<?=$this->data['ugtp_id']?>][p_in_protocol]" <?=$this->data['measuring']['p_in_protocol'] == 1 ? 'checked' : ''?>></td>
					<?php foreach ($this->data['measuring']['p'] as $k => $p):?>
						<td>
							<input class="form-control <?=$k == 0 ? 'first' : ''?>" data-col="<?=$k?>" type="number" step="any" name="form_data[<?=$this->data['ugtp_id']?>][p][<?=$k?>]" value="<?=$p?>">
						</td>
					<?php endforeach;?>
				</tr>
				<tr>
					<td class="text-center">Требования</td>
					<td class="text-center"><input class="form-check-input" type="checkbox" value="1" id="req" name="form_data[<?=$this->data['ugtp_id']?>][req_in_protocol]" <?=$this->data['measuring']['req_in_protocol'] == 1 ? 'checked' : ''?>></td>
					<?php foreach ($this->data['measuring']['norm'] as $k => $norm):?>
						<td>
							<input class="form-control" type="text" step="any" name="form_data[<?=$this->data['ugtp_id']?>][norm][<?=$k?>]" value="<?=$norm?>">
						</td>
					<?php endforeach;?>
				</tr>
				</tbody>
			</table>
		<?php endif;?>
	</div>
	<button type="submit" class="btn btn-primary save-average">Сохранить</button>
</div>
