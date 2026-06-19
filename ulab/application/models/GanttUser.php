<?php

/**
 * Класс, который нужен для отображения в вьюшке index и адекватеой связи юзера с проектами
 *
 * todo: поправить нейминг
 */
class GanttUser extends Model implements JsonSerializable
{
    private int $id;
    private string $name;
    private string $position;
    private float $salary;
    private ?int $projectId;

    private array $projectTimeLines;

    public function __construct(array $data)
    {
        parent::__construct();

        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->position = $data['position'];
//        $this->salary = $data['salary'];
        $this->projectId = $data['project_id'];
        $this->projectTimeLines = $this->calculateTimeLines();
    }

    private function calculateTimeLines(): array
    {
        $query = "SELECT * FROM `gantt_user_projects` where user_id = {$this->getId()} AND project_id = {$this->projectId} order by start_date asc;";
        $data = $this->DB->Query($query);

        $result = [];
        while ($row = $data->Fetch()) {
            $timelineData = [];
            $timelineData['id'] = $row['id'];
            $timelineData['user_id'] = $row['user_id'];
            $timelineData['project_id'] = $row['project_id'];
            $timelineData['start_date'] = $row['start_date'];
            $timelineData['end_date'] = $row['end_date'];
            $timelineData['hours'] = $this->getHours($row['user_id'], $row['project_id'], $row['id']);

            $result[] = $timelineData;
        }

        return $result;
    }

    public function getHours($userId, $projectId, $timelineId): array
    {
        $query = "SELECT * FROM `gantt_user_hours` where user_id = {$userId} AND project_id = {$projectId} AND timeline_id = {$timelineId} order by date asc;";
        $data = $this->DB->Query($query);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function getTimeLinesCountByProjectId(): int
    {
        // fixme: когда дата выходит за предел минимальной в календаре, то её не нужно отображать. поправить этот момент
        return count($this->getTimeLinesByProjectId());
    }

    public function getTimeLines(): array
    {
        return $this->projectTimeLines;
    }

    public function getTimeLinesByProjectId(): array
    {
        $projectId = $this->projectId;

        $filtered = array_filter($this->getTimeLines(), function ($timeLine) use ($projectId) {
            return intval($timeLine['project_id']) == intval($projectId);
        });

        return array_values($filtered);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
//            'salary' => $this->salary,
            'projectId' => $this->projectId,
            'projectTimeLines' => $this->projectTimeLines,
        ];
    }
}