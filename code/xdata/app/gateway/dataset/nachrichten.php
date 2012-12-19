<?php

if (!defined('_JEXEC')) die('not allowed');


class datasetNachrichten extends gateway
{

    public function execute()
    {
        $result='';
        $category = 374;
        $items=$this->getContentItems($category);

        $html ='';
        foreach ($items as $i)
        {
            $html .='<li><a href="'.$i->link.'">'.date('d.m.: ',strtotime($i->modified)). htmlentities($i->title).'</a></li>';
        }
        $html = '<ul>'.$html.'</ul>';

        return $html;
    }

 }