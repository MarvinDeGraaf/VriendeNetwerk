<?php

Class Image extends Database {
    public function storeImage($path) {
        $query = "INSERT INTO image (filename)
        VALUES (?);";
        return parent::voerQueryUit($query, [$path]);
    }

        public function getImageUrlQuery($where)
    {
        $query = "SELECT filename FROM image WHERE imageId = ?";
        return parent::voerQueryUit($query, [$where]);
    }

    public function getImageByFilename($fileName)
    {
        $query = "SELECT imageId
        FROM image
        WHERE filename = ?";
        return parent::voerQueryUit($query, [$fileName]);
    }
}