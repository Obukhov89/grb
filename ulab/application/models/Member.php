<?php

/**
 * Класс, необходимый для связи данных во вбюшке index2
 *
 * todo: нейминг поправить
 */
class Member extends Model implements JsonSerializable
{
    private int $id;
    private string $name;
    private string $position;
    private float $salary;
    private array $projects;

    public function __construct(array $data)
    {
        parent::__construct();

        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->position = $data['position'];
        $this->salary = $data['salary'];
        $this->projects = $this->calculateProjects();
    }

    private function calculateProjects(): array
    {
        $sql = "SELECT * FROM `osk_project` 
                WHERE id IN (SELECT project_id FROM `gantt_user_projects` WHERE user_id = {$this->getId()});";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($project = $data->Fetch()) {
            $result[] = $project;
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

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}
