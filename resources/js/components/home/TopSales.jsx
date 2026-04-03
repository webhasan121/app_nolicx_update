import ProductsLoop from "../client/ProductsLoop";

export default function TopSales({ products = [] }) {
    if (!products.length) return null;

    return (
        <div className="py-4">

            {/* Header */}
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">
                    Top Sales
                </h2>
            </div>

            {/* Products */}
            <ProductsLoop
                products={products}
                className="transition-all duration-300 product_section"
            />

        </div>
    );
}
