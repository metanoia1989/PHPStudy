<?php
$employee_detail_array = [
    'name' => 'John Doe',
    'position' => 'Software Enginner',
    'address' => '53, nth street, city',
    'status' => 'best',
    'child' => [
        'name' => 'John Doe',
        'position' => 'Software Enginner',
        'address' => '53, nth street, city',
        'status' => 'best',
    ],
];

$employee = (object) $employee_detail_array;

$employee_object = new stdClass;
$employee_object->name = "John Doe";
$employee_object->position = "Software Engineer";
$employee_object->address = "53, nth street, city";
$employee_object->status = "Best";

$employee_array = (array) $employee_object;

var_dump($employee);
// var_dump($employee_array);