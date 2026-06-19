<div class="filters mb-4">
    <form action="<?=URI?>/statistic/reportFromLims/" method="post">
        <div class="row">
                <div class="col-3">
                    <input type="date" id="date_start" name="date_start" class="form-control" value="<?=$this->data['date_start']?>">
                </div>

                <div class="col-3">
                    <input type="date" id="date_end" name="date_end" class="form-control" value="<?=$this->data['date_end']?>">
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Применить</button>
                </div>
        </div>
    </form>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <table class="table table-bordered mb-3">
            <thead>
            <tr>
                <th scope="col" class="text-center">№ п/п</th>
                <th scope="col">Показатель</th>
                <th scope="col" class="text-center">ЛФХИ</th>
                <th scope="col" class="text-center">ЛФМИ</th>
                <th scope="col" class="text-center">ДСЛ</th>
                <th scope="col" class="text-center">ЛСМ</th>
                <th scope="col" class="text-center">ОСК</th>
                <th scope="col" class="text-center">ИТОГО</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->data['data'] as $i => $row): ?>
                <tr>
                    <td class="text-center">
                        <?=$i?>
                    </td>
                    <td>
                        <?=$row['title']?>
                    </td>
                    <td class="text-center">
                        <?=$row['lfhi']?>
                    </td>
                    <td class="text-center">
                        <?=$row['lfmi']?>
                    </td>
                    <td class="text-center">
                        <?=$row['dsl']?>
                    </td>
                    <td class="text-center">
                        <?=$row['lsm']?>
                    </td>
                    <td class="text-center">
                        <?=$row['osk']?>
                    </td>
                    <td class="text-center">
                        <?=$row['total']?>
                    </td>
                </tr>
            <?php endforeach; ?>
			<?php if(in_array($_SESSION['SESS_AUTH']['USER_ID'], [61,33,101])):?>
				<tr>
					<td class="text-center">
						<?=$i+1?>
					</td>
					<td>
						Количество заявок <b>ОПЫТНОГО ЗАВОДА</b>
					</td>
					<td colspan="2">
						Сумма <b>ВСЕХ ЗАЯВОК</b>
					</td>
					<td class="text-center">
						<?=$this->data['OZ']['allSUM']?>
					</td>
					<td colspan="2">
						Сумма <b>ЗАВЕРШЕННЫХ</b>
					</td>
					<td class="text-center">
						<?=$this->data['OZ']['wonSum']?>
					</td>
				</tr>
				<tr>
					<td colspan="8">
						<div class="chart-container" style="height: 400px; width: 400px">
							<canvas id="ChartOz"></canvas>
						</div>
					</td>
				</tr>
			<?endif;?>
            </tbody>
        </table>


        <table class="table table-bordered w-50">
            <thead>
            <tr>
                <th scope="col" class="text-center">№ п/п</th>
                <th scope="col">Показатель</th>
                <th scope="col" class="text-center">ИТОГО</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        Количество ТЗ <b>ПОСТУПИЛО</b>
                    </td>
                    <td class="text-center">
                        <?=$this->data['count_tz_new']?>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>
                        Количество ТЗ <b>В РАБОТЕ</b>
                    </td>
                    <td class="text-center">
                        <?=$this->data['count_tz_in_work']?>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>
                        Количество ТЗ <b>ЗАКРЫТЫХ</b>
                    </td>
                    <td class="text-center">
                        <?=$this->data['count_tz_complete']?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="filters mb-4">
    <div>
        <div class="row">
            <div class="col-3">
                <input type="date" id="date_start_chart" class="form-control" value="<?=$this->data['current_dates']['first_date_of_year']?>">
            </div>

            <div class="col-3">
                <input type="date" id="date_end_chart" class="form-control" value="<?=$this->data['current_dates']['max_date']?>">
            </div>

            <div class="col-3">
                <select id="lab_chart" class="form-control">
                    <option value='0' selected>Bсе лаборатории</option>
                    <?php if ($this->data['lab']): ?>
                        <?php foreach ($this->data['lab'] as $lab): ?>
                            <option value="<?= $lab['ID'] ?? '' ?>"><?= $lab['NAME'] ?? '' ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-auto">
                <button type="button" id="filter" class="btn btn-primary">Применить</button>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <div id="chartForm">
            <canvas id="myChart1" style="position:absolute; padding-left: 90px"></canvas>
            <canvas id="myChart" style="position:relative;"></canvas>
        </div>
    </div>
</div>
