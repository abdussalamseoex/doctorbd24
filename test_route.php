<?php
$request = Request::create("/hospitals", "GET");
$response = app()->handle($request);
echo $response->status();
