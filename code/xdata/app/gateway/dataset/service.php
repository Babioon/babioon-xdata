<?php

if (!defined('_JEXEC')) die('not allowed');


class datasetService extends gateway
{

    public function execute()
    {
        $result='datasetService';
        $category = 378;
        $items=$this->getContentItems($category);

        echo "#<div style='text-align:left;font_size:1.2em;'><pre>";
        print_r($items);
        echo "</pre></div>#";


        return $result;
    }

 }