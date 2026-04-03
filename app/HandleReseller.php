<?php

namespace App;

trait HandleReseller
{
    private $reseller = 'auth.reseller.';
    //
    public function productIndex()
    {
        // 
        return view($this->reseller . "products.list");
    }
}
