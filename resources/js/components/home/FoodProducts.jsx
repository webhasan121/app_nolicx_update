import NavLink from "../NavLink";
import ProductsLoop from "../client/ProductsLoop";

export default function FoodProducts({ products = [] }) {
    if (!products.length) return null;

    return (
        <div className="pb-6">
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">Food Items</h2>
                <NavLink
                    href={route("category.products", { cat: "food-items" })}
                    className="px-3 py-2 rounded hover:text-indigo-600"
                >
                    View All
                </NavLink>
            </div>

            <div className="transition-all duration-300 product_section">
                <ProductsLoop products={products} />
            </div>
        </div>
    );
}
