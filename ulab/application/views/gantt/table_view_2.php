<table class="table table-bordered table-hover" id="gantt_table">
    <thead>
    <tr>
        <th scope="col" class="text-center" style="width: 500px; background: #eff0f4;"><span></span></th>
        <?php foreach ($this->data['calendar'] as $month) { ?>
            <th scope="col" colspan="<?php echo count($month['days']) ?>"
                class="text-center"><?php echo $month['local_name'] . " " . $month['year'] ?></th>
        <?php } ?>
    </tr>
    <tr>
        <th class="" style="background: #eff0f4;"></th>
        <!-- Заголовки для дней в месяце -->
        <?php foreach ($this->data['calendar'] as $month) {
            foreach ($month['days'] as $day) { ?>
                <td id="sep_<?= $day['day'] ?>" <?php if ($this->data['current_date']['month']['name'] . "_" . $this->data['current_date']['day'] == $month['local_name'] . "_" . $day['day']) {
                    echo 'style="background: #ffa50087;"';
                } else if ($day['is_weekend']) {
                    echo 'style="background: #00b8ce;"';
                } ?>
                >
                    <span><?= $day['day'] ?></span>
                </td>
            <?php }
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->data['table']['table_info'] as $member) { ?>
        <tr>
            <td style="display: none;"></td>
            <td rowspan="1" class="fixed-column"
                style="background: white;"
                onclick="editUser(<?= $member->getId(); ?>);"><?= $member->getName(); ?></td>
            <?php echo renderCells($this->data['calendar'], $this->data['current_date']); ?>
        </tr>

        <?php foreach ($member->getProjects() as $project) {
            $timeLinesCount = count($project->getTimeLines());
            ?>
            <tr>
                <td onclick="editProject(<?= $project->getId(); ?>, <?= $member->getId(); ?>)"
                    class="fixed-column"
                    rowspan="<?= $timeLinesCount ?>"
                    style="background: lightgray; text-align: right; vertical-align: middle;">
                    <span><?= $project->getName(); ?></span></td>
                <?php
                $timeLines = $project->getTimeLines();

                for ($i = 0; $i < $timeLinesCount; $i++) {
                    echo renderCells($this->data['calendar'],
                        $this->data['current_date'],
                        $timeLines[$i],
                        $member->getId(),
                        $project->getId(),
                        $project->getColor1(),
                        $project->getColor2(),
                        $i,
                    );
                }
                ?>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>