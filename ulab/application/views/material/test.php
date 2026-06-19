<div class="panel panel-default">
	<header class="panel-heading">
		Схема для материала
		<span class="tools float-end">
            <a href="#" class="fa fa-chevron-down"></a>
         </span>
	</header>
	<div class="panel-body" style="display: none;">
		<datalist id="gost-arr">
			<?php foreach ($this->data['gost'] as $gost): ?>
				<option data-value="<?= $gost['ID'] ?>"><?=$gost['view_gost']?></option>
			<?php endforeach; ?>
		</datalist>
		<div class="row">
			<div class="d-flex align-items-start">
				<div class="nav flex-column nav-pills me-3" style="width: 25%;" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<?php foreach ($this->data['scheme'] as $item): ?>
						<button class="btn btn-outline-secondary nav-link mw-100 mt-0 mb-1" id="v-pills-<?= $item['id'] ?>-tab" data-bs-toggle="pill"
								data-bs-target="#v-pills-<?= $item['id'] ?>" type="button" role="tab"
								aria-controls="v-pills-<?= $item['id'] ?>"
								aria-selected="true"><?= $item['name_scheme'] ?></button>
					<?php endforeach; ?>
						<button class="btn btn-primary mw-100 mt-0" type="button" id="v-pills-<?= $item['id'] ?>-tab" data-bs-toggle="pill"
								data-bs-target="#v-pills-new" role="tab"
								aria-controls="v-pills-new"
								aria-selected="true"><i class="fa-solid fa-plus"></i></button>
				</div>
				<div class="tab-content w-100" id="v-pills-tabContent">
					<?php foreach ($this->data['scheme'] as $scheme): ?>
						<div class="tab-pane fade scheme-info" id="v-pills-<?=$scheme['id']?>" role="tabpanel"
							 aria-labelledby="v-pills-<?=$scheme['id']?>-tab" tabindex="0">
							<input type="hidden" name="scheme_id" value="<?=$scheme['id']?>">
							<input type="hidden" name="material_id" value="<?=$this->data['id']?>">
							<div class="mb-3">
								<button type="button" class="btn btn-danger float-end mb-1 delete-scheme"><i class="fa-solid fa-minus"></i> Удалить схему</button>
								<label for="scheme-name-<?=$scheme['id']?>" class="form-label">Название схемы:</label>
								<input type="text" name="scheme-param" class="form-control" id="scheme-name-<?=$scheme['id']?>" placeholder="Введите название схемы" value="<?=$scheme['name_scheme']?>">
							</div>
							<div class="form-group">
								<input type="checkbox" name="united_scheme" class="btn-check united-method" id="united-method-<?= $scheme['id']?>"
									   autocomplete="off">
								<label class="btn btn-outline-primary mt-0" for="united-method-<?= $scheme['id']?>"
									<?= $scheme['united_method'] == 1 ? 'checked' : '' ?>>Объединение методик</label>
							</div>
							<div class="row united-scheme-div <?= $scheme['united_method'] == 1 ? '' : 'visually-hidden'?>">
								<div class="form-group price_for_scheme col-4">
									<div class="input-group">
										<input type="number" min="0" step="0.01" class="form-control"
											   name="price_scheme"
											   value="<?=$scheme['price']?>"
											<?= $scheme['united_method'] == 1 ? '' : 'disabled'?>>
										<span class="input-group-text">руб</span>
									</div>
								</div>
								<div class="form-group unit_for_scheme col-4"
									 style="padding-left: 0.7rem">
									<select name="unit_scheme_unit" class="form-select" style="width: 65%"
										<?= $scheme['united_method'] == 1 ? '' : 'disabled' ?>>
										<?php foreach ($this->data['unit'] as $units): ?>
											<option
												<?= $scheme['unit'] == $units['id'] ? 'selected' : '' ?>
												value="<?= $units['id'] ?>">
												<?= $units['name'] ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="line-dashed-small"></div>
							<table class="table table-striped table-gost">
								<thead>
								<th>ГОСТ</th>
								<th>Пункт</th>
								<th>Определяемая характеристика</th>
								<th style="width: 5%"></th>
								</thead>
								<tbody>
								<?php foreach ($scheme['gost'] as $gost):?>
									<tr class="tr-gost">
										<td>
											<?=$gost['name_gost']?>
											<input type="hidden" class="hidden-gost" name="gost_id[]" value="<?=$gost['id_gost']?>">
										</td>
										<td><?=$gost['param']?></td>
										<td><?=$gost['spec']?></td>
										<td><a class="del-tr" style="color: black"><i class="fa-solid fa-trash-can"></a></i></td>
									</tr>
								<?php endforeach;?>
								<tr class="new-gost">
									<td><button class="btn btn-primary add-new-gost"><i class="fa-solid fa-plus"></i> Добавить ГОСТ</button></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								</tbody>
							</table>
							<div class="line-dashed-small"></div>
							<button class="btn btn-primary edit-scheme">Сохранить</button>
						</div>
					<?php endforeach; ?>
					<div class="tab-pane fade scheme-info" id="v-pills-new" role="tabpanel"
						 aria-labelledby="v-pills-new-tab" tabindex="0">
						<input type="hidden" name="scheme_id" value="">
						<input type="hidden" name="material_id" value="<?=$this->data['id']?>">
						<div class="mb-3">
							<label for="scheme-name-new" class="form-label">Название схемы:</label>
							<input type="text" class="form-control" id="scheme-name-new" name="scheme-param" placeholder="Введите название схемы">
						</div>
						<div class="form-group">
							<input type="checkbox" class="btn-check united-method" id="united-method-new" name="united_scheme"
								   autocomplete="off">
							<label class="btn btn-outline-primary mt-0" for="united-method-new">Объединение методик</label>
						</div>
						<div class="row united-scheme-div visually-hidden">
							<div class="form-group price_for_scheme col-4">
								<div class="input-group">
									<input type="number" min="0" step="0.01" class="form-control"
										   name="price_scheme"
										   placeholder="введите цену схемы" disabled>
									<span class="input-group-text">руб</span>
								</div>
							</div>
							<div class="form-group unit_for_scheme col-4"
								 style="padding-left: 0.7rem">
								<select name="unit_scheme_unit" class="form-select" style="width: 65%" disabled>
									<?php foreach ($this->data['unit'] as $units): ?>
										<option value="<?= $units['id'] ?>">
											<?= $units['name'] ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="line-dashed-small"></div>
						<table class="table table-striped table-gost">
							<thead>
							<th>ГОСТ</th>
							<th>Пункт</th>
							<th>Определяемая характеристика</th>
							<th style="width: 5%"></th>
							</thead>
							<tbody>
							<tr class="new-gost">
								<td><button class="btn btn-primary add-new-gost"><i class="fa-solid fa-plus"></i> Добавить ГОСТ</button></td>
								<td></td>
								<td></td>
							</tr>
							</tbody>
						</table>
						<div class="line-dashed-small"></div>
						<button class="btn btn-primary edit-scheme">Сохранить</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
			<!--		<div class="list-group col-4">-->
			<!--			--><?php //foreach ($this->data['scheme'] as $item):?>
			<!--				<button type="button" class="list-group-item list-group-item-action" data-id="-->
			<? //=$item['id']?><!--" aria-current="true">-->
			<!--					--><? //=$item['name_scheme']?>
			<!--				</button>-->
			<!--			--><?php //endforeach;?>
			<!--		</div>-->
			<!--		<div class="col-8">-->
			<!--			<div class="row">-->
			<!--				<div class="form-check col-3">-->
			<!--					<input class="form-check-input" type="checkbox" value="" id="united_method">-->
			<!--					<label class="form-check-label" for="united_method">-->
			<!--						Объединение методик-->
			<!--					</label>-->
			<!--				</div>-->
			<!--				<div class="form-group price_for_scheme col-4">-->
			<!--						<div class="input-group">-->
			<!--							<input type="float" min="0" step="0.01" class="form-control"-->
			<!--								   name="price_scheme"-->
			<!--								   value="">-->
			<!--							<span class="input-group-text">руб</span>-->
			<!--						</div>-->
			<!--				</div>-->
			<!--				<div class="form-group unit_for_scheme col-4"-->
			<!--					 style="padding-left: 0.7rem">-->
			<!--					<select name="unit_scheme" class="form-select" style="width: 65%">-->
			<!--						--><?php //foreach ($this->data['unit'] as $units): ?>
			<!--							<option value="--><? //= $units['id'] ?><!--">-->
			<? //= $units['name'] ?><!--</option>-->
			<!--						--><?php //endforeach; ?>
			<!--					</select>-->
			<!--				</div>-->
			<!--			</div>-->
			<!--			<div class="line-dashed-small"></div>-->
			<!--			<table class="table table-striped">-->
			<!--				<thead>-->
			<!--					<th>ГОСТ</th>-->
			<!--					<th>Пункт</th>-->
			<!--					<th>Определяемая характеристика</th>-->
			<!--					<th style="width: 15%"></th>-->
			<!--				</thead>-->
			<!--				<tbody>-->
			<!---->
			<!--				</tbody>-->
			<!---->
			<!--			</table>-->
			<!---->
			<!--		</div>-->
			<!--		</div>-->

<!--		</div>-->
<!--	</div>-->
<!--</div>-->
