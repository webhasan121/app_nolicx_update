import { Link } from "@inertiajs/react";
import ProductsLoop from "../client/ProductsLoop";

export default function RecommendedProducts({ products = [] }) {
    if (!products.length) return null;

    return (
        <div className="pb-6">
            {/* Header */}
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">
                    For You
                </h2>
            </div>

            <div className="transition-all duration-300 product_section">
                <ProductsLoop products={products} />
            </div>
        </div>
    );
}
