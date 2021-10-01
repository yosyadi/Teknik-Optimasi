<?php

class Catalogue
{
    function products($parameters)
    {
        $collectionOfListProduct = [];

        file($parameters['file_name']);
    }
}

$parameters = [
    'file_name' => 'products.txt',
    'columns' => ['item', 'protein', 'karbohidrat', 'lemak', 'serat']
];
