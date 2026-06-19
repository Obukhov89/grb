<table class="table table-striped table-hover">
	<thead>
	<th class="text-center">№</th>
	<th class="text-center">Заявка</th>
	<th class="text-center">Акт ПП</th>
	<th class="text-center">Дата акта</th>
	<th class="text-center">Дата отправки в лабораторию</th>
	<th class="text-center">Протокол PDF</th>
	<th class="text-center"></th>
	</thead>
	<tbody>
		<?php $i = 1; foreach ( $this->data['minerals_request'] as $item ):?>
		<tr>
			<td class="text-center"><?=$i?></td>
			<td class="text-center"><a href="/ulab/request/card/<?=$item['ID_Z']?>" target="_blanc"><?=$item['REQUEST_TITLE']?></a></td>
			<td class="text-center"><?=$item['NUM_ACT_TABLE']?></td>
			<td class="text-center"><?=$item['DATE_ACT'] ? date('d.m.Y', strtotime($item['DATE_ACT'])) : ''?></td>
			<td class="text-center">
				<?php if(!empty($item['date_mineralogy'])):?>
					<input class="date_mineralogy text-center" type="date" value="<?=$item['date_mineralogy']?>">
				<?php else:?>
					<input class="date_mineralogy text-center" type="date">
				<?php endif;?>
				<input class="id" type="hidden" id="id<?=$i?>" value="<?=$item['ID_Z']?>">
			</td>
			<td class="text-center">
				<?php foreach ($item['protocols'] as $protocol):?>
				<a class="no-decoration" href="/archiveMineralogyProtocol/<?=$item['ID_Z']?>/<?=$protocol?>"><?=$protocol?></a><a class="float-end del_protocol_mineralogy"><i class="fa-solid fa-xmark"></i></a><br>
				<?php endforeach;?>
			</td>
			<td class="text-center">
				<form action="/ulab/statistic/uploadFileMineralogy/<?=$item['ID_Z']?>" enctype="multipart/form-data" method="post">
					<label for="formFileMultiple<?=$i?>" style="cursor: pointer">
						<svg class="icon" width="35" height="35">
							<use xlink:href="<?=URI?>/assets/images/icons.svg#download"/>
						</svg>
					</label>
					<input class="form-control saveProtocolMineralogy" type="file" name="protocols[]" accept=".pdf" id="formFileMultiple<?=$i?>" multiple hidden>
				</form>
			</td>
		</tr>
		<?php $i++; endforeach;?>
	</tbody>
</table>
