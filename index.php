<?php
class Travel
{
    protected $allTravel;

    public function __construct()
    {
        $this->allTravel = json_decode(file_get_contents('https://5f27781bf5d27e001612e057.mockapi.io/webprovise/travels'),true);
    }

    public function sumPrice(){
        return array_reduce($this->allTravel, function($carry, $item){
            if(!isset($carry[$item['companyId']])){
                $carry[$item['companyId']] = $item['price'];
            } else {
                $carry[$item['companyId']] += $item['price'];
            }
            return $carry;
        });
    }
}

class Company
{
    protected $allCompany;

    public function __construct()
    {
        $this->allCompany = json_decode(file_get_contents('https://5f27781bf5d27e001612e057.mockapi.io/webprovise/companies'),true);
    }

    public function addTotalPrice($arrPrice)
    {
        foreach ($this->allCompany as $key => $company){
            $this->allCompany[$key]['price'] = $arrPrice[$company['id']] ?? 0;
        }
    }

    public function buildTree($parentId = 0) {
        $branch = [];
        foreach ($this->allCompany as $element) {
            if ($element['parentId'] == $parentId) {
                $children = $this->buildTree($element['id']);
                if ($children) {
                    $element['price'] += array_sum(array_column($children, 'price', 'id'));
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function getAll()
    {
        return $this->allCompany;
    }
}

class TestScript
{
    public function execute()
    {
        $start = microtime(true);
        $travel = new Travel();
        $arraySumPrice = $travel->sumPrice();
        $company = new Company();
        $company->addTotalPrice($arraySumPrice);
        echo "<pre>";
        print_r($company->buildTree());
        echo 'Total time: ' . (microtime(true) - $start);
    }
}
(new TestScript())->execute();
