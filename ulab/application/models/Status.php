<?php

class Status extends Model
{
    public function setNewTzStatus($dealId, $data)
    {
        $response = $this->DB->Query("SELECT * FROM tz_status_history WHERE deal_id = {$dealId} AND status_id = {$data['status_id']}")->Fetch();

        if($data['status_id'] == 17) {
            $isHasTz = $this->checkTz($dealId);

            if(!$isHasTz) {
                return;
            }
        }

        if(!is_array($response)) {
            $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                          VALUE({$dealId}, {$data['user_id']}, {$data['status_id']}, '{$data['date']}')");
        }
    }

    public function checkTz($dealId)
    {
        $response = $this->DB->Query(
            "
                SELECT ugtp.id as is_create_tz
                FROM ba_tz bt
                LEFT JOIN ulab_material_to_request umtr ON umtr.deal_id = bt.ID_Z
                LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
                WHERE bt.ID_Z = '{$dealId}'
            "
        )->Fetch()['is_create_tz'];

        if($response){
            return true;
        }else {
            return  false;
        }
    }

    public function getStatusTrials($dealId)
    {
        $result = [];

        $response = $this->DB->Query("
            SELECT 
                (SELECT state 
                 FROM ulab_start_trials ust 
                 WHERE ust.ugtp_id = ugtp.id 
                 ORDER BY ust.id DESC 
                    LIMIT 1) as state
            FROM ulab_material_to_request umtr 
            LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            WHERE umtr.deal_id = {$dealId}
            GROUP BY ugtp.id
        ");

        while($row = $response->Fetch()) {
            $result[] = $row['state'];
        }

        return $result;
    }

    public function getStatusProtocols($dealId, $user_id)
    {
        $result = [];

        $response = $this->DB->Query("
            SELECT p.NUMBER
            FROM ulab_material_to_request umtr
            LEFT JOIN PROTOCOLS p ON p.ID = umtr.protocol_id
            WHERE umtr.deal_id = {$dealId}
        ");

        while($row = $response->Fetch()) {
            $result[] = $row['NUMBER'];
        }
    }

    public function getStatusesForDeal($dealId, $takenDeal = false)
    {
        $result = [];
        $titlesArray = [];

        $response = $this->DB->Query("
                SELECT ts.id, ts.group_id, ts.name, ts.header_status, 
                       tsg.color, tsg.name as title, tsg.id as group_id,
                       tsh.date,
                       tsg2.name as group_title, tsg2.color as group_color, tsg2.id as gr_id, tsg2.comment as group_comment
                FROM tz_status ts
                LEFT JOIN tz_status_history tsh ON tsh.status_id = ts.id AND tsh.deal_id = {$dealId}
                LEFT JOIN tz_status_groups tsg2 ON tsg2.id = ts.group_id
                LEFT JOIN tz_status_groups tsg ON tsg.id = ts.group_id AND tsh.date IS NOT NULL
                ORDER BY ts.id
        ");

        while($row = $response->Fetch()) {
            $result['statuses'][$row['group_title']][] = $row;

            $titlesArray[$row['gr_id']] =
                [
                    'title' => $row['group_title'],
                    'comment' => $row['group_comment']
                ];
        }

        foreach($result['statuses'] as $key => $value) {
            $fullStatus = true;

            foreach ($value as $v) {
                if (empty($v['date']) && $v['header_status']) {
                    $fullStatus = false;
                }
            }

            if ($fullStatus) {
                $result['title'] = $result['statuses'][$key][0]['group_title'];
                $result['color'] = $result['statuses'][$key][0]['group_color'];
                $result['group_id'][] = $result['statuses'][$key][0]['gr_id'];
            }


            if($takenDeal && $key == 'Заключение договора' && !empty($value[1]['date']) && !empty($value[3]['date'])) {
                $result['title'] = 'Заключение договора';
                $result['color'] = '#15c';
                $result['group_id'][] = 3;
            }

            if($key == 'Испытания завершены' && (!empty($value[0]['date']) || !empty($value[1]['date']))) {
                $result['title'] = 'Испытания завершены';
                $result['color'] = '#f7ba00';
                $result['group_id'][] = 6;
            }

            if($key == 'Заявка успешно завершена' && (!empty($value[0]['date']) || !empty($value[1]['date']))) {
                $result['title'] = 'Заявка успешно завершена';
                $result['color'] = '#68c140';
                $result['group_id'][] = 7;
            }

            if($key == 'Заявка не успешна' && !empty($value[0]['date'])) {
                $result['title'] = 'Заявка не успешна';
                $result['color'] = '#e06666';
                $result['group_id'][] = 8;
            }
        }

        if(empty($result['title'])) {
            $result['title'] = 'Рассмотрение заявки';
            $result['color'] = '#cfe2f3';
            $result['group_id'][] = 1;
        }


        $lastGroup = max($result['group_id']);
        $nexStep = $lastGroup + 1;

        $result['last_group'] = $lastGroup;

        if(count($titlesArray) > $nexStep) {
            $result['next_title'] = $titlesArray[$nexStep]['title'];
            $result['next_comment'] = $titlesArray[$nexStep]['comment'];
        }

        return $result;
    }

    public function getStatusList()
    {
        $result = [];

        $response = $this->DB->Query("
            SELECT ts.name as status_name, ts.id as status_id,
                   tsg.name as group_name, tsg.id as group_id
            FROM tz_status ts
            LEFT JOIN tz_status_groups tsg ON tsg.id = ts.group_id 
        ");

        while($row = $response->Fetch()) {
            $result[$row['group_id']]['statuses_list'][] =
                [
                    'status_id' => $row['status_id'],
                    'status_name' => $row['status_name']
                ];

            $result[$row['group_id']]['group_name'] = $row['group_name'];
        }

        return $result;
    }

    public function getStatusesForGroup($groupId)
    {
        $result = [];

        $response = $this->DB->Query("
            SELECT ts.name as status_name, ts.id as status_id
            FROM tz_status ts
            WHERE ts.group_id = {$groupId} AND ts.header_status = 1
        ");

        while ($row = $response->Fetch()) {
            $result[] = $row['status_id'];
        }

        return $result;
    }

    public function checkLooseRequest($dealId)
    {
        $result = $this->DB->Query("SELECT id FROM tz_status_history WHERE deal_id = '{$dealId}' AND status_id = 7 LIMIT 1")->Fetch()['id'];

        return $result;
    }

    public function getLooseComment($dealId)
    {
        $result = $this->DB->Query("SELECT lose_comment FROM ba_tz WHERE ID_Z = '{$dealId}'")->Fetch()['lose_comment'];

        return $result;
    }

    public function checkHasStatus($dealId, $statusId)
    {
        $result = $this->DB->Query("SELECT id FROM tz_status_history WHERE deal_id = '{$dealId}' AND status_id = '{$statusId}' LIMIT 1")->Fetch()['id'];

        return $result;
    }

    public function checkHasStatusNew($dealId, $statusId)
    {
        $result = $this->DB->Query("SELECT id FROM tz_status_history WHERE deal_id = '{$dealId}' AND status_id != '{$statusId}' LIMIT 1")->Fetch()['id'];

        return $result;
    }
}