import ProductCard from "../home/ProductCard";

export default function ProductsLoop({
    products = [],
    showSaveForLater = false,
    savedForLater = false,
    onSaveForLaterChange = null,
}) {
    if (!products.length) return null;

    return (
        <div>
            <div
                className=""
                style={{
                    display: "grid",
                    justifyContent: "center",
                    gridTemplateColumns: "repeat(auto-fill, minmax(160px, 1fr))",
                    gridGap: "10px",
                }}
            >
                {products.map((product) => (
                    <div key={product.id} className="">
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
