<?php

use Catalogue as GlobalCatalogue;

class Parameters
{
    const FILE_NAME = 'products.txt';
    const COLUMNS = ['item', 'price'];
    const population_size = 10;
    // const BUDGET = 250000;
    // const STOPPING_VALUE = 10000;
    const BUDGET = 130;
    const STOPPING_VALUE = 10;
    const CROSSOVER_RATE = 0.8;
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
    
    //untuk menenemukan berapa jumlah individu yang terbaik
    function searchBestIndividu($fits, $maxItem,  $numberOfIndividuHasMaxItem){
        if($numberOfIndividuHasMaxItem === 1){
            $index = array_search($maxItem, array_column($fits, 'numberOfSelectedItem'));
            return $fits[$index];
        } else {
            foreach($fits as $key => $val){
                if($val['numberOfSelectedItem'] === $maxItem){
                    echo $key. ' '.$val['fitnessValue'].'<br>';
                    $ret[] = [
                        'individuKey' => $key,
                        'fitnessValue' => $val['fitnessValue']
                    ];
                }
            }
            if (count(array_unique(array_column($ret, 'fitnessValue'))) === 1){
                $index = rand(0, count($ret) - 1);
            } else {
                $max = max(array_column($ret, 'fitnessValue'));
                $index = array_search($max, array_column($ret, 'fitnessValue'));
            }
            echo 'Hasil';
            return $ret[$index];
        }
    }

    //menemukan solusi yang terbaik dari individu yang FIT
    function isFound($fits){
        //menghitung item Max
        $countedMaxItems = array_count_values(array_column($fits, 'numberOfSelectedItem'));
        print_r($countedMaxItems);
        echo '<br>';
        $maxItem = max(array_keys($countedMaxItems));
        echo $maxItem;
        echo '<br>';
        echo $countedMaxItems[$maxItem];
        $numberOfIndividuHasMaxItem = $countedMaxItems[$maxItem];

        $bestFitnessValue = $this->searchBestIndividu($fits, $maxItem, $numberOfIndividuHasMaxItem)['fitnessValue'];
        echo '<br>';
        //menampilkan hasil individu terbaik dengan harga yang paling mendekati budget
        echo '<br>Best fitness value: '.$bestFitnessValue; 
        echo '<br>';

        //menghitung selisih harga dengan budget
        $residual = Parameters::BUDGET - $bestFitnessValue;
        echo 'Residual : '.$residual;
        echo '<br>';

        if($residual <= Parameters::BUDGET && $residual > 0){
            return TRUE;
        }
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
        if($this->isFound($fits)){
            echo 'Found';
        } else {
            echo '>> Next Generation';
        }
        echo '<br>';
    }
}

//Pertemuan 3 Crossover
class Crossover{
    public $population;

    function __construct($population)
    {
        $this->population = $population;
    }

    //membangkitkan nilai acak
    function randomZeroToOne(){
        return (float) rand() / (float) getrandmax();
    }
    function generateCrossover(){
        for ($i = 0; $i <= Parameters::population_size-1; $i++){
            $randomZeroToOne = $this->randomZeroToOne();
            if($randomZeroToOne < Parameters::CROSSOVER_RATE){
                $parents[$i] = $randomZeroToOne;
            }
        }

        //membuat kombinasi dari individu-individu yang terpilih
        foreach(array_keys($parents) as $key){
            foreach(array_keys($parents) as $subkey){
                if ($key !== $subkey){
                    $ret[] = [$key, $subkey];
                }
            }
            array_shift($parents);
        }
        return $ret;
    }

    //tetapkan parent
    function offspring($parent1, $parent2, $cutPointIndex, $offspring){
        $lengthOfGen = new Individu;
        if($offspring === 1){
            for($i = 0; $i <= $lengthOfGen->CountNumberOfGen() - 1; $i++){
                if($i <= $cutPointIndex){
                    $ret[] = $parent1[$i]; //jika dia membaca setiap gen sepanjang kromosom, maka jika kurang dari cutPointIndex akan disimpan kedalam return
                }
                if($i > $cutPointIndex){
                    $ret[] = $parent2[$i];
                }
            }
        }
        if($offspring === 2){
            for($i = 0; $i <= $lengthOfGen->CountNumberOfGen() - 1; $i++){
                if($i <= $cutPointIndex){
                    $ret[] = $parent2[$i]; //jika dia membaca setiap gen sepanjang kromosom, maka jika kurang dari cutPointIndex akan disimpan kedalam return
                }
                if($i > $cutPointIndex){
                    $ret[] = $parent1[$i];
                }
            }
        }
        return $ret;
    }
    
    function cutPointRandom(){
        $lengthOfGen = new Individu;
        return rand(0, $lengthOfGen->CountNumberOfGen() - 1);
    }

    function crossover(){
        $cutPointIndex = $this->cutPointRandom();
        echo 'cutPoint di Index Ke : '. $cutPointIndex;
        //potong berdasarkan crossover
        echo '<br>';
        foreach($this->generateCrossover() as $listOfCrossover){
            $parent1 = $this->population[$listOfCrossover[0]];
            $parent2 = $this->population[$listOfCrossover[1]];
            echo '<br><br>Parent :<br>';
            foreach($parent1 as $gen){
                echo $gen;
            }
            echo ' >< ';
            foreach($parent2 as $gen){
                echo $gen;
            }
            //echo '<br';
            echo '<br>Offspring<br>';
            $offspring1 = $this->offspring($parent1, $parent2, $cutPointIndex, 1);
            $offspring2 = $this->offspring($parent1, $parent2, $cutPointIndex, 2);
            foreach($offspring1 as $gen){
                echo $gen;
            }
            echo ' >< ';
            foreach($offspring2 as $gen){
                echo $gen;
            }
        }
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

$crossover = new Crossover($population);
$crossover->crossover();

// $individu = new Individu;
// print_r($individu->createRandomIndividu());