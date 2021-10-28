<?php

use Catalogue as GlobalCatalogue;

class Parameters
{
    const FILE_NAME = 'products.txt';
    const COLUMNS = ['item', 'price'];
    const population_size = 10;
    const BUDGET = 250000;
}

class Catalogue
{
    function createProductcolumn($listOfRawProduct){
        foreach (array_keys($listOfRawProduct) as $listOfRawProductKey){
            $listOfRawProduct[Parameters::COLUMNS[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
            unset($listOfRawProductKey);
        }
        return $listOfRawProduct;
    }
    function product()
    {
        $collectionOfListProduct = [];

        $raw_data = file(Parameters::FILE_NAME);
        foreach ($raw_data as $listOfRawProduct){
            $collectionOfListProduct[] = $this->createProductcolumn(explode(",", $listOfRawProduct));
        }

        return $collectionOfListProduct;

        // foreach ($collectionOfListProduct as $listOfRawProduct){
        //     print_r($listOfRawProduct);
        //     echo '<br>';
        // }
        // return [
        //     'product' => $collectionOfListProduct,
        //     'gen_length' => count($collectionOfListProduct)
        // ];
    }
}

class Individu
{
    function CountNumberOfGen()
    {
        $catalogue = new GlobalCatalogue;
        return count($catalogue->product());
    }
    function createRandomIndividu()
    {
        
        for ($i = 0; $i <= $this->CountNumberOfGen()-1; $i++){
            $ret[] = rand(0,1);
        }
        return $ret;
    }
}

class Population
{
    function createRandomPupulation(){
        $individu = new Individu;

        for ($i = 0; $i <= Parameters::population_size-1; $i++){
            $ret[] = $individu->createRandomIndividu();
        }
        return $ret;
    }
}

class Fitness
{
    //memilih item
    function selectionItem($individu){
        $catalogue = new Catalogue;
        foreach($individu as $individuKey => $binaryGen){
            if ($binaryGen === 1){
                $ret[] = [
                    'selectedKey' => $individuKey,
                    'selectedPrice' => $catalogue->product()[$individuKey]['price']
                ];
            }
        }
        return $ret;
    }

    //Menjumlahkan untuk mencari Fiteness Value
    function calculateFitnessValue($individu){
        return array_sum(array_column($this->selectionItem($individu),'selectedPrice'));
    }

    //menghitung item yang dipilih
    function countSelectedItem($individu){
        return count($this->selectionItem($individu));
    }

    //menemukan solusi yang terbaik dari individu yang FIT
    function isFound($fits){
        //menghitung item Max
        print_r(array_count_values(array_column($fits, 'numberOfSelectedItem')));
    }

    //mengecek apakah Fit atau tidak dari fitnes value
    function isFit($fitnessValue){
        if($fitnessValue <= Parameters::BUDGET){
            return TRUE;
        }
    }
    
    //menseleksi
    function fitnessEvaluation($population){
        $catalogue = new Catalogue;
        foreach ($population as $listOfIndividuKey => $listOfIndividu){
           echo 'Individu'. $listOfIndividuKey.'<br>';
            foreach ($listOfIndividu as $individuKey => $binaryGen){
                echo $binaryGen.'&nbsp;&nbsp;';
                print_r($catalogue->product()[$individuKey]);
                echo '<br>';
            }
            $fitnessValue = $this->calculateFitnessValue($listOfIndividu);
            $numberOfSelectedItem = $this->countSelectedItem($listOfIndividu);
            echo 'Max. Item: '. $numberOfSelectedItem;
            echo ' Fitness value: '. $fitnessValue;
            if ($this->isFit($fitnessValue)){
                echo ' (Fit)';
                //invidu yang fit kemudian disimpan kedalam array untuk memnentukan kandidat yang terbaik nantinya
                $fits[] = [
                    'selectedindividuKey' => $listOfIndividu,
                    'numberOfSelectedItem' => $numberOfSelectedItem,
                    'fitnessValue' => $fitnessValue
                ];
                print_r($fits);
            } else {
                echo ' (Not Fit)';
            }
            echo '<p>';
        }
        $this->isFound($fits);
    }
}

$parameters = [
    'file_name' => 'products.txt',
    'columns' => ['item', 'price'],
    'population_size' => 10
];

// $katalog = new Catalogue;
// $katalog->product($parameters);

$initalPopulation = new Population;
$population = $initalPopulation->createRandomPupulation();

$fitness = new Fitness;
$fitness->fitnessEvaluation($population);


// $individu = new Individu;
// print_r($individu->createRandomIndividu());