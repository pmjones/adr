<?php
$json = '';
foreach ($this->collection as $blog) {
    $json .= json_encode($blog) . ','
}
echo '{' . rtrim($json, ',') . '}';
