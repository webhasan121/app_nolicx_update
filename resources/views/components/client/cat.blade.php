<div>
    @props(['cat', 'active' => false] )
    <style>
        .cat_box {
            position: relative;
            display: block;

            height: {
                    {
                    $height
                }
            }

            px;
            /* border: 1px solid #a8a8a8; */
            border-radius: 12px;
            overflow: hidden;
            /* max-width: {{$height}}px; */
        }

        .cat_box img {
            height: {
                    {
                    $height
                }
            }

            px;
            object-fit: cover;
            width: 100%;
        }

        .cat_box:hover img {
            scale: 1.1;
            transition: all linear .3s;
            bottom: 0;
            left: 0;
        }

        .cat_box .detail-box {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: auto;
            left: 0px;
            background: linear-gradient(0deg, rgb(59, 59, 59), transparent);
            /* border: 1px solid; */
            vertical-align: middle;
            display: flex;
            flex-direction: column;
            justify-content: center;
            justify-content: start;
            align-items: center;
        }

        .cat_box .detail-box {
            color: white !important;
            font-size: 14px !important;
            font-weight: bold;
        }

        .fa_icon {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 18px;
            height: 25px;
            width: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--brand-secondary);
            border-radius: 50%;
        }
    </style>

    <div @class([" px-2 mb-2 cat_box", 'shadow'=> $active])>
        <div class="cat_box border">
            {{-- <a href="{{route('product.by.catgory', ['id' =>$cat->id, 'name' => Str::slug( $cat->name)])}}"
                class=""> --}}
                <a wire:navigate href="{{route('category.products', ['cat' => $cat->name])}}">
                    <img src="{{ asset('storage/' . $cat->image) }}">


                    {{-- <i class="fas fa-caret-up text-white fa_icon"></i> --}}

                    <div class="detail-box">
                        <div class="w-full px-3 py-1 bold bg_primary text-center text-light product-title">
                            {{$cat->name}}
                        </div>

                    </div>
                </a>

        </div>
    </div>

</div>