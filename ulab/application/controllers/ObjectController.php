<?php

use Bitrix\Main\Loader;


class ObjectController extends Controller
{


    public function new()
    {
        $version = rand();

        $this->addJs('/assets/js/object.js?v=' . $version);
        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

        $secondment = $this->model('Secondment');

        $this->data["companyList"] = $secondment->getCompanyList();
        $this->data["cityList"] = $secondment->getCityArr();

        echo "<pre>";
      //  var_dump( $this->data["companyList"]);
        //var_dump( $cityList);
        echo "</pre>";

        $this->view("new");
    }

    public function getCityArr()
    {

    }

    // Добавляет новый объект
    public function addAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $object = $this->model('ObjectTest');

        $data["NAME"] = $_POST["NAME"];
        $data["ID_COMPANY"] = $_POST["ID_COMPANY"];
        $data["KM"] = $_POST["KM"];
        $data["CITY_ID"] = $_POST["CITY"];

        // Подведение координатов под формат, как в objects.php
        for($a=0; $a<count($_POST['coord']); $a++)
        {
            $_POST['coord'][$a][0] = (float)$_POST['coord'][$a][0];
            $_POST['coord'][$a][1] = (float)$_POST['coord'][$a][1];
        }
        $coord[] = $_POST['coord'];

        $data["COORD"] = serialize($coord);

        $data["ID"] = $object->add($data);

       // echo json_encode($result);
        echo json_encode($data);
    }

    // Загрузить города
    public function addCities()
    {
        $object = $this->model('Object');

        $cityJsonPath = '/home/bitrix/www/ulab/assets/json/citiesSQL.json';
        $cityJson = file_get_contents($cityJsonPath);
        $cityArr = json_decode($cityJson, true);

        $cityList = [];

        foreach ($cityArr as $city) {
            $cityList[] = $city["city"];
            $cityName = $city["city"];
        }

//        $this->data["cityList"] = $cityList;
//
//        echo "<pre>";
//        var_dump($this->data["cityList"]);
//        echo "</pre>";






    }
}