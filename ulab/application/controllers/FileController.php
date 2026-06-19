<?php

/**
 *
 * Class FileController
 */
class FileController extends Controller
{
    public function bytePdfToServerAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $byteFile = $_POST["file"];
        $path = $_POST["path"];

        $file = $this->model("Secondment");

        $file->byteFileToServer($byteFile, $path);

        echo json_encode(true);
    }

}