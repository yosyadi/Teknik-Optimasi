<?php

class Catalogue
{
    function createProductcolumn($columns, $listOfRawProduct){
        foreach (array_keys($listOfRawProduct) as $listOfRawProductKey){
            $listOfRawProduct[$columns[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
            unset($listOfRawProductKey);
        }
        return $listOfRawProduct;
    }
    function product($parameters)
    {
        $collectionOfListProduct = [];

        $raw_data = file($parameters['file_name']);
        foreach ($raw_data as $listOfRawProduct){
            $collectionOfListProduct[] = $this->createProductcolumn($parameters['columns'], explode(",", $listOfRawProduct));
        }

        foreach ($collectionOfListProduct as $listOfRawProduct){
            print_r($listOfRawProduct);
            echo '<br>';
        }
        return [
            'product' => $collectionOfListProduct,
            'gen_length' => count($collectionOfListProduct)
        ];
    }
}

class populationGenerator{
    function createIndividu($parameters){
        $catalogue = new Catalogue;
        $lengthOfGen = $catalogue->product($parameters)['gen_length'];
        for ($i = 0; $i <= $lengthOfGen-1; $i++){
            $ret[] = rand(0, 150);
        }
        return $ret;
    }

    function createPupulation($parameters){
        for ($i = 0; $i <= $parameters['population_size']; $i++){
            $this->createIndividu($parameters);
            $ret[] = $this->createIndividu($parameters);
        }
        foreach ($ret as $key => $val){
            print_r($val);
            echo '<br>';
        }
    }
}

$parameters = [
    'file_name' => 'products.txt',
    'columns' => ['item', 'price'],
    'population_size' => 10
];

$katalog = new Catalogue;
$katalog->product($parameters);

$initalPopulation = new populationGenerator;
$initalPopulation->createPupulation($parameters);