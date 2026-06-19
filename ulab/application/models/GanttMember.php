<?php

/**
 * Класс, необходимый для связи данных во вбюшке index2
 *
 * todo: нейминг поправить
 */
class GanttMember extends Model implements JsonSerializable
{
    private int $id;
    private string $name;
    private string $position;
    private float $salary;
    private array $projects;

    public function __construct(array $data, array $filter)
    {
        parent::__construct();

        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->position = $data['position'];
//        $this->salary = $data['salary'];
        $this->projects = $this->calculateProjects($filter);
    }

    private function calculateProjects(array $filter): array
    {
        $where = "";
        if (!empty(trim($filter['project_filter']))) {
            $where = " and name like '%{$filter['project_filter']}%'";
        }
        $sql = "SELECT * FROM `osk_project` 
                WHERE id IN (SELECT project_id FROM `gantt_user_projects` WHERE user_id = {$this->getId()}) $where;";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($project = $data->Fetch()) {
            $result[] = new GanttProject($project, $this->id);
        }

        return $result;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getname()
    {
        return $this->name;
    }

    public function getProjects(): array
    {
        return $this->projects;
    }

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}
