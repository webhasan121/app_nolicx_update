import NavLink from "../NavLink";
import ProductsLoop from "../client/ProductsLoop";

export default function TodaysProducts({ products = [] }) {
    return (
        <div className="pb-6">
            {/* Header */}
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">Today's</h2>
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Unde nam voluptatem est saepe, esse, facilis fuga adipisci temporibus cupiditate eum minus exercitationem repudiandae libero mollitia sapiente! Eligendi perferendis unde beatae.
                <NavLink
                    href={`${route("products.index")}?tag=today`}
                    className="px-3 py-2 rounded hover:text-indigo-600"
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
