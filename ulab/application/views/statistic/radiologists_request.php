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
		<?php $i = 1; foreach ( $this->data['radiologists_request'] as $item ):?>
		<tr>
			<td class="text-center"><?=$i?></td>
			<td class="text-center"><a href="/ulab/request/card/<?=$item['ID_Z']?>" target="_blanc"><?=$item['REQUEST_TITLE']?></a></td>
			<td class="text-center"><?=$item['NUM_ACT_TABLE']?></td>
			<td class="text-center"><?=$item['DATE_ACT'] ? date('d.m.Y', strtotime($item['DATE_ACT'])) : ''?></td>
			<td class="text-center">
				<?php if(!empty($item['date_radiology'])):?>
					<input class="date_radiology text-center" type="date" value="<?=$item['date_radiology']?>">
				<?php else:?>
					<input class="date_radiology text-center" type="date">
				<?php endif;?>
				<input class="id" type="hidden" id="id<?=$i?>" value="<?=$item['ID_Z']?>">
			</td>
			<td class="text-center">
				<?php foreach ($item['protocols'] as $protocol):?>
				<a class="no-decoration" href="/archiveRadiologyProtocol/<?=$item['ID_Z']?>/<?=$protocol?>"><?=$protocol?></a><a class="float-end del_protocol_radiology"><i class="fa-solid fa-xmark"></i></a><br>
				<?php endforeach;?>
			</td>
			<td class="text-center">
				<form action="/ulab/statistic/uploadFile/<?=$item['ID_Z']?>" enctype="multipart/form-data" method="post">
					<label for="formFileMultiple<?=$i?>" style="cursor: pointer">
						<svg class="icon" width="35" height="35">
							<use xlink:href="<?=URI?>/assets/images/icons.svg#download"/>
						</svg>
					</label>
					<input class="form-control saveProtocolRadiology" type="file" name="protocols[]" accept=".pdf" id="formFileMultiple<?=$i?>" multiple hidden>
				</form>
			</td>
		</tr>
		<?php $i++; endforeach;?>
	</tbody>
</table>
