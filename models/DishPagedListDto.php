<?php

include_once 'models/BasicDto.php';
include_once 'models/DishDto.php';
include_once 'models/PageInfoModel.php';
include_once 'helpers/SortingEnum.php';
include_once 'helpers/CategoriesEnum.php';

class DishPagedListDto extends BasicDto {
    const maxSize = 6;
    protected $dishes = array();
    protected $pagination = array();
    
    public function __construct($params) {
        $request = array();
        $request['SELECT'] = "*";
        $request['FROM'] = "dishes";

        $whereStatemant = $this->getWhere($params);
        if ($whereStatemant) {
            $request['WHERE'] = implode(' AND ', $whereStatemant);
        }

        if (!is_null($params['sorting']) && !empty($params['sorting'])) {
            $sort = Sorting::checkSorting($params['sorting']);

            if (!$sort) {
                $s = $params['sorting'];
                $response = new Response(400, "There is no such sorting parameter as '$s'");
                setHTTPStatus(400, $response);
                exit;
            }
            $request['ORDER BY'] = $sort->value;
        }

        $dbRequest = implode(
            " ", 
            array_map(
                function($key, $value) {
                    return $key." ".$value;
                }, 
                array_keys($request),
                array_values($request)
            ));
        
        $requestResult = $GLOBALS['dbLink']->query($dbRequest)->fetch_all(MYSQLI_ASSOC);

        if (!isset($params['page'])) {
            $response = new Response(400, "Page undefined. Incorrect request");
            setHTTPStatus(400, $response);
            exit;
        }

        $currPage = $params['page'];
        $pag = new PageInfoModel(count($requestResult), $currPage);
        $this->pagination = $pag->getContents();

        $maxPage = $this->pagination['count']; 
        if ($maxPage < $currPage) {
            $response = new Response(400, "Page number is above pages count. Max page is '$maxPage'");
            setHTTPStatus(400, $response);
            exit;
        }

        $dishIndex = ($currPage - 1) * self::maxSize;

        if ($dishIndex + self::maxSize >= count($requestResult) - 1) {
            $requestResult = array_slice($requestResult, $dishIndex);
        }
        else {
            $requestResult = array_slice($requestResult, $dishIndex, self::maxSize);
        }

        foreach($requestResult as $key => $value) {
            array_push($this->dishes, (new DishDto($value))->getContents());
        }
    }

    private function getWhere($params) {
        $where = array();

        if (isset($params['categories']) || !empty($params['categories'])) {
            
            if (!is_array($params['categories'])) {
                $isCategoryValid = Categories::checkCategories($params['categories']);
                if (!$isCategoryValid) {
                    $c = $params['categories'];
                    $response = new Response(400, "There is no such category as '$c'");
                    setHTTPStatus(400, $response);
                    exit;
                }
            }
            
            foreach ($params['categories'] as $key => $value) {
                $isCategoryValid = Categories::checkCategories($value);
                if (!$isCategoryValid) {
                    $c = $value;
                    $response = new Response(400, "There is no such category as '$c'");
                    setHTTPStatus(400, $response);
                    exit;
                }
            }

            $categories = "'" . $params['categories'] . "'";
            if (is_array($params['categories'])) {
                $uwu = array_map(
                    function ($val) {
                        return "'" . $val . "'";
                    }, $params['categories']
                );
                
                $categories = implode(',', $uwu);
            }

            array_push($where, "category IN ($categories)");
        }

        if (isset($params['vegetarian']) || !empty($params['vegetarian'])) {
            $isVegetarian = filter_var($params['vegetarian'], FILTER_VALIDATE_BOOLEAN);

            if ($isVegetarian) {
                array_push($where, "vegetarian = $isVegetarian");
            }
        }

        return $where;
    }
}
?>