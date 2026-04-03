import { Link } from "@inertiajs/react";
import axios from "axios";

export default function ProductCard({ product }) {
    const hasOffer = product.offer_type && product.discount;

    const discountPercentage = hasOffer
        ? Math.round(((product.price - product.discount) / product.price) * 100)
        : null;

    const isSoldOut = product.unit < 2;


    const addToCart = async () => {
        try {
            const response = await axios.post("/cart/add", {
                product_id: product.id,
            });

            alert(response.data.message);

            if (response.data.cartCount !== undefined) {
                router.reload({ only: ["auth"] });
            }
        } catch (error) {
            if (error.response?.status === 401) {
                alert("Login to add cart");
            }
        }
    };

    return (
        <div className="relative overflow-hidden bg-white border box group">
            {/* Discount Badge */}
            {hasOffer && (
                <div className="absolute top-0 left-0 z-10 px-2 py-1 text-xs text-white discount-badge bg_primary">
                    {discountPercentage}%
                </div>
            )}

            {/* Hover Option Container */}
            <div className="absolute inset-0 hidden transition-opacity opacity-0 option_container lg:block bg-orange-100/40 group-hover:opacity-100">
                <div className="flex flex-col items-center justify-between w-full h-full">
                    <div className="flex flex-col justify-center flex-1 w-full text-center">
                        <button
                            onClick={addToCart}
                            className="w-full p-2 mb-4 text-sm bg-white"
                        >
                            <i className="mx-2 fas fa-cart-plus"></i>
                            To Cart
                        </button>

                        <Link
                            href={`/product/${product.id}/${product.slug}`}
                            className="text-xs"
                        >
                            View Details
                            <i className="mx-2 fas fa-arrow-right"></i>
                        </Link>
                    </div>

                    <Link
                        href={`/product/order/${product.id}/${product.slug}`}
                        className="flex items-center justify-center w-full py-2 font-bold text-center bg-white text_primary"
                    >
                        Order Now
                        <i className="mx-2 fas fa-arrow-right"></i>
                    </Link>
                </div>
            </div>

            {/* Image */}
            <div className="overflow-hidden img-box">
                <img
                    src={`/storage/${product.thumbnail}`}
                    className="object-cover w-full h-40 transition-transform duration-300 group-hover:scale-125"
                    alt={product.title}
                />
            </div>

            {/* Card Body */}
            <div className="flex flex-col justify-between p-2 h-28">
                {/* Title + Unit */}
                <div className="flex items-start justify-between space-x-1 text-white">
                    <Link
                        href={`/product/${product.id}/${product.slug}`}
                        className="block w-full p-1 text-xs text-white truncate bg_primary"
                    >
                        {product.title.length > 15
                            ? product.title.substring(0, 15) + "..."
                            : product.title}
                    </Link>

                    <div className="p-1 text-xs bg_primary">
                        {product.unit ?? 0}
                    </div>
                </div>

                {/* Price Section */}
                <div className="flex items-center justify-between py-1 text-sm font-bold">
                    {hasOffer ? (
                        <>
                            <span className="text-md">
                                {product.discount} TK
                            </span>

                            <span className="text-xs">
                                <del>MRP {product.price} TK</del>
                            </span>
                        </>
                    ) : (
                        <span>{product.price} TK</span>
                    )}
                </div>

                {/* Order Button */}
                <Link
                    href={`/product/order/${product.id}/${product.slug}`}
                    className="flex items-center justify-center block text-sm font-bold text-center transition bg-white text_primary hover:bg_primary hover:text-white"
                >
                    <i className="mr-2 fas fa-cart-plus"></i>
                    Order Now
                </Link>
            </div>

            {/* Sold Out Overlay */}
            {isSoldOut && (
                <div className="absolute top-0 left-0 z-20 flex items-center justify-center w-full h-full bg-black/30">
                    <div className="w-full py-1 text-sm font-bold text-center uppercase bg-white">
                        Sold Out
                    </div>
                </div>
            )}
        </div>
    );
}
