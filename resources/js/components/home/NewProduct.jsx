import NavLink from "../NavLink";
import ProductsLoop from "../client/ProductsLoop";

export default function NewProduct({ products = [] }) {
    if (!products.length) return null;

    return (
        <div className="pb-6">
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">New</h2>

                <NavLink
                    href={`${route("products.index")}?badge=new`}
                    className="border-b-0 px-3 py-2 rounded text-inherit hover:text-indigo-600 hover:border-transparent"
                >
                    View All
                </NavLink>
            </div>
            <div className="pb-4 transition-all duration-300 product_section">
                <ProductsLoop products={products} />
            </div>
        </div>
    );
}
