import { Link } from "@inertiajs/react";
import ProductsLoop from "../client/ProductsLoop";

export default function NewProduct({ products = [] }) {
    if (!products.length) return null;

    return (
        <div className="pb-6">
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">New</h2>

                <Link
                    href="/products?badge=new"
                    className="px-3 py-2 rounded hover:text-indigo-600"
                >
                    View All
                </Link>
            </div>
            <div className="pb-4 transition-all duration-300 product_section">
                <ProductsLoop products={products} />
            </div>
        </div>
    );
}
