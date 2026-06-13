import NavLink from "../NavLink";
import ProductsLoop from "../client/ProductsLoop";
import useTranslation from "../../hooks/useTranslation";

export default function GroceryProducts({ products = [] }) {
    const { t } = useTranslation();

    if (!products.length) return null;

    return (
        <div className="pb-6">
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">{t("Grocery Item")}</h2>
                <NavLink
                    href={route("category.products", { cat: "grocery-item" })}
                    className="px-3 py-2 rounded hover:text-indigo-600"
                >
                    {t("View All")}
                </NavLink>
            </div>

            <div className="transition-all duration-300 product_section">
                <ProductsLoop products={products} />
            </div>
        </div>
    );
}
