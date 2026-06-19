<form id="measuring-sheet-form" method="post">
	<div class="average-sample-density-wrapper">
		<div class="information wrepper-shadow mb-4">
			<!--<div class="row mb-4">
                <div class="form-group col-sm-6">
                    <lable for="testDate">Дата испытания</lable>
                    <input type="date" class="form-control test-date w-100" id="testDate" name="test_date" value="<?/*= $test ?? '' */?>">
                </div>
                <div class="form-group col-sm-6">
                    <lable for="fellingDate">Дата взятия вырубки(керна)</lable>
                    <input type="date" class="form-control felling-date w-100" id="fellingDate" name="felling_date" value="<?/*= $test ?? '' */?>">
                </div>
            </div>-->
			<h2 class="mb-4">Информация об испытаниях</h2>

			<div class="row">
				<div class="form-group col-sm-6 w-50">
					<lable for="actualThickness">Фактическая толщина, см</lable>
					<input type="number" class="form-control actual-thickness w-100" id="actualThickness" name="actual_thickness" step="0.01" value="<?= $test ?? '' ?>">
				</div>
				<div class="form-group col-sm-6 w-50">
					<lable for="adhesionToBase">Сцепление с нижним слоем, с основанием</lable>
					<input type="text" class="form-control adhesion-to-base w-100" id="adhesionToBase" name="adhesion_to_base" value="<?= $test ?? '' ?>">
				</div>
			</div>
			<div class="row">
				<div class="form-group col-sm-6 w-50">
					<lable for="view">Вид асфальтобетонной смеси</lable>
					<input type="text" class="form-control view w-100" id="view" name="view" value="<?= $test ?? '' ?>">
				</div>
				<div class="form-group col-sm-6 w-50">
					<lable for="type">Тип асфальтобетонной смеси</lable>
					<input type="text" class="form-control type w-100" id="type" name="type" value="<?= $test ?? '' ?>">
				</div>
			</div>
			<div class="row">
				<div class="form-group col-sm-6">
					<lable for="brand">Марка асфальтобетонной смеси</lable>
					<input type="text" class="form-control brand w-100" id="brand" name="brand" value="<?= $test ?? '' ?>">
				</div>
			</div>
		</div>

		<div class="coating-samples wrepper-shadow mb-4">
			<h2 class="mb-4">Образцы из покрытия</h2>

			<!--<div class="row">
                <div class="form-group col-sm-6">
                    <lable for="massAfterSaturation">Масса образца на воздухе после водонасыщения</lable>
                    <input type="number" class="form-control mass-after-saturation w-100" id="massAfterSaturation" name="mass_after_saturation" step="0.01" value="<?/*= $test ?? '' */?>">
                </div>
            </div>-->

			<table class="table table-striped measuring-sheet average-sample-density-table table-fixed list_data mb-4">
				<thead>
				<tr class="table-info">
					<th scope="col">Образец</th>
					<th scope="col">Масса образца, взвешенного на воздухе g, г</th>
					<th scope="col">Плотность воды, равная 1 г/см<sup>3</sup></th>
					<th scope="col">Масса образца, взвешенного в воде g<sub>1</sub>, г</th>
					<th scope="col">Масса образца, выдержанного в течение 30 мин в воде и вторично взвешенного на воздухе g<sub>2</sub>, г</th>
					<th scope="col">Масса насыщенного водой образца, взвешенного на воздухе g<sub>5</sub>, г</th>
					<th scope="col">Средняя плотность образца</th>
					<th scope="col">Водонасыщение образца</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach (SAMPLES as $val): ?>
					<tr>
						<th scope="row" class="text-nowrap"><?= $val ?></th>
						<td>
							<input type="number" id="sampleWeightInAir<?= $val ?>" class="form-control sample-weight-in-air sample-weight-in-air-<?= $val ?>" name="sample_weight_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['sample_weight_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="densityWater<?= $val ?>" class="form-control density-water density-water-<?= $val ?>" name="density_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['density_water_' . $val] ?? '1' ?>">
						</td>
						<td>
							<input type="number" id="sampleWeightInWater<?= $val ?>" class="form-control sample-weight-in-water sample-weight-in-water-<?= $val ?>" name="sample_weight_in_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['sample_weight_in_water_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="massSampleReWeighedInAir<?= $val ?>" class="form-control mass-sample-re-weighed-in-air mass-sample-re-weighed-in-air-<?= $val ?>" name="mass_sample_re_weighed_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['mass_sample_re_weighed_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="waterSaturatedSampleMass<?= $val ?>" class="form-control water-saturated-sample-mass water-saturated-sample-mass-<?= $val ?>" name="water_saturated_sample_mass[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['water_saturated_sample_mass' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="averageSampleDensity<?= $val ?>" class="form-control average-sample-density average-sample-density-<?= $val ?>" name="average_sample_density[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['average_sample_density' . $val] ?? '' ?>" readonly>
						</td>
						<td>
							<input type="number" id="waterSaturation<?= $val ?>" class="form-control water-saturation water-saturation-<?= $val ?>" name="water_saturation[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['water_saturation' . $val] ?? '' ?>" readonly>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tbody class="repeated-tests <?= $show ? '' : 'd-none' ?>">
				<?php foreach (SAMPLES_FOR_RETESTING as $val): ?>
					<tr>
						<th scope="row" class="text-nowrap"><?= $val ?></th>
						<td>
							<input type="number" id="sampleWeightInAir<?= $val ?>" class="form-control sample-weight-in-air sample-weight-in-air-<?= $val ?>" name="sample_weight_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['sample_weight_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="densityWater<?= $val ?>" class="form-control density-water density-water-<?= $val ?>" name="density_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['density_water_' . $val] ?? '1' ?>">
						</td>
						<td>
							<input type="number" id="sampleWeightInWater<?= $val ?>" class="form-control sample-weight-in-water sample-weight-in-water-<?= $val ?>" name="sample_weight_in_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['sample_weight_in_water_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="massSampleReWeighedInAir<?= $val ?>" class="form-control mass-sample-re-weighed-in-air mass-sample-re-weighed-in-air-<?= $val ?>" name="mass_sample_re_weighed_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['mass_sample_re_weighed_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="waterSaturatedSampleMass<?= $val ?>" class="form-control water-saturated-sample-mass water-saturated-sample-mass-<?= $val ?>" name="water_saturated_sample_mass[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['water_saturated_sample_mass' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="averageSampleDensity<?= $val ?>" class="form-control average-sample-density average-sample-density-<?= $val ?>" name="average_sample_density[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['average_sample_density' . $val] ?? '' ?>" readonly>
						</td>
						<td>
							<input type="number" id="waterSaturation<?= $val ?>" class="form-control water-saturation water-saturation-<?= $val ?>" name="water_saturation[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['water_saturation' . $val] ?? '' ?>" readonly>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<div class="form-group row mb-4">
				<div class="col">
					<label for="arithmeticMeanDensity">Среднеарифметическое значение результатов средней плотности</label>
					<input type="number" id="arithmeticMeanDensity" class="form-control arithmetic-mean-density" name="arithmetic_mean_density" step="0.01" value="<?= $plSand8735TrueDensity['arithmetic_mean_density'] ?? '' ?>" readonly>
				</div>
				<div class="col">
					<label for="arithmeticMeanWaterSaturation">Среднеарифметическое значение результатов водонасыщения</label>
					<input type="number" id="arithmeticMeanWaterSaturation" class="form-control arithmetic-mean-water-saturation" name="arithmetic_mean_water_saturation" step="0.01" value="<?= $plSand8735TrueDensity['arithmetic_mean_water_saturation'] ?? '' ?>" readonly>
				</div>
			</div>

			<div class="row mb-4">
				<div class="col">
					<button type="button" id="сalculateAverageSampleDensity" class="btn btn-primary сalculate-average-sample-density w-100 mw-100" name="сalculate_average_sample_density">Расчитать</button>
				</div>
			</div>
		</div>
		<!-- /.coating-sample -->

		<div class="overmolded-samples wrepper-shadow">
			<h2 class="mb-4">Переформованные образцы</h2>

			<!--<div class="form-group col-sm-6">
                <lable for="overmoldedMassAfterSaturation">Масса образца на воздухе после водонасыщения</lable>
                <input type="number" class="form-control overmolded-mass-after-saturation w-100" id="overmoldedMassAfterSaturation" name="overmolded_mass_after_saturation" step="0.01" value="<?/*= $test ?? '' */?>">
            </div>-->

			<table class="table table-striped measuring-sheet overmolded-table table-fixed list_data mb-4">
				<thead>
				<tr class="table-info">
					<th scope="col">Образец</th>
					<th scope="col">Масса образца, взвешенного на воздухе, г</th>
					<th scope="col">Плотность воды, равная 1 г/см<sup>3</sup></th>
					<th scope="col">Масса образца, взвешенного в воде, г</th>
					<th scope="col">Масса образца, выдержанного в течение 30 мин в воде и вторично взвешенного на воздухе, г</th>
					<th scope="col">Масса насыщенного водой образца, взвешенного на воздухе g<sub>5</sub>, г</th>
					<th scope="col">Средняя плотность образца</th>
					<th scope="col">Водонасыщение образца</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach (SAMPLES as $val): ?>
					<tr>
						<th scope="row" class="text-nowrap"><?= $val ?></th>
						<td>
							<input type="number" id="overmoldedSampleWeightInAir<?= $val ?>" class="form-control overmolded-sample-weight-in-air overmolded-sample-weight-in-air-<?= $val ?>" name="overmolded_sample_weight_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_sample_weight_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedDensityWater<?= $val ?>" class="form-control overmolded-density-water overmolded-density-water-<?= $val ?>" name="overmolded_density_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_density_water_' . $val] ?? '1' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedSampleWeightInWater<?= $val ?>" class="form-control overmolded-sample-weight-in-water overmolded-sample-weight-in-water-<?= $val ?>" name="overmolded_sample_weight_in_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_sample_weight_in_water_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedMassSampleReWeighedInAir<?= $val ?>" class="form-control overmolded-mass-sample-re-weighed-in-air overmolded-mass-sample-re-weighed-in-air-<?= $val ?>" name="overmolded_mass_sample_re_weighed_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_mass_sample_re_weighed_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedWaterSaturatedSampleMass<?= $val ?>" class="form-control overmolded-water-saturated-sample-mass overmolded-water-saturated-sample-mass-<?= $val ?>" name="overmolded_water_saturated_sample_mass[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_water_saturated_sample_mass' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedAverageSampleDensity<?= $val ?>" class="form-control overmolded-average-sample-density overmolded-average-sample-density-<?= $val ?>" name="overmolded_average_sample_density[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_average_sample_density' . $val] ?? '' ?>" readonly>
						</td>
						<td>
							<input type="number" id="overmoldedWaterSaturation<?= $val ?>" class="form-control overmolded-water-saturation overmolded-water-saturation-<?= $val ?>" name="overmolded_water_saturation[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_water_saturation' . $val] ?? '' ?>" readonly>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tbody class="overmolded-repeated-tests <?= $show ? '' : 'd-none' ?>">
				<?php foreach (SAMPLES_FOR_RETESTING as $val): ?>
					<tr>
						<th scope="row" class="text-nowrap"><?= $val ?></th>
						<td>
							<input type="number" id="overmoldedSampleWeightInAir<?= $val ?>" class="form-control overmolded-sample-weight-in-air overmolded-sample-weight-in-air-<?= $val ?>" name="overmolded_sample_weight_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_sample_weight_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedDensityWater<?= $val ?>" class="form-control overmolded-density-water overmolded-density-water-<?= $val ?>" name="overmolded_density_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_density_water_' . $val] ?? '1' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedSampleWeightInWater<?= $val ?>" class="form-control overmolded-sample-weight-in-water overmolded-sample-weight-in-water-<?= $val ?>" name="overmolded_sample_weight_in_water[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_sample_weight_in_water_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedMassSampleReWeighedInAir<?= $val ?>" class="form-control overmolded-mass-sample-re-weighed-in-air overmolded-mass-sample-re-weighed-in-air-<?= $val ?>" name="overmolded_mass_sample_re_weighed_in_air[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_mass_sample_re_weighed_in_air_' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedWaterSaturatedSampleMass<?= $val ?>" class="form-control overmolded-water-saturated-sample-mass overmolded-water-saturated-sample-mass-<?= $val ?>" name="overmolded_water_saturated_sample_mass[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_water_saturated_sample_mass' . $val] ?? '' ?>">
						</td>
						<td>
							<input type="number" id="overmoldedAverageSampleDensity<?= $val ?>" class="form-control overmolded-average-sample-density overmolded-average-sample-density-<?= $val ?>" name="overmolded_average_sample_density[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_average_sample_density' . $val] ?? '' ?>" readonly>
						</td>
						<td>
							<input type="number" id="overmoldedWaterSaturation<?= $val ?>" class="form-control overmolded-water-saturation overmolded-water-saturation-<?= $val ?>" name="overmolded_water_saturation[<?= $val ?>]" data-trial="<?= $val ?>" min="0" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_water_saturation' . $val] ?? '' ?>" readonly>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<div class="form-group row mb-4">
				<div class="col">
					<label for="overmoldedArithmeticMeanDensity">Среднеарифметическое значение результатов средней плотности</label>
					<input type="number" id="overmoldedArithmeticMeanDensity" class="form-control overmolded-arithmetic-mean-density" name="overmolded_arithmetic_mean_density" data-trial="2" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_arithmetic_mean_density'] ?? '' ?>" readonly>
				</div>
				<div class="col">
					<label for="overmoldedArithmeticMeanWaterSaturation">Среднеарифметическое значение результатов водонасыщения</label>
					<input type="number" id="overmoldedArithmeticMeanWaterSaturation" class="form-control overmolded-arithmetic-mean-water-saturation" name="overmolded_arithmetic_mean_water_saturation" data-trial="2" step="0.01" value="<?= $plSand8735TrueDensity['overmolded_arithmetic_mean_water_saturation'] ?? '' ?>" readonly>
				</div>
			</div>

			<div class="row mb-4">
				<div class="col">
					<button type="button" id="сalculateOvermoldedAverageSampleDensity" class="btn btn-primary сalculate-overmolded-average-sample-density w-100 mw-100" name="сalculate_overmolded_average_sample_density">Расчитать</button>
				</div>
			</div>
		</div>
		<!-- /.overmolded-samples -->

		<div class="line-dashed"></div>

		<div class="form-group row mb-4">
			<div class="col">
				<label for="compactionFactor">Коэффициент уплотнения</label>
				<input type="number" id="compactionFactor" class="form-control compaction-factor" name="compaction_factor" step="0.01" value="<?= $plSand8735TrueDensity['compaction_factor'] ?? '' ?>" readonly>
			</div>
		</div>

		<div class="row mb-4">
			<div class="col">
				<?php if ( true ): ?>
					<button type="submit" class="btn btn-primary save w-100 mw-100" name="average_sample_density_save">Сохранить</button>
				<?php else: ?>
					<div class="text-center text-uppercase bg-light-secondary">Сохранить</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<!-- /.average-sample-density-wrapper -->
</form>
