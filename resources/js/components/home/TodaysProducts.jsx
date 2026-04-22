import NavLink from "../NavLink";
import ProductsLoop from "../client/ProductsLoop";

export default function TodaysProducts({ products = [] }) {
    return (
        <div className="pb-6">
            {/* Header */}
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">Today's</h2>

                <NavLink
                    href={`${route("products.index")}?tag=today`}
                    className="border-b-0 px-3 py-2 rounded text-inherit hover:text-indigo-600 hover:border-transparent"
                >
                    View All
                </NavLink>
            </div>

            {products.length ? (
                <div className="pb-4 transition-all duration-300 product_section">
                    <ProductsLoop products={products} />
                </div>
            ) : (
                <hr className="mt-2 border-slate-200" />
            )}
        </div>
    );
}
