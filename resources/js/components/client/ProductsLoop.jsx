import ProductCard from "../home/ProductCard";

export default function ProductsLoop({
    products = [],
    showSaveForLater = false,
    savedForLater = false,
    onSaveForLaterChange = null,
    gridClassName = "grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7",
}) {
    if (!products.length) return null;

    return (
        <div>
            <div className={gridClassName}>
                {products.map((product) => (
                    <div key={product.id} className="min-w-0">
                        <ProductCard
                            product={product}
                            showSaveForLater={showSaveForLater}
                            savedForLater={savedForLater}
                            onSaveForLaterChange={onSaveForLaterChange}
                        />
                    </div>
                ))}
            </div>
            <hr className="my-2" />
        </div>
    );
}
