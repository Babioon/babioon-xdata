<?php

if (!defined('_JEXEC')) die('not allowed');


class datasetMeinungen extends gateway
{

    public function execute()
    {
        $result='datasetMeinungen';
        $category = 379;
        $items=$this->getContentItems($category);

        echo "#<div style='text-align:left;font_size:1.2em;'><pre>";
        print_r($items);
        echo "</pre></div>#";


        return $result;
    }

 }