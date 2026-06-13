import { useEffect, useState } from "react";
import ProductsLoop from "../client/ProductsLoop";
import useTranslation from "../../hooks/useTranslation";

export default function RecommendedProducts({
    products = [],
    onSaveForLaterChange = null,
}) {
    const { t } = useTranslation();
    const [displayedProducts, setDisplayedProducts] = useState(products);

    useEffect(() => {
        setDisplayedProducts(products);
    }, [products]);

    if (!displayedProducts.length) return null;

    const handleSaveForLaterChange = (product, isSaved) => {
        setDisplayedProducts((items) => {
            if (isSaved) {
                return items.some((item) => item.id === product.id)
                    ? items
                    : [product, ...items];
            }

            return items.filter((item) => item.id !== product.id);
        });

        onSaveForLaterChange?.(product, isSaved);
    };

    return (
        <div className="pb-6">
            {/* Header */}
            <div className="flex items-center justify-between px-2 py-4">
                <h2 className="text-xl font-bold">
                    {t("For You")}
                </h2>
            </div>

            <div className="transition-all duration-300 product_section">
                <ProductsLoop
                    products={displayedProducts}
                    showSaveForLater
                    savedForLater
                    onSaveForLaterChange={handleSaveForLaterChange}
                />
            </div>
        </div>
    );
}
