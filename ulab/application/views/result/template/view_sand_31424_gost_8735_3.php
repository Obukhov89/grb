<form class="measuring-sheet-form" method="post">
    <div class="fineness-modulus-wrapper">
        <h3 class="mb-4">Определение зернового состава и модуля крупности ГОСТ 8735-88 п.3123</h3>

        <div class="wrapper-mass mb-4">
            <div class="form-group row">
                <div class="col">
                    <label for="total_mass">Общая масса, г</label>
                    <input type="number" id="total_mass" class="form-control total-mass" name="total_mass" min="0" step="0.01" value="<?= $plSand8735FinenessModule['total_mass'] ?? 2000 ?>">
                </div>
                <div class="col">
                    <label for="mass_on_sieve">Масса на каждом сите, г</label>
                    <input type="number" id="mass_on_sieve" class="form-control mass-on-sieve" name="mass_on_sieve" min="0" step="0.01" value="<?= $plSand8735FinenessModule['mass_on_sieve'] ?? 1000 ?>">
                </div>
            </div>
        </div>

        <table class="table table-striped measuring-sheet list_data mb-4">
            <thead>
            <tr class="table-info">
                <th scope="col" class="text-nowrap">Диаметр отверстий сит, мм</th>
                <th scope="col">10</th>
                <th scope="col">5</th>
                <th scope="col">2,5</th>
                <th scope="col">1,25</th>
                <th scope="col">0,63</th>
                <th scope="col">0,315</th>
                <th scope="col">0,16</th>
                <th scope="col">менее 0,16</th>
                <!--<th scope="col">0,05</th>
                <th scope="col" class="text-nowrap">&lt; 0,05</th>-->
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row" class="text-nowrap">Частный остаток, г</th>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-10" data-sieve="10" name="private_remainder[10]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_10'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-5" data-sieve="5" name="private_remainder[5]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_5'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-2_5" data-sieve="2.5" name="private_remainder[2_5]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_2_5'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-1_25" data-sieve="1.25" name="private_remainder[1_25]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_1_25'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-0_63" data-sieve="0.63" name="private_remainder[0_63]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_0_63'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-0_315" data-sieve="0.315" name="private_remainder[0_315]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_0_315'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-0_16" data-sieve="0.16" name="private_remainder[0_16]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_0_16'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-0_071" data-sieve="0.071" name="private_remainder[0_071]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_0_071'] ?? '' ?>">
                </td>
                <td>
                    <input type="number" class="form-control private-remainder private-remainder-less_0_071" data-sieve="less_0.071" name="private_remainder[less_0_071]" step="0.01" min="0" value="<?= $plSand8735FinenessModule['private_remainder_less_0_071'] ?? '' ?>">
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
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-10" name="private_remainder_by_mass[10]" data-sieve="10" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_10'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-5"
                    name="private_remainder_by_mass[5]" data-sieve="5" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_5'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-2_5" name="private_remainder_by_mass[2_5]" data-sieve="2.5" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_2_5'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-1_25"
                    name="private_remainder_by_mass[1_25]" data-sieve="1.25" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_1_25'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_63" 
                    name="private_remainder_by_mass[0_63]" data-sieve="0.63" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_0_63'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_315" 
                    name="private_remainder_by_mass[0_315]" data-sieve="0.315" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_0_315'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_16" 
                    name="private_remainder_by_mass[0_16]" data-sieve="0.16" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_0_16'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-0_071" 
                    name="private_remainder_by_mass[0_071]" data-sieve="0.071" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_0_071'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control private-remainders-by-mass private-remainders-by-mass-less_0_071" name="private_remainder_by_mass[less_0_071]" data-sieve="less_0.071" value="<?= $plSand8735FinenessModule['private_remainder_by_mass_less_0_071'] ?? '' ?>" readonly>
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
                    name="total_remainder_by_mass[10]" data-sieve="10" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_10'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-5" 
                    name="total_remainder_by_mass[5]" data-sieve="5" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_5'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-2_5" 
                    name="total_remainder_by_mass[2_5]" data-sieve="2.5" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_2_5'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-1_25" 
                    name="total_remainder_by_mass[1_25]" data-sieve="1.25" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_1_25'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_63" 
                    name="total_remainder_by_mass[0_63]" data-sieve="0.63" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_0_63'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_315" 
                    name="total_remainder_by_mass[0_315]" data-sieve="0.315" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_0_315'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_16" 
                    name="total_remainder_by_mass[0_16]" data-sieve="0.16" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_0_16'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-0_071" 
                    name="total_remainder_by_mass[0_071]" data-sieve="0.071" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_0_071'] ?? '' ?>" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-remainder-by-mass total-remainder-by-mass-less_0_071" 
                    name="total_remainder_by_mass[less_0_071]" data-sieve="less_0.071" value="<?= $plSand8735FinenessModule['total_remainder_by_mass_less_0_071'] ?? '' ?>" readonly>
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
                <input type="number" id="finenessModule" class="form-control fineness-module" name="fineness_module" value="<?= $plSand8735FinenessModule['fineness_module'] ?? '' ?>" readonly>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col flex-grow-0">
                <button type="button" id="finenessModuleCalculate" class="btn btn-primary fineness-module-calculate" name="fineness_module_calculate">Расчитать</button>
            </div>
            <div class="col">
                <?php if ( true ): ?>
                    <button type="submit" class="btn btn-primary save" name="fineness_modulus_save">Сохранить</button>
                <?php else: ?>
                    <div class="form-control text-center text-uppercase bg-light-secondary">Сохранить</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- /.fineness-modulus-wrapper -->
</form>