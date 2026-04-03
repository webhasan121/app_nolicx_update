<?php

namespace App;

use App\Http\Middleware\AbleTo;

trait HandleVendor
{

    //////////////// 
    //  VENDOR  PRODUCT //
    ///////////////
    private $vendor = 'auth.vendor.';
    // private $vendorProduct = $vendor . '.products';

    public function productView()
    {
        //
        // return $this->hasMany(Product::class, 'vendor_id', 'id');
        return view($this->vendor . "products.list");
    }
    public function productDetails()
    {
        //
    }
    public function productEdit()
    {
        //
    }

    public function productCreate()
    {
        //
    }
    public function productStore()
    {
        //     
    }

    public function productUpdate()
    {
        //
    }
    public function productStatus()
    {
        //
    }
    public function productStock()
    {
        //
    }


    //////////////// 
    //  VENDOR CATEGORY //
    ///////////////
    public function categoryIndex()
    {
        //
    }
    public function categoryCreate()
    {
        //
    }
    public function categoryStore()
    {
        //
    }
    public function categoryEdit()
    {
        //
    }
    public function categoryUpdate()
    {
        //
    }


    //////////////// 
    //  VENDOR ORDER //
    ///////////////

    public function orderIndex()
    {
        //
    }
    public function orderUpdate()
    {
        //    
    }
}
